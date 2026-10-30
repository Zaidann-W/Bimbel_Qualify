<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Services\OwnerDashboardService;

class PemantauanMateriController extends Controller
{
    public function __construct(private readonly OwnerDashboardService $service) {}

    public function index()
    {
        $data = $this->service->dashboard()['materi'];

        return view('owner.pemantauan-materi', [
            'modul' => $this->ambilMateriDariDB(),
            'statistik' => $this->ambilStatistikDariDB(),
        ]);
    }

    private function ambilMateriDariDB(): array
    {
        $materials = Material::where('status', 'terbit')
            ->with(['subject', 'file', 'uploader'])
            ->get();

        $modul = [];

        foreach ($materials as $material) {
            $jenjang = $material->classGroups->pluck('name')->join(', ') ?? '-';

            $modul[] = [
                'ikon' => 'file',
                'judul' => $material->title,
                'mata' => $material->subject ? $material->subject->name : '-',
                'jenjang_label' => $jenjang,
                'jenjang' => $material->classGroups->first()?->level ?? 'semua',
                'diperbarui' => $material->published_at ? $material->published_at->format('d M Y H:i') : '-',
                'admin' => $material->uploader ? $material->uploader->name : 'Unknown',
                'halaman' => $material->file ? $material->file->name : '-',
            ];
        }

        return $modul;
    }

    private function ambilStatistikDariDB(): array
    {
        $modulCount = Material::where('status', 'terbit')->count();

        return [
            [
                'label' => 'Kesiapan Akademik',
                'angka' => $modulCount,
                'satuan' => 'Modul',
                'kecil' => false,
                'ikon' => 'file-text',
                'aksen' => 'bg-sandgold/60',
                'penanda' => 'check-circle',
                'catatan' => '100% kurikulum nasional terverifikasi',
            ],
            [
                'label' => 'Evaluasi & Uji Coba',
                'angka' => 0,
                'satuan' => 'Paket Try Out',
                'kecil' => false,
                'ikon' => 'clipboard-form',
                'aksen' => 'bg-shell',
                'penanda' => 'dot',
                'catatan' => 'Soal HOTS & format SNBT terbaru',
            ],
            [
                'label' => 'Pembaruan Terakhir',
                'angka' => 'Hari Ini, 09:15',
                'satuan' => null,
                'kecil' => true,
                'ikon' => 'refresh',
                'aksen' => 'bg-sandgold/50',
                'penanda' => 'clock',
                'catatan' => 'Diunggah oleh Admin',
            ],
        ];
    }
}
