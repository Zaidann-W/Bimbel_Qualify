<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminLaporanKeuanganService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanKeuanganController extends Controller
{
    public function __construct(private readonly AdminLaporanKeuanganService $service) {}

    /**
     * Tampilkan halaman laporan keuangan & SPP dengan data real dari DB.
     */
    public function index(Request $request): View
    {
        $data = $this->service->rekap(
            $request->query('start_date'),
            $request->query('end_date'),
            $request->query('status'),
            $request->query('type'),
        );

        $data['statusList'] = ['belum_bayar', 'sebagian', 'lunas', 'batal'];
        $data['typeList'] = ['spp_bulanan', 'registrasi', 'formulir_tes', 'modul'];
        $data['request'] = $request;

        return view('admin.laporan-keuangan', $data);
    }

    /**
     * Ekspor laporan keuangan ke CSV (bisa dibuka Excel).
     */
    public function export(Request $request): StreamedResponse
    {
        $format = $request->query('format', 'csv');
        $data = $this->service->exportData(
            $request->query('start_date'),
            $request->query('end_date'),
            $request->query('status'),
            $request->query('type'),
        );

        $filename = 'laporan-keuangan-'.now()->format('Ymd-His').'.'.$format;

        return response()->streamDownload(function () use ($data, $format): void {
            $out = fopen('php://output', 'w');

            if ($format === 'csv') {
                fputcsv($out, [
                    'No Invoice', 'Nama Siswa', 'NIS', 'Kelas', 'Jenis Tagihan', 'Deskripsi',
                    'Nominal', 'Status', 'Tgl Jatuh Tempo', 'Tgl Bayar', 'Metode', 'Diverifikasi Oleh',
                ]);

                foreach ($data as $row) {
                    fputcsv($out, [
                        $row['invoice_number'],
                        $row['student_name'],
                        $row['nis'],
                        $row['kelas'],
                        $row['type'],
                        $row['description'],
                        $row['amount'],
                        $row['status'],
                        $row['due_date'],
                        $row['paid_at'],
                        $row['payment_method'],
                        $row['verified_by'],
                    ]);
                }
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
