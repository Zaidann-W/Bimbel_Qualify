<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    /**
     * Tampilkan pusat keuangan, tagihan SPP, dan transaksi.
     */
    public function index(): View
    {
        $invoices = Invoice::with(['student', 'payments'])->latest()->limit(12)->get();

        $transaksi = $invoices->map(function ($invoice) {
            $payment = $invoice->payments->where('status', 'berhasil')->first();

            return [
                'waktu' => $payment?->paid_at?->format('H:i WIB') ?? $invoice->created_at?->format('H:i WIB') ?? '-',
                'keterangan' => $invoice->description ?? '-',
                'pihak' => $invoice->student->full_name ?? '-',
                'metode' => $payment?->payment_method ?? '-',
                'nominal' => (float) $invoice->amount,
                'arah' => 'masuk',
                'status' => $invoice->status,
                'invoice_number' => $invoice->invoice_number,
            ];
        });

        $tagihan = Invoice::with('student')
            ->where('status', '!=', 'lunas')
            ->where('status', '!=', 'batal')
            ->latest('due_date')
            ->limit(5)
            ->get()
            ->map(function ($invoice) {
                return [
                    'nama' => $invoice->student->full_name ?? '-',
                    'kelas' => trim((string) ($invoice->student->education_level ?? '').' '.($invoice->student->school_grade ?? '')),
                    'nominal' => (float) $invoice->amount,
                    'tempo' => $invoice->due_date?->diffForHumans() ?? '-',
                ];
            });

        $totalMasuk = $invoices->sum('amount');
        $totalKeluar = 0; // placeholder, akan diisi dari tabel pengeluaran jika tersedia
        $saldoBersih = $totalMasuk - $totalKeluar;

        return view('admin.keuangan', compact('transaksi', 'tagihan', 'totalMasuk', 'totalKeluar', 'saldoBersih'));
    }
}
