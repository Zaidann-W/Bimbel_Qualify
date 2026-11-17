<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengaturanController extends Controller
{
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
            DB::transaction(function () use ($validated): void {
                foreach ($validated as $key => $value) {
                    WebsiteSetting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => is_bool($value) ? ($value ? '1' : '0') : $value,
                            'description' => ucfirst(str_replace('_', ' ', $key)),
                        ]
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
