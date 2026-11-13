<?php

namespace App\Services;

use App\Models\ClassGroup;
use App\Models\StudentAttendance;
use Illuminate\Support\Collection;

class AdminLaporanPresensiService
{
    /**
     * Label status kehadiran yang dipakai pada tampilan laporan.
     *
     * @var array<string, string>
     */
    private const STATUS_LABEL = [
        'hadir' => 'Hadir',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'alpa' => 'Alpha',
    ];

    public function rekap(?string $startDate = null, ?string $endDate = null, ?string $classGroupId = null, ?string $status = null): array
    {
        $statusFilter = $status ? strtolower($status) : null;

        $query = StudentAttendance::with(['student', 'classSession.classGroup'])
            ->when($startDate, fn ($q) => $q->whereDate('arrived_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('arrived_at', '<=', $endDate))
            ->when($classGroupId, fn ($q) => $q->whereHas('classSession', fn ($sq) => $sq->where('class_group_id', $classGroupId)))
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->orderBy('arrived_at', 'desc');

        $attendances = $query->get();

        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalAlpha = $attendances->where('status', 'alpa')->count();
        $total = $attendances->count();

        $data = $attendances->map(function ($attendance) {
            $classSession = $attendance->classSession;
            $classGroup = $classSession?->classGroup;
            $student = $attendance->student;

            return [
                'tanggal' => $attendance->arrived_at?->format('d/m/Y') ?? '-',
                'waktu' => $attendance->arrived_at?->format('H:i') ?? '-',
                'kelas' => $classGroup?->code ?? '-',
                'nama_siswa' => $student->full_name ?? '-',
                'nis' => $student->nis ?? '-',
                'status' => self::STATUS_LABEL[$attendance->status] ?? ucfirst((string) $attendance->status),
                'catatan' => $attendance->note ?? '-',
                'dicatat_oleh' => $attendance->recorded_by ?? '-',
            ];
        });

        return [
            'summary' => [
                'total' => (int) $total,
                'hadir' => (int) $totalHadir,
                'izin' => (int) $totalIzin,
                'sakit' => (int) $totalSakit,
                'alpa' => (int) $totalAlpha,
                'rate_hadir' => $total > 0 ? round($totalHadir / $total * 100, 1) : 0,
            ],
            'data' => $data->toArray(),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'class_group_id' => $classGroupId,
                'status' => $status,
            ],
        ];
    }

    public function exportData(?string $startDate = null, ?string $endDate = null, ?string $classGroupId = null, ?string $status = null): array
    {
        return $this->rekap($startDate, $endDate, $classGroupId, $status)['data'];
    }

    public function getClassGroups(): Collection
    {
        return ClassGroup::orderBy('code')->get();
    }
}
