<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ClassGroup;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
=======
use App\Models\Student;
use Illuminate\Http\Request;
>>>>>>> origin/main
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataSiswaController extends Controller
{
    /**
     * Tampilkan database induk siswa dan informasi kelas (data dari DB).
     */
    public function index(Request $request): View|StreamedResponse
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'jenjang' => $request->query('jenjang', 'semua'),
            'status' => $request->query('status', 'semua'),
        ];

        $siswaQuery = Student::query()
            ->with(['guardians', 'enrollments.classGroup'])
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $keyword = '%'.$filters['q'].'%';
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('full_name', 'like', $keyword)
                        ->orWhere('nis', 'like', $keyword)
                        ->orWhere('nisn', 'like', $keyword)
                        ->orWhere('school_name', 'like', $keyword);
                });
            })
            ->when($filters['jenjang'] !== 'semua', fn ($query) => $query->where('education_level', strtoupper($filters['jenjang'])))
            ->when($filters['status'] !== 'semua', fn ($query) => $query->where('status', $filters['status']))
            ->orderBy('full_name');

        if ($request->query('export') === 'csv') {
            return $this->exportCsv($siswaQuery->get());
        }

        $siswa = $siswaQuery->paginate(10)->withQueryString();

        return view('admin.datasiswa', [
            'siswa' => $siswa,
            'filters' => $filters,
            'kpis' => $this->kpis(),
            'distribusi' => $this->distribusi(),
            'kelas' => ClassGroup::with('academicPeriod')->withCount(['enrollments as siswa_count' => fn ($query) => $query->where('status', 'aktif')])->orderBy('code')->get(),
        ]);
    }

    /**
     * @param  Collection<int, Student>  $students
     */
    private function exportCsv($students): StreamedResponse
    {
        $filename = 'data-siswa-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($students): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nama', 'NIS', 'NISN', 'Jenjang', 'Sekolah', 'Kelas', 'Wali', 'Telepon', 'Status']);

            foreach ($students as $student) {
                $guardian = $student->guardians->firstWhere('pivot.is_primary_contact', true) ?? $student->guardians->first();
                $enrollment = $student->enrollments->firstWhere('status', 'aktif');

                fputcsv($out, [
                    $student->full_name,
                    $student->nis,
                    $student->nisn,
                    $student->education_level,
                    $student->school_name,
                    $enrollment?->classGroup?->code,
                    $guardian?->name,
                    $guardian?->phone_wa,
                    $student->status,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function create(): View
    {
        return view('admin.siswa-form', [
            'student' => new Student(['status' => 'aktif']),
            'classGroups' => ClassGroup::orderBy('code')->get(),
            'guardian' => null,
            'classGroupId' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $student = Student::create($this->studentPayload($data));
            $this->syncGuardian($student, $data);
            $this->syncEnrollment($student, $data['class_group_id'] ?? null);
        });

        return redirect()->route('admin.dataSiswa')->with('status', 'Siswa '.$data['full_name'].' berhasil ditambahkan.');
    }

    public function show(Student $student): View
    {
        $student->load(['guardians', 'enrollments.classGroup.academicPeriod', 'invoices']);

        return view('admin.siswa-detail', ['student' => $student]);
    }

    public function edit(Student $student): View
    {
        $guardian = $student->guardians()->wherePivot('is_primary_contact', true)->first()
            ?? $student->guardians()->first();

        return view('admin.siswa-form', [
            'student' => $student,
            'classGroups' => ClassGroup::orderBy('code')->get(),
            'guardian' => $guardian,
            'classGroupId' => $student->enrollments()->where('status', 'aktif')->value('class_group_id'),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $this->validated($request, $student);

        DB::transaction(function () use ($student, $data) {
            $student->update($this->studentPayload($data));
            $this->syncGuardian($student, $data);
            $this->syncEnrollment($student, $data['class_group_id'] ?? null);
        });

        return redirect()->route('admin.dataSiswa')->with('status', 'Data siswa '.$student->full_name.' berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        DB::transaction(function () use ($student) {
            Enrollment::where('student_id', $student->id)
                ->where('status', 'aktif')
                ->update(['status' => 'keluar', 'ended_on' => now()->toDateString()]);

            $student->delete();
        });

        return redirect()->route('admin.dataSiswa')->with('status', 'Siswa '.$student->full_name.' berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'nis' => ['nullable', 'string', 'max:30', 'unique:students,nis,'.($student?->id ?? 'NULL')],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:students,nisn,'.($student?->id ?? 'NULL')],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone_wa' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'school_name' => ['nullable', 'string', 'max:150'],
            'school_grade' => ['nullable', 'string', 'max:10'],
            'education_level' => ['required', 'in:SD,SMP,SMA'],
            'curriculum' => ['nullable', 'string', 'max:50'],
            'major' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:aktif,cuti,nonaktif,lulus'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'class_group_id' => ['nullable', 'exists:class_groups,id'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function studentPayload(array $data): array
    {
        return collect($data)
            ->only([
                'full_name', 'nickname', 'nis', 'nisn', 'birth_place', 'birth_date', 'email',
                'phone_wa', 'address', 'school_name', 'school_grade', 'education_level',
                'curriculum', 'major', 'status',
            ])
            ->filter(fn ($value) => $value !== null)
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncGuardian(Student $student, array $data): void
    {
        $name = $data['guardian_name'] ?? null;

        if ($name === null || $name === '') {
            return;
        }

        $guardian = $student->guardians()->wherePivot('is_primary_contact', true)->first()
            ?? $student->guardians()->first();

        if ($guardian) {
            $guardian->update(['name' => $name, 'phone_wa' => $data['guardian_phone'] ?? $guardian->phone_wa]);

            return;
        }

        $guardian = Guardian::create([
            'name' => $name,
            'phone_wa' => $data['guardian_phone'] ?? null,
        ]);

        $student->guardians()->attach($guardian->id, [
            'relationship' => 'wali',
            'is_primary_contact' => true,
            'is_financial_responsible' => true,
        ]);
    }

    private function syncEnrollment(Student $student, ?int $classGroupId): void
    {
        $active = $student->enrollments()->where('status', 'aktif');

        if ($classGroupId === null) {
            $active->update(['status' => 'keluar', 'ended_on' => now()->toDateString()]);

            return;
        }

        if ((clone $active)->where('class_group_id', $classGroupId)->exists()) {
            return;
        }

        $active->update(['status' => 'pindah', 'ended_on' => now()->toDateString()]);

        Enrollment::create([
            'student_id' => $student->id,
            'class_group_id' => $classGroupId,
            'started_on' => now()->toDateString(),
            'status' => 'aktif',
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function kpis(): array
    {
        $aktif = Student::where('status', 'aktif')->count();
        $baru = Student::where('status', 'aktif')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $distribusi = $this->distribusi();

        return [
            [
                'label' => 'Total Siswa Aktif',
                'nilai' => number_format($aktif, 0, ',', '.'),
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

                'nilai' => number_format($baru, 0, ',', '.'),
=======
                'nilai' => Student::where('status', 'aktif')
                    ->whereMonth('created_at', now()->month)
                    ->count(),
>>>>>>> origin/main
                'satuan' => 'Siswa',
                'nilai_kelas' => 'text-bronze',
                'ikon' => 'user-plus',
                'tile' => 'bg-gold text-espresso',

                'catatan' => 'Terdaftar bulan ini',
            ],
            [
                'label' => 'Distribusi Jenjang',
                'nilai' => (string) collect($distribusi)->where('jumlah_raw', '>', 0)->count(),
=======
                'catatan' => 'Menunggu verifikasi berkas',
            ],
            [
                'label' => 'Distribusi Jenjang',
                'nilai' => Student::distinct('education_level')->count('education_level'),
>>>>>>> origin/main
                'satuan' => 'Tingkatan',
                'nilai_kelas' => 'text-ink',
                'ikon' => 'graduation-cap',
                'tile' => 'bg-sand text-ink',

                'catatan' => collect($distribusi)
                    ->map(fn (array $row): string => str_replace('Jenjang ', '', $row['jenjang']).' ('.$row['jumlah_raw'].')')
                    ->implode(' • '),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function distribusi(): array
    {
        $total = Student::where('status', 'aktif')->count();
        $counts = Student::where('status', 'aktif')
            ->select('education_level', DB::raw('count(*) as total'))
            ->groupBy('education_level')
            ->pluck('total', 'education_level');

        return collect(['SMA', 'SMP', 'SD'])
            ->map(function (string $level) use ($counts, $total): array {
                $jumlah = (int) ($counts[$level] ?? 0);
                $persen = $total > 0 ? (int) round($jumlah / $total * 100) : 0;

                return [
                    'jenjang' => 'Jenjang '.$level,
                    'jumlah' => $jumlah.' Siswa ('.$persen.'%)',
                    'jumlah_raw' => $jumlah,
                    'persen' => $persen,
                ];
            })
            ->all();
=======
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
>>>>>>> origin/main
    }
}
