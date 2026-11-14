<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Collection;

class AdminLaporanPendaftaranService
{
    public function rekap(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Application::with(['user', 'statusHistory'])

            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate))
            ->orderBy('created_at', 'desc');

        $applications = $query->get();

        $total = $applications->count();
        $baru = $applications->where('status', 'baru')->count();
        $proses = $applications->where('status', 'proses')->count();
        $selesai = $applications->where('status', 'selesai')->count();
        $batal = $applications->where('status', 'batal')->count();

        $data = $applications->map(function ($application) {
            $user = $application->user;
            $status = $application->status ?? 'baru';

            return [
                'tanggal' => $application->created_at?->format('d/m/Y H:i') ?? '-',
                'nama_siswa' => $user?->full_name ?? '-',
                'nis' => $user?->nis ?? '-',
                'kelas' => $user?->education_level.' '.$user?->school_grade.' '.$user?->major ?? '-',
                'status' => ucfirst($status),
                'dicatat_oleh' => $application->statusHistory?->last()?->created_by ?? '-',
                'tanggal_daftar' => $application->created_at?->format('d/m/Y') ?? '-',
            ];
        })->toArray();

        return [
            'summary' => [
                'total' => (int) $total,
                'baru' => (int) $baru,
                'proses' => (int) $proses,
                'selesai' => (int) $selesai,
                'batal' => (int) $batal,
                'rate_baru' => $total > 0 ? round($baru / $total * 100, 1) : 0,
            ],
            'data' => $data,
        ];
    }

    public function exportData(?string $startDate = null, ?string $endDate = null): array
    {
        return $this->rekap($startDate, $endDate)['data'];
    }
}