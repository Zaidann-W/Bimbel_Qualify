<?php

namespace App\Services;

use App\Models\Invoice;

class AdminLaporanKeuanganService
{
    public function rekap(?string $startDate = null, ?string $endDate = null, ?string $status = null, ?string $type = null): array
    {
        $query = Invoice::with(['student', 'academicPeriod', 'payments.verifier'])
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderBy('created_at', 'desc');

        $invoices = $query->get();

        $totalTagihan = $invoices->sum('amount');
        $totalTerbayar = $invoices->sum(function ($invoice) {
            return $invoice->payments->where('status', 'berhasil')->sum('amount');
        });
        $totalBelumBayar = $invoices->where('status', 'belum_bayar')->sum('amount');
        $totalLunas = $invoices->where('status', 'lunas')->count();

        $data = $invoices->map(function ($invoice) {
            $payment = $invoice->payments->where('status', 'berhasil')->first();
            $student = $invoice->student;

            return [
                'invoice_number' => $invoice->invoice_number,
                'student_name' => $student->full_name ?? '-',
                'nis' => $student->nis ?? '-',
                'kelas' => trim((string) ($student->education_level ?? '').' '.($student->school_grade ?? '').' '.($student->major ?? '-')),
                'type' => $invoice->type,
                'description' => $invoice->description ?? '-',
                'amount' => (float) $invoice->amount,
                'status' => $invoice->status,
                'due_date' => $invoice->due_date?->format('d/m/Y') ?? '-',
                'paid_at' => $payment?->paid_at?->format('d/m/Y H:i') ?? '-',
                'payment_method' => $payment?->payment_method ?? '-',
                'verified_by' => $payment?->verifier?->name ?? '-',
            ];
        });

        return [
            'summary' => [
                'total_tagihan' => (float) $totalTagihan,
                'total_terbayar' => (float) $totalTerbayar,
                'total_belum_bayar' => (float) $totalBelumBayar,
                'total_lunas' => (int) $totalLunas,
                'total_invoice' => (int) $invoices->count(),
            ],
            'data' => $data->toArray(),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'type' => $type,
            ],
        ];
    }

    public function exportData(?string $startDate = null, ?string $endDate = null, ?string $status = null, ?string $type = null): array
    {
        return $this->rekap($startDate, $endDate, $status, $type)['data'];
    }
}
