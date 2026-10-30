<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanWebsiteController extends Controller
{
    public function index()
    {
        $settings = WebsiteSetting::all()->keyBy('key');
        $testimonials = Testimonial::orderBy('diinput', 'desc')->get();

        return view('owner.pengaturan-website', [
            'identitas' => $settings->only([
                'nama_lembaga', 'slogan', 'whatsapp', 'email', 'alamat',
            ])->transform(fn ($s) => [
                'id' => $s['key'],
                'label' => match ($s['key']) {
                    'nama_lembaga' => 'Nama Lembaga Pendidikan',
                    'slogan' => 'Slogan / Tagline Publik',
                    'whatsapp' => 'Nomor WhatsApp CS Publik',
                    'email' => 'Email Informasi Publik',
                    'alamat' => 'Alamat Kampus Pusat (Headquarter)',
                    default => $s['key'],
                },
                'ikon' => null,
                'nilai' => $s->value,
                'tipe' => match ($s->key) {
                    'whatsapp' => 'tel',
                    'email' => 'email',
                    default => 'text',
                },
                'terkunci' => $s->key === 'nama_lembaga',
                'verifikasi' => $s->key === 'nama_lembaga',
                'bantuan' => match ($s->key) {
                    'nama_lembaga' => 'Nama paten brand terikat dengan lisensi legalitas pusat.',
                    'slogan' => 'Ditampilkan tepat di bawah logo utama pada hero banner.',
                    'whatsapp' => 'Tujuan direct link tombol "Konsultasi WhatsApp Sekarang".',
                    'email' => 'Untuk menerima formulir kemitraan dan pertanyaan pendaftaran.',
                    'alamat' => 'Tercantum pada footer dan peta Google Maps terintegrasi.',
                    default => '',
                },
                'penuh' => false,
            ])->toArray(),
            'testimoni' => $testimonials->transform(fn ($t) => [
                'inisial' => $t->inisial,
                'avatar' => $t->avatar,
                'nama' => $t->nama,
                'meta' => $t->meta,
                'rating' => $t->rating,
                'kutipan' => $t->kutipan,
                'diinput' => $t->diinput,
                'kategori' => $t->kategori,
                'tayang' => $t->tayang,
                'ekstra' => $t->ekstra,
            ])->toArray(),
        ]);
    }

    public function update(Request $request)
    {
        DB::transaction(function () use ($request) {
            // Update website settings
            $validated = $request->validate([
                'nama_lembaga' => 'required|string|max:100',
                'slogan' => 'required|string|max:200',
                'whatsapp' => 'required|string|regex:/^[0-9]{8,15}$/',
                'email' => 'required|string|email|max:100',
                'alamat' => 'required|string|max:255',
            ], [
                'nama_lembaga.required' => 'Nama lembaga wajib diisi.',
                'slogan.required' => 'Slogan wajib diisi.',
                'whatsapp.regex' => 'No. WA harus berisi 8-15 digit angka.',
                'email.email' => 'Format email tidak valid.',
            ]);

            WebsiteSetting::where('key', 'nama_lembaga')->update(['value' => $validated['nama_lembaga']]);
            WebsiteSetting::where('key', 'slogan')->update(['value' => $validated['slogan']]);
            WebsiteSetting::where('key', 'whatsapp')->update(['value' => $validated['whatsapp']]);
            WebsiteSetting::where('key', 'email')->update(['value' => $validated['email']]);
            WebsiteSetting::where('key', 'alamat')->update(['value' => $validated['alamat']]);

            // Update testimonials
            foreach ($request->input('testimoni', []) as $index => $testimoni) {
                $tahunLahir = substr($testimoni['diinput'] ?? '', 0, 4);
                $tahunSekarang = date('Y');
                $umur = $tahunSekarang - $tahunLahir;

                Testimonial::updateOrCreate(
                    ['id' => $testimoni['id'] ?? null],
                    [
                        'inisial' => substr($testimoni['nama'] ?? '', 0, 1).'.',
                        'avatar' => match ($testimoni['kategori'] ?? '') {
                            'Siswa Sukses' => 'bg-palegold text-espresso',
                            'Wali Murid' => 'bg-espresso text-white',
                            default => 'bg-gold text-espresso',
                        },
                        'nama' => $testimoni['nama'] ?? '',
                        'meta' => $testimoni['meta'] ?? '',
                        'rating' => $testimoni['rating'] ?? 5,
                        'kutipan' => $testimoni['kutipan'] ?? '',
                        'diinput' => $testimoni['diinput'] ?? date('Y-m-d'),
                        'kategori' => $testimoni['kategori'] ?? 'Siswa Sukses',
                        'tayang' => $testimoni['tayang'] ?? true,
                        'ekstra' => $testimoni['ekstra'] ?? false,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan website berhasil disimpan dan langsung tayang di website publik.',
        ]);
    }
}
