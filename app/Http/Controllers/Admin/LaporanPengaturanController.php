<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\View\View;

class LaporanPengaturanController extends Controller
{
    /**
     * Tampilkan pusat laporan ekspor dan pengaturan portal admin.
     */
    public function index(): View
    {
        $settings = WebsiteSetting::pluck('value', 'key')->toArray();

        $berkas = [
            ['ikon' => 'wallet', 'judul' => 'Laporan Keuangan & SPP', 'keterangan' => 'Arus kas, uang gedung, dan pelunasan SPP', 'route' => 'admin.laporanKeuangan.export'],
            ['ikon' => 'clipboard-check', 'judul' => 'Presensi & Jadwal Mengajar', 'keterangan' => 'Kehadiran siswa dan jam mengajar tentor', 'route' => 'admin.laporanPresensi.export'],
            ['ikon' => 'user-plus', 'judul' => 'Pendaftaran Siswa Baru', 'keterangan' => 'Data pendaftar baru dan status registrasi berkas', 'route' => null],
            ['ikon' => 'book-open', 'judul' => 'Rekap Sesi Klinik PR', 'keterangan' => 'Sesi konsultasi belajar dan rekap materi', 'route' => null],
        ];

        $notifikasi = [
            ['id' => 'notif-struk', 'label' => 'Kirim bukti struk pembayaran otomatis', 'aktif' => ! empty($settings['notif_struk'])],
            ['id' => 'notif-absensi', 'label' => 'Pemberitahuan ketidakhadiran kelas', 'aktif' => ! empty($settings['notif_absensi'])],
            ['id' => 'notif-tagihan', 'label' => 'Pengingat tagihan SPP jatuh tempo', 'aktif' => ! empty($settings['notif_tagihan'])],
        ];

        $semester_pil = ['Semester Ganjil', 'Semester Genap'];

        return view('admin.laporan-pengaturan', compact('berkas', 'notifikasi', 'semester_pil', 'settings'));
    }
}
