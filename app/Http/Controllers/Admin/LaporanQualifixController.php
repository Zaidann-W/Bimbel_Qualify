<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminLaporanQualifixService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanQualifixController extends Controller
{
    public function __construct(private readonly AdminLaporanQualifixService $service) {}

    /** 
     * Tampilkan halaman laporan qualifix.
     */
    public function index(Request $request): View
    {
        $data = $this->service->rekap(
            $request->query('start_date'),
            $request->query('end_date'),
            $request->query('status'),
        );
        $data['statusList'] = ['Menunggu', 'Dibahas', 'Selesai'];
        $data['request'] = $request;

        return view('admin.laporan-qualifix', $data);
    }

    /** 
     * Ekspor laporan qualifix ke CSV atau PDF.
     */
    public function export(Request $request): StreamedResponse
    {
        $format = $request->query('format', 'csv');
        $data = $this->service->exportData(
            $request->query('start_date'),
            $request->query('end_date'),
            $request->query('status'),
        );

        $filename = 'laporan-qualifix-'.now()->format('Ymd-His').($format === 'pdf' ? '.pdf' : '.'.$format);

        if ($format === 'pdf') {
            return $this->exportAsPdf($data, $filename);
        }

        return response()->streamDownload(function () use ($data, $format): void {
            $out = fopen('php://output', 'w');

            if ($format === 'csv') {
                fputcsv($out, [
                    'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Mata Pelajaran',
                    'Status', 'Dicatat Oleh',
                ]);

                foreach ($data as $row) {
                    fputcsv($out, [
                        $row['tanggal'] ?? '',
                        $row['nis'] ?? '',
                        $row['nama_siswa'] ?? '',
                        $row['kelas'] ?? '',
                        $row['mata_pelajaran'] ?? '',
                        $row['status'] ?? '',
                        $row['dicatat_oleh'] ?? '',
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
     * Ekspor laporan qualifix sebagai PDF menggunakan DomPDF.
     */
    protected function exportAsPdf(array $rawData, string $filename): StreamedResponse
    {
        // Compute summaries from the raw data
        $total = count($rawData);
        $menunggu = 0;
        $dibahas = 0;
        $selesai = 0;
        $rows = [];

        foreach ($rawData as $row) {
            $status = $row['status'] ?? '';
            $rows[] = $row;

            if (strtolower($status) === 'menunggu') {
                $menunggu++;
            } elseif (strtolower($status) === 'dibahas') {
                $dibahas++;
            } elseif (strtolower($status) === 'selesai') {
                $selesai++;
            }
        }

        $pdfData = [
            'summary' => [
                'total' => $total,
                'menunggu' => $menunggu,
                'dibahas' => $dibahas,
                'selesai' => $selesai,
                'rate_menunggu' => $total > 0 ? round($menunggu / $total * 100, 1) : 0,
            ],
            'rows' => $rows,
        ];

        $pdf = PDF::loadView('admin.laporan-qualifix-pdf', ['data' => $pdfData]);

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}