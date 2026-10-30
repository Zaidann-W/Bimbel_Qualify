<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\OwnerLaporanAbsensiService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanAbsensiController extends Controller
{
    public function __construct(private readonly OwnerLaporanAbsensiService $service) {}

    public function index(Request $request): View|StreamedResponse
    {
        $periode = $request->query('periode');
        $jenjang = $request->query('jenjang');

        $data = $this->service->rekap($periode, $jenjang);

        if ($request->query('export') === 'csv') {
            return $this->exportCsv($data, $request->query('tab', 'jenjang'));
        }

        return view('owner.laporan-absensi', $data);
    }

    /**
     * @param  array{rekapJenjang: array<int, array<string, mixed>>, rekapTentor: array<int, array<string, mixed>>}  $data
     */
    private function exportCsv(array $data, string $tab): StreamedResponse
    {
        $isTentor = $tab === 'tentor';
        $rows = $isTentor ? $data['rekapTentor'] : $data['rekapJenjang'];

        $header = $isTentor
            ? ['Nama Tentor', 'Unit / Mata Ajar', 'Sesi Diampu', 'Hadir', 'Telat', 'Ketepatan', 'Status']
            : ['Kode', 'Kelas', 'Rincian', 'Jumlah Siswa', 'Total Pertemuan', 'Hadir', 'Izin/Sakit', 'Alpa', 'Status'];

        $filename = 'rekap-absensi-'.($isTentor ? 'tentor' : 'kelas').'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows, $header, $isTentor): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);

            foreach ($rows as $row) {
                fputcsv($out, $isTentor
                    ? [$row['nama'], $row['rincian'], $row['sesi'], $row['hadir'], $row['telat'], $row['ketepatan'], $row['status']]
                    : [$row['kode'], $row['nama'], $row['rincian'], $row['siswa'], $row['pertemuan'], $row['hadir'], $row['izin'], $row['alpa'], $row['status']]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
