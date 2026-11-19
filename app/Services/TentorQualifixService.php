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
     *     activeTab: string,
     *     antrean: array<int, array<string, mixed>>,
     *     tickets: Collection<int, QualifixTicket>,
     *     selectedTicket: QualifixTicket|null,
     *     selected: array<string, mixed>|null,
     *     todayDate: string,
     * }
     */
    public function qualifix(?User $user, string $activeTab = 'semua', ?int $selectedTicketId = null): array
    {
        $staffProfile = $user !== null
            ? StaffProfile::where('user_id', $user->id)->first()
            : null;

        // Requirement 3: Semua pertanyaan muncul (bukan hanya pertanyaan yang sesuai Mapel tentor)
        $tickets = QualifixTicket::query()
            ->with([
                'student',
                'subject',
                'classGroup',
                'staff',
                'attachments.file',
                'answers.staff.user',
                'answers.attachments.file',
            ])
            ->orderByDesc('submitted_at')
            ->get();

        $menungguCount = $tickets->where('status', 'menunggu_tentor')->count();
        $sudahDijawabCount = $tickets->where('status', 'sedang_dibahas')->count();
        $selesaiCount = $tickets->where('status', 'selesai')->count();
        $totalCount = $tickets->count();
        $selesaiMingguIni = $this->countSelesaiMingguIni($tickets->where('status', 'selesai'));

        $activeTab = in_array($activeTab, ['semua', 'menunggu', 'sudah_dijawab', 'selesai'], true) ? $activeTab : 'semua';

        $filteredTickets = match ($activeTab) {
            'menunggu' => $tickets->where('status', 'menunggu_tentor')->values(),
            'sudah_dijawab' => $tickets->where('status', 'sedang_dibahas')->values(),
            'selesai' => $tickets->where('status', 'selesai')->values(),
            default => $tickets->values(),
        };

        // Memilih tiket yang dipencet atau tiket pertama dari daftar terfilter
        $selectedTicket = null;
        if ($selectedTicketId !== null) {
            $selectedTicket = $filteredTickets->firstWhere('id', $selectedTicketId);
        }
        $selectedTicket ??= $filteredTickets->first();

        $stats = $this->statCards($menungguCount, $selesaiMingguIni);
        $tabs = $this->tabs($totalCount, $menungguCount, $sudahDijawabCount, $selesaiCount, $activeTab);

        $antrean = $filteredTickets->map(function (QualifixTicket $ticket) use ($selectedTicket) {
            return $this->kartuAntrean($ticket, $selectedTicket && $selectedTicket->id === $ticket->id);
        })->all();

        return [
            'user' => $user,
            'staffProfile' => $staffProfile,
            'stats' => $stats,
            'tabs' => $tabs,
            'activeTab' => $activeTab,
            'antrean' => $antrean,
            'tickets' => $tickets,
            'totalCount' => $totalCount,
            'selectedTicket' => $selectedTicket,
            'selected' => $selectedTicket ? $this->kartuAntrean($selectedTicket, true) : null,
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
                'Antrean menunggu respons tanggapan',
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
    private function tabs(int $total, int $menunggu, int $sudahDijawab, int $selesai, string $activeTab): array
    {
        return [
            [
                0 => 'Semua',
                1 => $total,
                2 => $activeTab === 'semua',
                'key' => 'semua',
                'label' => 'Semua',
                'count' => $total,
                'active' => $activeTab === 'semua',
            ],
            [
                0 => 'Menunggu Respons',
                1 => $menunggu,
                2 => $activeTab === 'menunggu',
                'key' => 'menunggu',
                'label' => 'Menunggu Respons',
                'count' => $menunggu,
                'active' => $activeTab === 'menunggu',
            ],
            [
                0 => 'Sudah Dijawab',
                1 => $sudahDijawab,
                2 => $activeTab === 'sudah_dijawab',
                'key' => 'sudah_dijawab',
                'label' => 'Sudah Dijawab',
                'count' => $sudahDijawab,
                'active' => $activeTab === 'sudah_dijawab',
            ],
            [
                0 => 'Selesai',
                1 => $selesai,
                2 => $activeTab === 'selesai',
                'key' => 'selesai',
                'label' => 'Selesai',
                'count' => $selesai,
                'active' => $activeTab === 'selesai',
            ],
        ];
    }

    /**
     * @return array<string|int, mixed>
     */
    private function kartuAntrean(QualifixTicket $ticket, bool $isSelected): array
    {
        $student = $ticket->student;
        $subject = $ticket->subject;
        $waktu = $ticket->submitted_at?->diffForHumans() ?? 'baru saja';
        $hasAnswers = $ticket->answers->isNotEmpty();

        $latestAnswer = $ticket->answers->sortByDesc('sent_at')->first();
        $pemberiJawaban = $latestAnswer?->staff?->full_name_with_title
            ?? $latestAnswer?->staff?->user?->name
            ?? null;

        $statusLabel = match ($ticket->status) {
            'selesai' => 'Selesai',
            'sedang_dibahas' => 'Sudah Dijawab',
            default => 'Menunggu',
        };

        $isMenunggu = $ticket->status === 'menunggu_tentor';
        $isSudahDijawab = $ticket->status === 'sedang_dibahas';
        $isSelesai = $ticket->status === 'selesai';

        $kelasLabel = $ticket->classGroup?->name
            ?? $ticket->classGroup?->code
            ?? ($student?->school_grade ? 'Kelas '.$student->school_grade : 'Siswa');
        $metaText = $kelasLabel.' • '.$waktu;

        return [
            0 => $this->initials($student?->full_name ?? 'Siswa'),
            1 => $student?->full_name ?? 'Siswa',
            2 => $statusLabel,
            3 => $metaText,
            4 => $ticket->title,
            5 => $ticket->question,
            6 => [$subject?->name ?? 'Mata Pelajaran'],
            7 => $isSelected,
            8 => $ticket->id,
            9 => $ticket->ticket_number,
            'id' => $ticket->id,
            'initial' => $this->initials($student?->full_name ?? 'Siswa'),
            'nama' => $student?->full_name ?? 'Siswa',
            'status' => $statusLabel,
            'is_menunggu' => $isMenunggu,
            'is_sudah_dijawab' => $isSudahDijawab,
            'is_selesai' => $isSelesai,
            'meta' => $metaText,
            'topik' => $ticket->title,
            'preview' => $ticket->question,
            'tags' => [$subject?->name ?? 'Mata Pelajaran'],
            'active' => $isSelected,
            'ticket_number' => $ticket->ticket_number,
            'pemberi_jawaban' => $pemberiJawaban,
            'ticket' => $ticket,
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
