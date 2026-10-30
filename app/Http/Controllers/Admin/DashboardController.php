<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): View|StreamedResponse
    {
        $dashboard = $this->service->adminDashboard();

        if ($request->query('export') === 'csv') {
            return $this->exportRekapHarian($dashboard);
        }

        return view('admin.dashboard', [
            'service' => $this->service,
            'recentActivities' => $this->service->recentActivities(),
            'activeUserCounts' => $this->service->activeUserCounts(),
            'dashboard' => $dashboard,
        ]);
    }

    /**
     * @param  array<string, mixed>  $dashboard
     */
    private function exportRekapHarian(array $dashboard): StreamedResponse
    {
        $filename = 'rekap-harian-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($dashboard): void {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Metrik Hari Ini', 'Nilai']);
            foreach ($dashboard['stats'] as $stat) {
                fputcsv($out, [$stat['label'], $stat['value'].' ('.$stat['note'].')']);
            }

            fputcsv($out, []);
            fputcsv($out, ['Arus Kas', 'Nilai']);
            fputcsv($out, ['Total Masuk', $dashboard['cash']['income']]);
            fputcsv($out, ['Total Keluar', $dashboard['cash']['expense'] ?? 'belum tersedia']);
            fputcsv($out, ['Saldo Bersih', $dashboard['cash']['balance']]);

            fputcsv($out, []);
            fputcsv($out, ['Sesi', 'Mata Pelajaran', 'Kelas', 'Tentor', 'Ruang', 'Presensi']);
            foreach (['sore', 'malam'] as $shift) {
                foreach ($dashboard['sessions'][$shift] as $session) {
                    fputcsv($out, [ucfirst($shift), $session['subject'], $session['grade'], $session['tutor'], $session['room'], $session['presence']]);
                }
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
