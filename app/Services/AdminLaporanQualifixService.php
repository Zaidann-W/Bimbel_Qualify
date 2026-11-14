<?php

namespace App\Services;

use App\Models\QualifixTicket;
use Illuminate\Support\Collection;

class AdminLaporanQualifixService
{
    public function rekap(?string $startDate = null, ?string $endDate = null, ?string $status = null): array
    {
        $query = QualifixTicket::with(['student', 'subject', 'classGroup', 'staff'])

            ->when($startDate, fn ($q) => $q->whereDate('submitted_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('submitted_at', '<=', $endDate))
            ->when($status, fn ($q) => $q->where('status', $dbStatus[$status] ?? $status));

        $tiket = $query->get();

        $total = $tiket->count();
        $menunggu = $tiket->where('status', 'menunggu_tentor')->count();
        $dibahas = $tiket->where('status', 'sedang_dibahas')->count();
        $selesai = $tiket->where('status', 'selesai')->count();

        $data = $tiket->map(function ($ticket) {
            $student = $ticket->student;
            $subject = $ticket->subject;
            $classGroup = $ticket->classGroup;

            return [
                'tanggal' => $ticket->submitted_at?->format('d/m/Y H:i') ?? '-',
                'nis' => $student?->nis ?? '-',
                'nama_siswa' => $student?->full_name ?? '-',
                'kelas' => $classGroup?->code ?? '-',
                'mata_pelajaran' => $subject?->name ?? '-',
                'status' => ucfirst($ticket->status ?? '-'),
                'dicatat_oleh' => $ticket->staff?->name ?? '-',
            ];
        })->toArray();

        return [
            'summary' => [
                'total' => (int) $total,
                'menunggu' => (int) $menunggu,
                'dibahas' => (int) $dibahas,
                'selesai' => (int) $selesai,
                'rate_menunggu' => $total > 0 ? round($menunggu / $total * 100, 1) : 0,
            ],
            'data' => $data,
        ];
    }

    public function exportData(?string $startDate = null, ?string $endDate = null, ?string $status = null): array
    {
        return $this->rekap($startDate, $endDate, $status)['data'];
    }
}