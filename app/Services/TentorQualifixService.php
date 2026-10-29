<?php

namespace App\Services;

use App\Models\QualifixTicket;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TentorQualifixService
{
    /**
     * Susun seluruh data halaman Qualix (Klinik PR) untuk tentor dari basis data.
     *
     * @return array{
     *     user: User|null,
     *     staffProfile: StaffProfile|null,
     *     stats: array<int, array<string, mixed>>,
     *     tabs: array<int, array<string, mixed>>,
     *     antrean: array<int, array<string, mixed>>,
     *     selected: array<string, mixed>|null,
     *     todayDate: string,
     * }
     */
    public function qualifix(?User $user): array
    {
        $staffProfile = $user !== null
            ? StaffProfile::where('user_id', $user->id)->first()
            : null;

        $tickets = $staffProfile !== null
            ? QualifixTicket::query()
                ->whereHas('staff', fn ($q) => $q->where('staff_profiles.id', $staffProfile->id))
                ->with(['student', 'subject'])
                ->orderByDesc('submitted_at')
                ->get()
            : collect();

        $menunggu = $tickets->where('status', 'menunggu_tentor');
        $selesai = $tickets->where('status', 'selesai');
        $selesaiMingguIni = $this->countSelesaiMingguIni($selesai);

        $stats = $this->statCards($menunggu->count(), $selesaiMingguIni);
        $tabs = $this->tabs($tickets->count(), $menunggu->count(), $selesai->count());
        $antrean = $tickets->map(fn (QualifixTicket $ticket) => $this->kartuAntrean($ticket))->all();

        return [
            'user' => $user,
            'staffProfile' => $staffProfile,
            'stats' => $stats,
            'tabs' => $tabs,
            'antrean' => $antrean,
            'selected' => $antrean[0] ?? null,
            'todayDate' => now()->locale('id')->translatedFormat('l, j F Y'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function statCards(int $menunggu, int $selesaiMingguIni): array
    {
        return [
            [
                'Pertanyaan Menunggu',
                (string) $menunggu,
                'Pertanyaan',
                'Antrean menunggu respons tanggapan Anda',
                'clock',
                'text-ember-600',
                $menunggu > 0 ? 'bg-ember-50 text-ember-600' : 'bg-bark-50 text-bark-500',
            ],
            [
                'Terkoreksi Pekan Ini',
                (string) $selesaiMingguIni,
                'Soal',
                'Terkoreksi & diselesaikan',
                'check-circle',
                'text-emerald-600',
                'bg-emerald-50 text-emerald-600',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function tabs(int $total, int $menunggu, int $selesai): array
    {
        return [
            ['Semua', $total, false],
            ['Menunggu Respons', $menunggu, true],
            ['Selesai', $selesai, false],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    private function kartuAntrean(QualifixTicket $ticket): array
    {
        $student = $ticket->student;
        $subject = $ticket->subject;
        $waktu = $ticket->submitted_at?->diffForHumans() ?? 'baru saja';

        return [
            $this->initials($student?->full_name ?? 'Siswa'),        // 0: initial
            $student?->full_name ?? 'Siswa',                         // 1: nama
            $ticket->status === 'menunggu_tentor' ? 'Menunggu' : 'Selesai', // 2: status
            ($student?->class_group_id !== null ? 'Kelas '.$student->class_group_id : '-').' • '.$waktu, // 3: meta
            $ticket->title,                                          // 4: topik
            $ticket->question,                                       // 5: preview
            [$subject?->name ?? 'Mata Pelajaran'],                   // 6: tags
            $ticket->status === 'menunggu_tentor',                   // 7: active
        ];
    }

    /**
     * Hitung tiket yang status selesai dan diselesaikan minggu ini.
     */
    private function countSelesaiMingguIni(Collection $selesai): int
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        return $selesai->filter(fn (QualifixTicket $ticket) => $ticket->resolved_at !== null &&
            $ticket->resolved_at->between($weekStart, $weekEnd)
        )->count();
    }

    private function initials(string $name): string
    {
        return Str::of($name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }
}
