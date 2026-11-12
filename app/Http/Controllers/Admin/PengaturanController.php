<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    /**
     * Tampilkan halaman pengaturan sistem dengan nilai dari database.
     */
    public function index(): View
    {
        $settings = WebsiteSetting::pluck('value', 'key')->toArray();

        return view('admin.pengaturan', compact('settings'));
    }

    /**
     * Simpan pengaturan sistem ke tabel website_settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'nullable|string|max:50',
            'semester' => 'nullable|string|max:20',
            'penanggung_jawab' => 'nullable|string|max:100',
            'biaya_pendaftaran' => 'nullable|numeric|min:0',
            'notif_struk' => 'nullable|boolean',
            'notif_absensi' => 'nullable|boolean',
            'notif_tagihan' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $mappings = [
                    'tahun_ajaran' => 'tahun_ajaran',
                    'semester' => 'semester',
                    'penanggung_jawab' => 'penanggung_jawab_cabang',
                    'biaya_pendaftaran' => 'biaya_pendaftaran',
                    'notif_struk' => 'notif_struk',
                    'notif_absensi' => 'notif_absensi',
                    'notif_tagihan' => 'notif_tagihan',
                ];

                foreach ($mappings as $inputKey => $settingKey) {
                    if (! array_key_exists($inputKey, $validated)) {
                        continue;
                    }

                    $value = $validated[$inputKey] === true ? '1' : ($validated[$inputKey] ?? null);

                    WebsiteSetting::updateOrCreate(
                        ['key' => $settingKey],
                        ['value' => $value, 'description' => ucfirst(str_replace('_', ' ', $settingKey))]
                    );
                }
            });

            return back()->with('success', 'Pengaturan sistem berhasil disimpan.');
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan pengaturan: '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan pengaturan: '.$e->getMessage());
        }
    }
}
