<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(): View
    {
        return view('admin.kelas', [
            'kelas' => ClassGroup::with('academicPeriod')
                ->withCount(['enrollments as siswa_count' => fn ($query) => $query->where('status', 'aktif')])
                ->orderBy('code')
                ->get(),
            'periods' => AcademicPeriod::orderByDesc('academic_year')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.kelas-form', [
            'classGroup' => new ClassGroup(['status' => 'aktif']),
            'periods' => AcademicPeriod::orderByDesc('academic_year')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        ClassGroup::create($data);

        return redirect()->route('admin.kelas')->with('status', 'Kelas '.$data['code'].' berhasil ditambahkan.');
    }

    public function edit(ClassGroup $classGroup): View
    {
        return view('admin.kelas-form', [
            'classGroup' => $classGroup,
            'periods' => AcademicPeriod::orderByDesc('academic_year')->get(),
        ]);
    }

    public function update(Request $request, ClassGroup $classGroup): RedirectResponse
    {
        $data = $this->validated($request, $classGroup);

        $classGroup->update($data);

        return redirect()->route('admin.kelas')->with('status', 'Kelas '.$classGroup->code.' berhasil diperbarui.');
    }

    public function destroy(ClassGroup $classGroup): RedirectResponse
    {
        $code = $classGroup->code;

        if ($classGroup->enrollments()->where('status', 'aktif')->exists()) {
            return back()->withErrors(['kelas' => 'Kelas '.$code.' masih memiliki siswa aktif. Pindahkan siswa terlebih dahulu.']);
        }

        $classGroup->delete();

        return redirect()->route('admin.kelas')->with('status', 'Kelas '.$code.' berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?ClassGroup $classGroup = null): array
    {
        return $request->validate([
            'academic_period_id' => ['required', 'exists:academic_periods,id'],
            'code' => [
                'required', 'string', 'max:30',
                Rule::unique('class_groups', 'code')
                    ->where('academic_period_id', $request->input('academic_period_id'))
                    ->ignore($classGroup?->id),
            ],
            'education_level' => ['nullable', 'in:SD,SMP,SMA'],
            'grade' => ['nullable', 'string', 'max:10'],
            'major' => ['nullable', 'string', 'max:30'],
            'curriculum' => ['nullable', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);
    }
}
