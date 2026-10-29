<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudySession;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SessionController extends Controller
{
    /**
     * Peta filter kategori mata pelajaran: kunci URL => kata kunci pencarian di kolom category_badge.
     *
     * Kata kunci dicocokkan sebagian (LIKE) agar label seperti "Matematika Saintek" terekam.
     *
     * @var array<string, string>
     */
    private const CATEGORY_FILTERS = [
        'matematika' => 'Matematika',
        'fisika' => 'Fisika',
        'kimia' => 'Kimia',
        'biologi' => 'Biologi',
        'tps' => 'TPS',
    ];

    /**
     * Tampilkan daftar jadwal belajar siswa beserta statistik pekan berjalan.
     */
    public function schedule(Request $request): View
    {
        $categoryKey = $this->resolvedCategoryKey($request);
        $weekFilter = $this->resolvedWeekFilter($request);

        [$weekStart, $weekEnd] = $this->currentWeekRange();

        $sessions = StudySession::query()
            ->when(
                $categoryKey !== null,
                fn (Builder $query) => $query
                    ->where('category_badge', 'like', '%'.self::CATEGORY_FILTERS[$categoryKey].'%'),
            )
            ->when($weekFilter === 'minggu-ini', fn (Builder $query) => $query->whereBetween('date', [$weekStart, $weekEnd]))
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Statistik header selalu dihitung dari seluruh sesi pekan ini, tidak terpengaruh filter kategori.
        $weekSessions = StudySession::query()
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $runningSessions = $weekSessions
            ->filter(fn (StudySession $session): bool => $session->isRunning())
            ->values();

        return view('siswa.detail-pertemuan', [
            'sessions' => $sessions,
            'categoryTabs' => $this->categoryTabs(),
            'categoryKey' => $categoryKey,
            'weekFilter' => $weekFilter,
            'weekLabel' => $this->weekRangeLabel($weekStart),
            'runningSession' => $runningSessions->first(),
            'runningCount' => $runningSessions->count(),
            'weekTotal' => $weekSessions->count(),
            'mainRoom' => $this->mainRoom($weekSessions),
        ]);
    }

    /**
     * Tampilkan detail sesi belajar yang sedang berlangsung atau terdekat.
     */
    public function current(Request $request): View
    {
        $studySession = StudySession::query()
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->first()
            ?? StudySession::query()
                ->whereDate('date', '>', today())
                ->orderBy('date')
                ->orderBy('start_time')
                ->first()
            ?? StudySession::query()
                ->orderByDesc('date')
                ->orderByDesc('start_time')
                ->first();

        abort_if($studySession === null, 404, 'Belum ada sesi belajar yang tersedia.');

        return $this->renderDetail($studySession, $request);
    }

    /**
     * Tampilkan detail satu sesi belajar beserta materi dan presensi siswa.
     */
    public function show(Request $request, StudySession $studySession): View
    {
        return $this->renderDetail($studySession, $request);
    }

    private function renderDetail(StudySession $studySession, Request $request): View
    {
        $studySession->load([
            'materials' => fn ($query) => $query->orderBy('id'),
        ]);

        $student = $request->user()->student;

        $attendance = $student !== null
            ? $studySession->attendances()->whereBelongsTo($student)->first()
            : null;

        return view('siswa.detail-jadwal', [
            'studySession' => $studySession,
            'attendance' => $attendance,
        ]);
    }

    /**
     * Kunci kategori yang dipilih siswa; kunci tidak valid atau kosong dianggap "Semua Matpel".
     */
    private function resolvedCategoryKey(Request $request): ?string
    {
        $raw = $request->query('category');

        return is_string($raw) && array_key_exists($raw, self::CATEGORY_FILTERS) ? $raw : null;
    }

    /**
     * Rentang minggu yang dipilih siswa; default "minggu-ini".
     */
    private function resolvedWeekFilter(Request $request): string
    {
        $raw = $request->query('week');

        return is_string($raw) && in_array($raw, ['minggu-ini', 'semua'], true) ? $raw : 'minggu-ini';
    }

    /**
     * Rentang minggu berjalan (Senin 00.00 sampai Minggu 23.59).
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function currentWeekRange(): array
    {
        $weekStart = now()->startOfWeek(CarbonInterface::MONDAY)->startOfDay();

        return [$weekStart, $weekStart->copy()->endOfWeek(CarbonInterface::SUNDAY)->endOfDay()];
    }

    /**
     * Tab filter kategori mata pelajaran, diawali opsi "Semua Matpel".
     *
     * @return array<int, array{key: ?string, label: string}>
     */
    private function categoryTabs(): array
    {
        $tabs = [
            ['key' => null, 'label' => 'Semua Matpel'],
        ];

        foreach (self::CATEGORY_FILTERS as $key => $keyword) {
            $tabs[] = [
                'key' => $key,
                'label' => $key === 'tps' ? 'TPS Penalaran' : $keyword,
            ];
        }

        return $tabs;
    }

    /**
     * Label rentang minggu berjalan dalam format Indonesia, misalnya "10 – 16 Maret 2025".
     */
    private function weekRangeLabel(Carbon $weekStart): string
    {
        $end = $weekStart->copy()->endOfWeek(CarbonInterface::SUNDAY);

        if ($weekStart->isSameMonth($end)) {
            return $weekStart->translatedFormat('j').' – '.$end->translatedFormat('j F Y');
        }

        return $weekStart->translatedFormat('j F Y').' – '.$end->translatedFormat('j F Y');
    }

    /**
     * Ruangan yang paling sering dipakai selama pekan ini beserta kapasitasnya.
     *
     * @param  Collection<int, StudySession>  $weekSessions
     * @return array{name: string, floor: ?string, capacity: ?int}|null
     */
    private function mainRoom(Collection $weekSessions): ?array
    {
        if ($weekSessions->isEmpty()) {
            return null;
        }

        $rooms = $weekSessions->groupBy(fn (StudySession $session): string => $session->roomBaseName);

        $roomName = $rooms
            ->sortByDesc(fn (Collection $sessionsOfRoom): int => $sessionsOfRoom->count())
            ->keys()
            ->first();

        $sample = $rooms->get($roomName)->first();

        return [
            'name' => $roomName,
            'floor' => $sample->roomFloorLabel,
            'capacity' => $sample->room_capacity,
        ];
    }
}
