<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminLaporanPresensiService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanPresensiController extends Controller
{
    public function __construct(private readonly AdminLaporanPresensiService $service) {}

    /** 
     * Tampilkan halaman laporan presensi dengan data real dari DB.
     */
    public function index(Request $request): View
    {
        $data = $this->service->rekap(
            $request->query('start_date'),
            $request->query('end_date'),
            $request->query('class_group_id'),
            $request->query('status'),
        );
        $data['classGroups'] = $this->service->getClassGroups();
        $data['statusList'] = ['Hadir', 'Izin', 'Sakit', 'Alpha'];
        $data['request'] = $request;

        return view('admin.laporan-presensi', $data);
    }

    /** 
     * Ekspor laporan presensi ke CSV atau PDF.
     */
    public function export(Request $request): StreamedResponse
    {
        $format = $request->query('format', 'csv');
        $data = $this->service->exportData(
            $request->query('start_date'),
            $request->query('end_date'),
            $request->query('class_group_id'),
            $request->query('status'),
        );

        $filename = 'laporan-presensi-'.now()->format('Ymd-His').($format === 'pdf' ? '.pdf' : '.'.$format);

        if ($format === 'pdf') {
            return $this->exportAsPdf($data, $filename);
        }

        return response()->streamDownload(function () use ($data, $format): void {
            $out = fopen('php://output', 'w');

            if ($format === 'csv') {
                fputcsv($out, [
                    'Tanggal', 'Waktu', 'Kelas', 'Nama Siswa', 'NIS', 'Status Kehadiran',
                    'Catatan', 'Dicatat Oleh',
                ]);

                foreach ($data as $row) {
                    fputcsv($out, [
                        $row['tanggal'],
                        $row['waktu'],
                        $row['kelas'],
                        $row['nama_siswa'],
                        $row['nis'],
                        $row['status'],
                        $row['catatan'],
                        $row['dicatat_oleh'],
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
     * Ekspor laporan presensi sebagai PDF menggunakan DomPDF.
     */
    protected function exportAsPdf(array $rawData, string $filename): StreamedResponse
    {
        // Compute summaries from the raw data (same logic as rekap)
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpha = 0;
        $rows = [];

        foreach ($rawData as $row) {
            $status = strtolower($row['status'] ?? '');
            $rows[] = $row;

            if ($status === 'hadir') {
                $totalHadir++;
            } elseif ($status === 'izin') {
                $totalIzin++;
            } elseif ($status === 'sakit') {
                $totalSakit++;
            } elseif ($status === 'alpa') {
                $totalAlpha++;
            }
        }

        $total = count($rawData);
        $pdfData = [
            'summary' => [
                'total' => $total,
                'hadir' => $totalHadir,
                'izin' => $totalIzin,
                'sakit' => $totalSakit,
                'alpa' => $totalAlpha,
                'rate_hadir' => $total > 0 ? round($totalHadir / $total * 100, 1) : 0,
            ],
            'rows' => $rows,
        ];

        $pdf = PDF::loadView('admin.laporan-presensi-pdf', ['data' => $pdfData]);

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}