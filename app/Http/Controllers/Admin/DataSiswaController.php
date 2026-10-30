<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataSiswaController extends Controller
{
    /**
     * Tampilkan database induk siswa dan informasi kelas (data dari DB).
     */
    public function index(Request $request): View
    {
        $query = Student::with(['user', 'guardians'])
            ->orderByDesc('created_at');

        // Pencarian nama, NIS, NISN, email, atau sekolah.
        if ($request->filled('q')) {
            $kata = $request->q;
            $query->where(function ($q) use ($kata) {
                $q->where('full_name', 'like', "%{$kata}%")
                    ->orWhere('nis', 'like', "%{$kata}%")
                    ->orWhere('nisn', 'like', "%{$kata}%")
                    ->orWhere('email', 'like', "%{$kata}%")
                    ->orWhere('school_name', 'like', "%{$kata}%");
            });
        }

        // Filter jenjang (SD/SMP/SMA).
        if ($request->filled('jenjang') && $request->jenjang !== 'semua') {
            $query->where('education_level', strtoupper($request->jenjang));
        }

        // Filter status.
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $siswa = $query->paginate(10)->withQueryString();

        // Angka pokok data siswa (KPI) dari DB.
        $kpis = [
            [
                'label' => 'Total Siswa Aktif',
                'nilai' => Student::active()->count(),
                'satuan' => 'Siswa',
                'nilai_kelas' => 'text-ink',
                'ikon' => 'users',
                'tile' => 'bg-sand text-ink',
                'catatan' => 'Terdaftar semester ini',
            ],
            [
                'label' => 'Siswa Baru',
                'nilai' => Student::where('status', 'aktif')
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'satuan' => 'Siswa',
                'nilai_kelas' => 'text-bronze',
                'ikon' => 'user-plus',
                'tile' => 'bg-gold text-espresso',
                'catatan' => 'Menunggu verifikasi berkas',
            ],
            [
                'label' => 'Distribusi Jenjang',
                'nilai' => Student::distinct('education_level')->count('education_level'),
                'satuan' => 'Tingkatan',
                'nilai_kelas' => 'text-ink',
                'ikon' => 'graduation-cap',
                'tile' => 'bg-sand text-ink',
                'catatan' => $this->distribusiCatatan(),
            ],
        ];

        return view('admin.datasiswa', compact('siswa', 'kpis'));
    }

    /**
     * Catatan distribusi siswa per jenjang untuk KPI.
     */
    protected function distribusiCatatan(): string
    {
        $sd = Student::where('education_level', 'SD')->count();
        $smp = Student::where('education_level', 'SMP')->count();
        $sma = Student::where('education_level', 'SMA')->count();

        return "SD ({$sd}) • SMP ({$smp}) • SMA ({$sma})";
    }
}
