<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Material;
use App\Models\QualifixTicket;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudentDashboardService
{
    /**
     * Susun seluruh data dashboard siswa dari basis data.
     *
     * @return array{
     *     kelas: string,
     *     periode: string,
     *     stats: array<int, array<string, mixed>>,
     *     jadwal: Collection<int, array<string, mixed>>,
     *     sesiUnggulan: array<string, mixed>|null,
     *     modulBaru: Collection<int, array<string, mixed>>,
     *     pengumuman: Announcement|null
     * }
     */
    public function dashboard(?Student $student, $classGroups): array
    {
        // Normalisasi input: bisa berupa object tunggal, collection, atau array
        if ($classGroups instanceof ClassGroup) {
            $classGroups = [$classGroups];
        }
        
        if ($classGroups instanceof \Illuminate\Support\Collection) {
            $classGroups = $classGroups->all();
        }
        
        if (!is_array($classGroups)) {
            $classGroups = (array) $classGroups;
        }

        $pekan = now();

        return [
            'kelas' => $this->labelKelas($classGroups),
            'periode' => $this->labelPeriode($classGroups, $pekan),
            'stats' => $this->statCards($student, $classGroups),
            'jadwal' => $this->jadwalPekan($student, $classGroups, $pekan),
            'sesiUnggulan' => $this->sesiUnggulan($classGroups),
            'modulBaru' => $this->modulBaru($classGroups),
            'pengumuman' => $this->pengumuman($classGroups),
        ];
    }

    /**
     * Ekstrak ID dari array/collection class groups secara aman.
     * Handle: array of objects, array of arrays, collection of objects.
     *
     * @return array<int, int>
     */
    private function extractClassGroupIds(array $classGroups): array
    {
        $ids = array_map(function ($cg) {
            if (is_object($cg)) {
                return $cg->id ?? null;
            }
            if (is_array($cg)) {
                return $cg['id'] ?? null;
            }
            return null;
        }, $classGroups);

        return array_values(array_filter($ids));
    }

    /**
     * Ambil ClassGroup pertama dari array untuk display label.
     */
    private function firstClassGroup(array $classGroups): ?ClassGroup
    {
        $first = $classGroups[0] ?? null;
        
        if ($first instanceof ClassGroup) {
            return $first;
        }
        
        if (is_array($first)) {
            // Kalau berupa array, konversi balik ke object (atau buat minimal)
            return ClassGroup::find($first['id'] ?? null);
        }
        
        return null;
    }

    private function labelKelas(array $classGroups): string
    {
        if (empty($classGroups)) {
            return 'Siswa';
        }

        // Ambil class group pertama saja untuk label display
        $cg = $this->firstClassGroup($classGroups);

        return trim(sprintf(
            'Kelas %s %s – %s',
            $cg?->grade ?? '',
            $cg?->education_level ?? '',
            $cg?->major ?? '',
        ));
    }

    private function labelPeriode(array $classGroups, Carbon $pekan): string
    {
        $awal = $pekan->copy()->startOfWeek()->locale('id')->translatedFormat('j M');
        $akhir = $pekan->copy()->endOfWeek()->locale('id')->translatedFormat('j M Y');

        $kelasLabel = empty($classGroups) ? 'Belum ada kelas aktif' : $this->labelKelas($classGroups);

        return trim(sprintf(
            'Periode %s – %s • %s',
            $awal,
            $akhir,
            $kelasLabel,
        ));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function statCards(?Student $student, array $classGroups): array
    {
        $classGroupIds = $this->extractClassGroupIds($classGroups);
        
        $modul = Material::query()
            ->where('status', 'terbit')
            ->when(!empty($classGroupIds), fn ($q) => $q->whereHas(
                'classGroups',
                fn ($q) => $q->whereIn('class_groups.id', $classGroupIds),
            ))
            ->count();

        [$hadir, $totalSesi, $persen] = $this->rekapPresensi($student, $classGroups);

        $tiket = QualifixTicket::query()->where('student_id', $student?->id);
        $aktif = (clone $tiket)->whereIn('status', ['menunggu_tentor', 'sedang_dibahas'])->count();
        $terjawab = (clone $tiket)->where('status', 'selesai')->count();
        $menunggu = (clone $tiket)->where('status', 'menunggu_tentor')->count();

        [$labelPresensi, $tonePresensi] = match (true) {
            $persen >= 90 => ['Sangat Baik', 'text-emerald-600 font-bold'],
            $persen >= 75 => ['Baik', 'text-amber-600 font-bold'],
            $totalSesi === 0 => ['Belum Ada Data', 'text-bark-400 font-medium'],
            default => ['Perlu Perhatian', 'text-ember-600 font-bold'],
        };

        return [
            [
                'label' => 'Modul Belajar',
                'chip' => false,
                'value' => (string) $modul,
                'sub' => 'Materi Siap Baca',
                'sub_tone' => 'text-bark-400 font-medium',
                'footer_kind' => 'link',
                'footer' => 'Buka Modul',
                'footer_extra' => null,
                'href' => route('siswa.materi'),
                'icon' => 'book-open',
                'icon_tone' => 'bg-bark-50 text-bark-400',
            ],
            [
                'label' => 'Presensi',
                'chip' => true,
                'value' => number_format($persen, 1, '.', ',').'%',
                'sub' => $labelPresensi,
                'sub_tone' => $tonePresensi,
                'footer_kind' => 'text',
                'footer' => sprintf('%d Hadir / %d Sesi', $hadir, $totalSesi),
                'footer_extra' => null,
                'href' => null,
                'icon' => 'clipboard-check',
                'icon_tone' => 'bg-emerald-50 text-emerald-600',
            ],
            [
                'label' => 'Klinik Soal',
                'chip' => false,
                'value' => (string) $aktif,
                'sub' => 'Pertanyaan Aktif',
                'sub_tone' => 'text-bark-400 font-medium',
                'footer_kind' => 'duo',
                'footer' => $terjawab.' Terjawab',
                'footer_extra' => $menunggu.' Menunggu',
                'href' => route('siswa.qualifix'),
                'icon' => 'message-circle',
                'icon_tone' => 'bg-ember-50 text-ember-500',
            ],
        ];
    }

    /**
     * @return array{0: int, 1: int, 2: float}
     */
    private function rekapPresensi(?Student $student, array $classGroups): array
    {
        $classGroupIds = $this->extractClassGroupIds($classGroups);
        
        if ($student === null || empty($classGroupIds)) {
            return [0, 0, 0.0];
        }

        $totalSesi = ClassSession::query()
            ->whereIn('class_group_id', $classGroupIds)
            ->where('status', '!=', 'dibatalkan')
            ->count();

        $hadir = StudentAttendance::query()
            ->where('student_id', $student->id)
            ->where('status', 'hadir')
            ->whereHas('classSession', fn ($q) => $q->whereIn('class_group_id', $classGroupIds))
            ->count();

        $persen = $totalSesi === 0 ? 0.0 : round($hadir / $totalSesi * 100, 1);

        return [$hadir, $totalSesi, $persen];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function jadwalPekan(?Student $student, array $classGroups, Carbon $pekan): Collection
    {
        $classGroupIds = $this->extractClassGroupIds($classGroups);
        
        if (empty($classGroupIds) || $student === null) {
            return collect();
        }

        return ClassSession::query()
            ->with(['subject', 'staff', 'room'])
            ->whereIn('class_group_id', $classGroupIds)
            ->whereBetween('session_date', [
                $pekan->copy()->startOfWeek()->toDateString(),
                $pekan->copy()->endOfWeek()->toDateString(),
            ])
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get()
            ->map(fn (ClassSession $sesi) => $this->kartuSesi($sesi));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function sesiUnggulan(array $classGroups): ?array
    {
        $classGroupIds = $this->extractClassGroupIds($classGroups);
        
        if (empty($classGroupIds)) {
            return null;
        }

        $sesi = ClassSession::query()
            ->with(['subject', 'staff', 'room'])
            ->whereIn('class_group_id', $classGroupIds)
            ->whereDate('session_date', '>=', today())
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->first();

        return $sesi === null ? null : $this->kartuSesi($sesi);
    }

    /**
     * @return array<string, mixed>
     */
    private function kartuSesi(ClassSession $sesi): array
    {
        $tanggal = Carbon::parse($sesi->session_date);

        return [
            'hari' => Str::upper(substr($tanggal->locale('id')->translatedFormat('l'), 0, 3)),
            'tgl' => $tanggal->format('j'),
            'mapel' => $sesi->subject?->name ?? 'Mata Pelajaran',
            'topik' => $sesi->topic ?? '-',
            'waktu' => sprintf(
                '%s – %s WIB • %s',
                $this->jam($sesi->start_time),
                $this->jam($sesi->end_time),
                $this->ruang($sesi),
            ),
            'tentor' => $sesi->staff?->full_name_with_title ?? '-',
            'hari_ini' => $tanggal->isToday(),
        ];
    }

    private function jam(?string $waktu): string
    {
        return $waktu === null ? '-' : str_replace(':', '.', substr($waktu, 0, 5));
    }

    private function ruang(ClassSession $sesi): string
    {
        $nama = trim(sprintf('%s %s', $sesi->room?->name ?? '', $sesi->room?->floor ?? ''));

        return $nama === '' ? 'Ruang belum ditentukan' : $nama;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function modulBaru(array $classGroup): Collection
    {
        $classGroupIds = $this->extractClassGroupIds($classGroup);
        
        return Material::query()
            ->with('file')
            ->where('status', 'terbit')
            ->when(!empty($classGroupIds), fn ($q) => $q->whereHas(
                'classGroups',
                fn ($q) => $q->whereIn('class_groups.id', $classGroupIds),
            ))
            ->orderByDesc('published_at')
            ->take(3)
            ->get()
            ->map(fn (Material $material) => [
                'badge' => $material->published_at?->greaterThan(now()->subDays(7)) ? 'Baru' : 'Modul',
                'judul' => $material->title,
                'ukuran' => $this->ukuranBerkas($material),
                'deskripsi' => $material->description ?? '',
            ]);
    }

    private function ukuranBerkas(Material $material): string
    {
        $berkas = $material->file;

        if ($berkas === null) {
            return 'Berkas belum tersedia';
        }

        $jenis = strtoupper(pathinfo($berkas->original_name, PATHINFO_EXTENSION) ?: 'FILE');
        $ukuran = match (true) {
            $berkas->size_bytes === null => '-',
            $berkas->size_bytes >= 1048576 => number_format($berkas->size_bytes / 1048576, 1).' MB',
            default => number_format($berkas->size_bytes / 1024, 0).' KB',
        };

        return $jenis.' • '.$ukuran;
    }

    private function pengumuman(array $classGroups): ?Announcement
    {
        $classGroupIds = $this->extractClassGroupIds($classGroups);
        
        return Announcement::query()
            ->with('room')
            ->whereNotNull('published_at')
            ->when(!empty($classGroupIds), fn ($q) => $q->where(
                fn ($q) => $q->whereNull('class_group_id')->orWhereIn('class_group_id', $classGroupIds),
            ))
            ->orderByDesc('published_at')
            ->first();
    }
}
