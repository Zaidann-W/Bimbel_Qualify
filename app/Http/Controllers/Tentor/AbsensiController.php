<?php

namespace App\Http\Controllers\Tentor;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\TutorAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    /**
     * Toleransi keterlambatan check-in dalam menit.
     */
    private const TOLERANSI_TELAT = 15;

    /**
     * Rentang default daftar sesi, dalam hari sebelum dan sesudah hari ini.
     */
    private const RENTANG_HARIAN = 7;

    public function index(Request $request): View
    {
        $user = $request->user();
        $staffProfile = StaffProfile::where('user_id', $user->id)->first();

        if (! $staffProfile) {
            return view('tentor.absensi', [
                'sessions' => collect(),
                'students' => collect(),
                'attendances' => collect(),
                'session' => null,
                'staffProfile' => null,
                'kehadiran' => null,
                'dari' => Carbon::today()->subDays(self::RENTANG_HARIAN),
                'sampai' => Carbon::today()->addDays(self::RENTANG_HARIAN),
                'barisSesi' => [],
            ]);
        }

        [$dari, $sampai] = $this->rentangDariRequest($request);

        // Sesi milik tentor ini pada rentang tanggal aktif.
        $sessions = ClassSession::with(['classGroup', 'subject', 'room', 'studyShift', 'tutorAttendance'])
            ->where('staff_id', $staffProfile->id)
            ->whereBetween('session_date', [$dari->toDateString(), $sampai->toDateString()])
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        // Sesi yang dipilih tetap bisa dibuka walau di luar rentang aktif,
        // selama memang milik tentor ini.
        $session = null;
        $selectedSessionId = $request->query('session_id');

        if (is_string($selectedSessionId) && $selectedSessionId !== '') {
            $session = ClassSession::with(['classGroup', 'subject', 'room', 'studyShift', 'tutorAttendance'])
                ->where('staff_id', $staffProfile->id)
                ->find($selectedSessionId);
        }

        $session ??= $sessions->first();

        $students = collect();
        $attendances = collect();

        if ($session) {
            // Ambil siswa yang terdaftar di class group ini
            $studentIds = Enrollment::where('class_group_id', $session->class_group_id)
                ->where('status', 'aktif')
                ->pluck('student_id');

            $students = Student::whereIn('id', $studentIds)
                ->orderBy('full_name')
                ->get(['id', 'full_name', 'nis', 'nisn', 'school_name']);

            // Ambil absensi yang sudah ada untuk sesi ini
            $attendances = StudentAttendance::where('class_session_id', $session->id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->keyBy('student_id');
        }

        return view('tentor.absensi', [
            'sessions' => $sessions,
            'barisSesi' => $this->barisSesi($sessions),
            'session' => $session,
            'students' => $students,
            'attendances' => $attendances,
            'staffProfile' => $staffProfile,
            'dari' => $dari,
            'sampai' => $sampai,
            'kehadiran' => $session ? $session->tutorAttendance()->where('staff_id', $staffProfile->id)->first() : null,
        ]);
    }

    /**
     * Rentang tanggal daftar sesi dari query string.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function rentangDariRequest(Request $request): array
    {
        $dari = $this->tanggalDariQuery($request, 'dari') ?? Carbon::today()->subDays(self::RENTANG_HARIAN);
        $sampai = $this->tanggalDariQuery($request, 'sampai') ?? Carbon::today()->addDays(self::RENTANG_HARIAN);

        if ($dari->greaterThan($sampai)) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        return [$dari->copy()->startOfDay(), $sampai->copy()->endOfDay()];
    }

    /**
     * Tanggal dari query string dengan format Y-m-d.
     */
    private function tanggalDariQuery(Request $request, string $key): ?Carbon
    {
        $nilai = $request->query($key);

        if (is_string($nilai) && Carbon::hasFormat($nilai, 'Y-m-d')) {
            return Carbon::parse($nilai);
        }

        return null;
    }

    /**
     * Baris daftar sesi beserta status kehadiran tentor.
     *
     * @param  Collection<int, ClassSession>  $sessions
     * @return array<int, array<string, mixed>>
     */
    private function barisSesi(Collection $sessions): array
    {
        return $sessions->map(function (ClassSession $sesi) {
            $kehadiran = $sesi->tutorAttendance->first();
            $tanggal = $sesi->tanggal;

            [$label, $tone] = match (true) {
                $kehadiran !== null && $kehadiran->check_in_at !== null => ['Check-in '.$kehadiran->check_in_at->format('H.i'), 'bg-emerald-50 text-emerald-700'],
                $tanggal->isToday() => ['Siap Check-in', 'bg-amber-50 text-amber-700'],
                $tanggal->isPast() => ['Tidak Check-in', 'bg-rose-50 text-rose-700'],
                default => ['Akan datang', 'bg-bark-100 text-bark-600'],
            };

            return [
                'id' => $sesi->id,
                'tanggal' => $tanggal,
                'label_tanggal' => $tanggal->locale('id')->translatedFormat('l, d/m/Y'),
                'waktu' => substr((string) $sesi->start_time, 0, 5).' – '.substr((string) $sesi->end_time, 0, 5),
                'mapel' => $sesi->subject?->name ?? $sesi->topic ?? 'Mata Pelajaran',
                'rombel' => $sesi->classGroup?->code ?? 'Kelas',
                'ruang' => $sesi->room?->name ?? 'Tanpa ruang',
                'badge' => $label,
                'tone' => $tone,
                'bisa_check_in' => $tanggal->isToday(),
            ];
        })->values()->all();
    }

    /**
     * Catat check-in tentor pada satu sesi.
     *
     * Status otomatis "hadir" untuk check-in tepat waktu dan "terlambat" bila
     * dilakukan melewati 15 menit dari jam mulai sesi.
     */
    public function checkIn(Request $request)
    {
        $staffProfile = $this->staffProfile($request);

        if (! $staffProfile) {
            return back()->withErrors(['error' => 'Profil tentor tidak ditemukan.']);
        }

        $validated = $request->validate([
            'session_id' => ['required', 'exists:class_sessions,id'],
        ]);

        $session = ClassSession::findOrFail($validated['session_id']);

        if ($session->staff_id !== $staffProfile->id) {
            return back()->withErrors(['error' => 'Sesi ini bukan milik Anda.']);
        }

        $mulai = Carbon::parse($session->session_date)->setTimeFromTimeString($session->start_time);
        $terlambat = now()->greaterThan($mulai->copy()->addMinutes(self::TOLERANSI_TELAT));

        TutorAttendance::updateOrCreate(
            [
                'class_session_id' => $session->id,
                'staff_id' => $staffProfile->id,
            ],
            [
                'check_in_at' => now(),
                'status' => $terlambat ? 'terlambat' : 'hadir',
            ]
        );

        return back()->with('success', 'Check-in tercatat pukul '.now()->format('H:i').($terlambat ? ' (terlambat).' : '.'));
    }

    /**
     * Catat checkout tentor pada satu sesi.
     */
    public function checkOut(Request $request)
    {
        $staffProfile = $this->staffProfile($request);

        if (! $staffProfile) {
            return back()->withErrors(['error' => 'Profil tentor tidak ditemukan.']);
        }

        $validated = $request->validate([
            'session_id' => ['required', 'exists:class_sessions,id'],
        ]);

        $session = ClassSession::findOrFail($validated['session_id']);

        if ($session->staff_id !== $staffProfile->id) {
            return back()->withErrors(['error' => 'Sesi ini bukan milik Anda.']);
        }

        TutorAttendance::updateOrCreate(
            [
                'class_session_id' => $session->id,
                'staff_id' => $staffProfile->id,
            ],
            [
                'check_out_at' => now(),
                'status' => 'hadir',
            ]
        );

        return back()->with('success', 'Checkout tercatat pukul '.now()->format('H:i').'.');
    }

    /**
     * Profil staf milik user yang sedang login.
     */
    private function staffProfile(Request $request): ?StaffProfile
    {
        return StaffProfile::where('user_id', $request->user()->id)->first();
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $staffProfile = StaffProfile::where('user_id', $user->id)->first();

        if (! $staffProfile) {
            return back()->withErrors(['error' => 'Profil tentor tidak ditemukan.']);
        }

        $validated = $request->validate([
            'session_id' => 'required|exists:class_sessions,id',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'attendances.*.arrived_at' => 'nullable|date_format:H:i',
            'attendances.*.note' => 'nullable|string',
        ]);

        $session = ClassSession::findOrFail($validated['session_id']);

        // Verify this session belongs to this tentor
        if ($session->staff_id !== $staffProfile->id) {
            return back()->withErrors(['error' => 'Anda tidak berhak menginput absensi untuk sesi ini.']);
        }

        foreach ($validated['attendances'] as $attendanceData) {
            $arrivedAt = null;
            if (! empty($attendanceData['arrived_at'])) {
                $arrivedAt = Carbon::parse($session->session_date)->setTimeFromTimeString($attendanceData['arrived_at']);
            }

            StudentAttendance::updateOrCreate(
                [
                    'class_session_id' => $session->id,
                    'student_id' => $attendanceData['student_id'],
                ],
                [
                    'status' => $attendanceData['status'],
                    'arrived_at' => $arrivedAt,
                    'note' => $attendanceData['note'] ?? null,
                    'recorded_by' => $user->id,
                ]
            );
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }
}
