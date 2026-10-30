<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    /**
     * Tampilkan daftar calon siswa yang sedang mendaftar (data dari DB).
     */
    public function index(Request $request): View
    {
        $query = Application::with(['batch'])
            ->orderByDesc('created_at');

        // Pencarian nama, wali, nomor registrasi, atau sekolah.
        if ($request->filled('q')) {
            $kata = $request->q;
            $query->where(function ($q) use ($kata) {
                $q->where('full_name', 'like', "%{$kata}%")
                    ->orWhere('guardian_name', 'like', "%{$kata}%")
                    ->orWhere('registration_number', 'like', "%{$kata}%")
                    ->orWhere('school_name', 'like', "%{$kata}%");
            });
        }

        // Filter status.
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $pendaftar = $query->paginate(10)->withQueryString();

        // Angka pokok admisi (KPI) dari DB.
        $kpis = [
            [
                'label' => 'Menunggu Verifikasi',
                'nilai' => Application::where('status', 'menunggu_review')->count(),
                'satuan' => 'Calon Siswa',
                'ikon' => 'clipboard-form',
                'aksen' => 'border-t-gold',
                'tile' => 'bg-sandgold text-bronze',
                'bawah' => 'sla',
            ],
            [
                'label' => 'Perlu Perbaikan',
                'nilai' => Application::where('status', 'perlu_perbaikan')->count(),
                'satuan' => 'Berkas',
                'ikon' => 'refresh',
                'aksen' => 'border-t-gold',
                'tile' => 'bg-sand text-ink',
                'bawah' => 'chip',
            ],
            [
                'label' => 'Diterima Bulan Ini',
                'nilai' => Application::where('status', 'diterima')
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'satuan' => 'Siswa Baru',
                'ikon' => 'users-check',
                'aksen' => 'border-t-espresso',
                'tile' => 'bg-espresso text-gold',
                'bawah' => 'konversi',
            ],
            [
                'label' => 'Ditolak / Batal',
                'nilai' => Application::whereIn('status', ['ditolak', 'batal'])->count(),
                'satuan' => 'Calon Siswa',
                'ikon' => 'slash-circle',
                'aksen' => 'border-t-line',
                'tile' => 'bg-sand text-ink',
                'bawah' => 'alasan',
            ],
        ];

        $statusMap = [
            'menunggu_review' => ['label' => 'Menunggu Review', 'key' => 'review'],
            'perlu_perbaikan' => ['label' => 'Perlu Perbaikan', 'key' => 'perbaikan'],
            'diterima' => ['label' => 'Diterima', 'key' => 'diterima'],
            'ditolak' => ['label' => 'Ditolak', 'key' => 'ditolak'],
            'batal' => ['label' => 'Ditolak', 'key' => 'ditolak'],
        ];

        $tabs = [
            ['key' => 'semua', 'label' => 'Semua', 'jumlah' => Application::count(), 'hitung' => true],
        ];

        foreach ($statusMap as $status => $info) {
            $tabs[] = [
                'key' => $info['key'],
                'label' => $info['label'],
                'jumlah' => Application::where('status', $status)->count(),
                'hitung' => false,
            ];
        }

        return view('admin.pendaftaran', compact('pendaftar', 'kpis', 'tabs', 'statusMap'));
    }

    /**
     * Tampilkan detail satu calon siswa berdasarkan nomor pendaftaran (data dari DB).
     */
    public function show(string $kode): View
    {
        $pendaftar = Application::where('registration_number', $kode)
            ->with(['batch', 'studyShift', 'documents.file', 'student'])
            ->firstOrFail();

        return view('admin.pendaftaran-detail', compact('pendaftar'));
    }
}
