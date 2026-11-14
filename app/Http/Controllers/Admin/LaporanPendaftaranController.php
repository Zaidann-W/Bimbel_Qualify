<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminLaporanPendaftaranService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanPendaftaranController extends Controller
{
    public function __construct(private readonly AdminLaporanPendaftaranService $service) {}

    /** 
     * Tampilkan halaman laporan pendaftaran.
     */
    public function index(Request $request): View
    {
        $data = $this->service->rekap(
            $request->query('start_date'),
            $request->query('end_date'),
        );
        $data['classGroups'] = $this->service->getClassGroups();
        $data['request'] = $request;

        return view('admin.laporan-pendaftaran', $data);
    }

    /** 
     * Ekspor laporan pendaftaran ke CSV atau PDF.
     */
    public function export(Request $request): StreamedResponse
    {
        $format = $request->query('format', 'csv');
        $data = $this->service->exportData(
            $request->query('start_date'),
            $request->query('end_date'),
        );

        $filename = 'laporan-pendaftaran-'.now()->format('Ymd-His').($format === 'pdf' ? '.pdf' : '.'.$format);

        if ($format === 'pdf') {
            return $this->exportAsPdf($data, $filename);
        }

        return response()->streamDownload(function () use ($data, $format): void {
            $out = fopen('php://output', 'w');

            if ($format === 'csv') {
                fputcsv($out, [
                    'Tanggal', 'Nama Siswa', 'NIS', 'Kelas', 'Status',
                    'Dicatat Oleh', 'Tanggal Daftar',
                ]);

                foreach ($data as $row) {
                    fputcsv($out, [
                        $row['tanggal'] ?? '',
                        $row['nama_siswa'] ?? '',
                        $row['nis'] ?? '',
                        $row['kelas'] ?? '',
                        $row['status'] ?? '',
                        $row['dicatat_oleh'] ?? '',
                        $row['tanggal_daftar'] ?? '',
                    ]);
                }
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /** 
     * Ekspor laporan pendaftaran sebagai PDF menggunakan DomPDF.
     */
    protected function exportAsPdf(array $rawData, string $filename): StreamedResponse
    {
        // Compute summaries from the raw data
        $total = count($rawData);
        $baru = 0;
        $proses = 0;
        $selesai = 0;
        $batal = 0;
        $rows = [];

        foreach ($rawData as $row) {
            $status = $row['status'] ?? '';
            $rows[] = $row;

            if (strtolower($status) === 'baru') {
                $baru++;
            } elseif (strtolower($status) === 'proses') {
                $proses++;
            } elseif (strtolower($status) === 'selesai') {
                $selesai++;
            } elseif (strtolower($status) === 'batal') {
                $batal++;
            }
        }

        $pdfData = [
            'summary' => [
                'total' => $total,
                'baru' => $baru,
                'proses' => $proses,
                'selesai' => $selesai,
                'batal' => $batal,
                'rate_baru' => $total > 0 ? round($baru / $total * 100, 1) : 0,
            ],
            'rows' => $rows,
        ];

        $pdf = PDF::loadView('admin.laporan-pendaftaran-pdf', ['data' => $pdfData]);

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}