<?php

namespace App\Http\Controllers\Tentor;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\StaffProfile;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class TentorMateriController extends Controller
{
    /**
     * Tampilkan halaman Bank Materi & Bahan Ajar untuk Tentor.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Query dasar materi berstatus terbit
        $query = Material::query()
            ->with(['subject', 'file', 'uploader', 'classGroups'])
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        // 2. Pencarian kata kunci (Requirement 1: Search dapat melakukan pencarian)
        if ($request->filled('q')) {
            $keyword = trim((string) $request->query('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('subject', function ($sq) use ($keyword) {
                        $sq->where('name', 'like', "%{$keyword}%")
                            ->orWhere('code', 'like', "%{$keyword}%");
                    });
            });
        }

        // 3. Filter Mata Pelajaran (Dropdown)
        if ($request->filled('mapel') && $request->query('mapel') !== 'semua') {
            $query->where('subject_id', $request->query('mapel'));
        }

        // 4. Filter Jenjang Kelas (Requirement 2: Filter jenjang kelas)
        $activeJenjang = (string) $request->query('jenjang', 'semua');
        if ($activeJenjang !== 'semua') {
            $query->whereHas('classGroups', function ($q) use ($activeJenjang) {
                if ($activeJenjang === '10') {
                    $q->where('grade', '10')->orWhere('code', 'like', '10-%');
                } elseif ($activeJenjang === '11') {
                    $q->where('grade', '11')->orWhere('code', 'like', '11-%');
                } elseif ($activeJenjang === '12') {
                    $q->where('grade', '12')->orWhere('code', 'like', '12-%');
                } elseif ($activeJenjang === 'smp') {
                    $q->where('education_level', 'SMP')->orWhereIn('grade', ['7', '8', '9']);
                } elseif ($activeJenjang === 'sd') {
                    $q->where('education_level', 'SD')->orWhereIn('grade', ['1', '2', '3', '4', '5', '6']);
                } else {
                    $q->where('grade', $activeJenjang)->orWhere('education_level', strtoupper($activeJenjang));
                }
            });
        }

        // 5. Filter Kategori (Requirement 2: Filter kategori)
        $activeKategori = (string) $request->query('kategori', 'semua');
        if ($activeKategori !== 'semua') {
            if ($activeKategori === 'slide') {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%slide%')
                        ->orWhere('title', 'like', '%presentasi%')
                        ->orWhereHas('file', function ($fq) {
                            $fq->where('mime_type', 'like', '%presentation%')
                                ->orWhere('original_name', 'like', '%.ppt%');
                        });
                });
            } elseif ($activeKategori === 'latihan') {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%latihan%')
                        ->orWhere('title', 'like', '%soal%')
                        ->orWhere('title', 'like', '%paket%')
                        ->orWhere('title', 'like', '%try out%')
                        ->orWhere('title', 'like', '%tryout%')
                        ->orWhere('title', 'like', '%bank soal%');
                });
            } elseif ($activeKategori === 'modul') {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%modul%')
                        ->orWhere(function ($sub) {
                            $sub->where('title', 'not like', '%slide%')
                                ->where('title', 'not like', '%presentasi%')
                                ->where('title', 'not like', '%soal%')
                                ->where('title', 'not like', '%latihan%');
                        });
                });
            } elseif ($activeKategori === 'diampu') {
                $staff = StaffProfile::where('user_id', $user?->id)->first();
                if ($staff) {
                    $subjectIds = $staff->subjects()->pluck('subjects.id')->all();
                    $query->whereIn('subject_id', $subjectIds);
                }
            }
        }

        // 6. Pagination 10 materi per halaman (Requirement 6)
        $materials = $query->paginate(10)->withQueryString();

        // 7. Statistik modul, paket latihan, dan slide sesuai database (Requirement 7)
        $countSlide = Material::published()
            ->where(function ($q) {
                $q->where('title', 'like', '%slide%')
                    ->orWhere('title', 'like', '%presentasi%')
                    ->orWhereHas('file', function ($fq) {
                        $fq->where('mime_type', 'like', '%presentation%')
                            ->orWhere('original_name', 'like', '%.ppt%');
                    });
            })->count();

        $countLatihan = Material::published()
            ->where(function ($q) {
                $q->where('title', 'like', '%latihan%')
                    ->orWhere('title', 'like', '%soal%')
                    ->orWhere('title', 'like', '%paket%')
                    ->orWhere('title', 'like', '%try out%')
                    ->orWhere('title', 'like', '%tryout%')
                    ->orWhere('title', 'like', '%bank soal%');
            })->count();

        $countModul = Material::published()
            ->where(function ($q) {
                $q->where('title', 'like', '%modul%')
                    ->orWhere(function ($sub) {
                        $sub->where('title', 'not like', '%slide%')
                            ->where('title', 'not like', '%presentasi%')
                            ->where('title', 'not like', '%soal%')
                            ->where('title', 'not like', '%latihan%');
                    });
            })->count();

        $totalTerbit = Material::published()->count();

        $stats = [
            [
                'Modul Tersedia',
                (string) $countModul,
                'Modul Lengkap',
                'Sesuai Kurikulum Nasional Terintegrasi',
                'book-open',
                'bg-ember-50 text-ember-600',
            ],
            [
                'Paket Latihan',
                (string) $countLatihan,
                'Paket Soal SNBT',
                'Dilengkapi Kunci & Pembahasan',
                'check-circle',
                'bg-emerald-50 text-emerald-600',
            ],
            [
                'Slide Presentasi',
                (string) $countSlide,
                'Berkas Bahan Ajar',
                'Siap Tayang di Smartboard / Layar',
                'presentation',
                'bg-bark-50 text-bark-500',
            ],
        ];

        $jenjangList = [
            ['key' => 'semua', 'label' => 'Semua Kelas'],
            ['key' => '10', 'label' => '10 SMA'],
            ['key' => '11', 'label' => '11 SMA'],
            ['key' => '12', 'label' => '12 SMA / SNBT'],
            ['key' => 'smp', 'label' => 'SMP / Persiapan'],
            ['key' => 'sd', 'label' => 'SD'],
        ];

        $kategoriList = [
            ['key' => 'semua', 'label' => 'Semua ('.$totalTerbit.')'],
            ['key' => 'modul', 'label' => 'Modul Latihan ('.$countModul.')'],
            ['key' => 'latihan', 'label' => 'Paket Latihan ('.$countLatihan.')'],
            ['key' => 'slide', 'label' => 'Slide Presentasi ('.$countSlide.')'],
        ];

        $subjects = Subject::orderBy('name')->get();

        return view('tentor.materi', [
            'materials' => $materials,
            'stats' => $stats,
            'jenjangList' => $jenjangList,
            'kategoriList' => $kategoriList,
            'activeJenjang' => $activeJenjang,
            'activeKategori' => $activeKategori,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Buka / unduh berkas materi untuk ditayangkan di kelas.
     */
    public function buka(Material $material): Response
    {
        $file = $material->file;

        if ($file && Storage::disk('local')->exists($file->path)) {
            return Storage::disk('local')->response($file->path, $file->original_name);
        }

        abort(404, 'Berkas materi tidak ditemukan pada penyimpanan server.');
    }
}
