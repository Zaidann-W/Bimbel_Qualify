<?php

namespace App\Http\Controllers\Tentor;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Material;
use App\Models\StaffProfile;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MateriController extends Controller
{
    /**
     * Daftar modul ajar untuk kelas yang diampu tentor ini.
     *
     * Materi difilter ke kelas-kelas tempat tentor memiliki sesi mengajar,
     * lalu dapat disaring lebih lanjut lewat pencarian, mapel, dan kelas.
     */
    public function index(Request $request): View
    {
        $staffProfile = StaffProfile::where('user_id', $request->user()->id)->first();

        $classGroupIds = $staffProfile !== null
            ? ClassSession::query()->where('staff_id', $staffProfile->id)->pluck('class_group_id')->unique()->values()->all()
            : [];

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'subject_id' => is_numeric($request->query('subject_id')) ? (int) $request->query('subject_id') : null,
            'class_group_id' => is_numeric($request->query('class_group_id')) ? (int) $request->query('class_group_id') : null,
        ];

        $materials = Material::query()
            ->with(['subject', 'file', 'uploader', 'classGroups'])
            ->published()
            ->whereHas('classGroups', fn ($q) => $q->whereIn('class_groups.id', $classGroupIds))
            ->when($filters['q'] !== '', fn ($q) => $q->where('title', 'like', '%'.$filters['q'].'%'))
            ->when($filters['subject_id'] !== null, fn ($q) => $q->where('subject_id', $filters['subject_id']))
            ->when($filters['class_group_id'] !== null, fn ($q) => $q->whereHas(
                'classGroups',
                fn ($i) => $i->where('class_groups.id', $filters['class_group_id']),
            ))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12);

        $subjects = Subject::query()->orderBy('name')->get();
        $classGroups = ClassGroup::query()->whereIn('id', $classGroupIds)->orderBy('code')->get();

        $stats = [
            ['label' => 'Modul Tersedia', 'value' => (string) $materials->total(), 'unit' => 'Modul', 'sub' => 'Materi terbit untuk kelas Anda', 'icon' => 'book-open', 'tone' => 'bg-ember-50 text-ember-600'],
            ['label' => 'Mata Pelajaran', 'value' => (string) $subjects->count(), 'unit' => 'Mapel', 'sub' => 'Tersedia di repositori', 'icon' => 'layers', 'tone' => 'bg-bark-50 text-bark-500'],
            ['label' => 'Kelas Diampu', 'value' => (string) $classGroups->count(), 'unit' => 'Kelas', 'sub' => 'Memiliki sesi mengajar', 'icon' => 'users', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ];

        return view('tentor.materi', [
            'materials' => $materials,
            'subjects' => $subjects,
            'classGroups' => $classGroups,
            'stats' => $stats,
            'filters' => $filters,
        ]);
    }

    /**
     * Unduh berkas modul sesuai hak akses tentor pada kelas yang diampunya.
     */
    public function unduh(Request $request, Material $materi): StreamedResponse
    {
        $staffProfile = StaffProfile::where('user_id', $request->user()->id)->first();

        abort_if($staffProfile === null, 403, 'Akun ini belum terhubung ke data tentor.');
        abort_if($materi->status !== 'terbit', 404);

        $classGroupIds = ClassSession::query()
            ->where('staff_id', $staffProfile->id)
            ->pluck('class_group_id');

        abort_unless(
            $materi->classGroups()->whereIn('class_groups.id', $classGroupIds)->exists(),
            403,
            'Materi ini tidak ditujukan untuk kelas yang Anda ampu.'
        );

        abort_unless(
            $materi->allow_download,
            403,
            'Materi ini hanya dapat dibaca secara online.'
        );

        $file = $materi->file;

        abort_if(
            $file === null || ! Storage::disk('local')->exists($file->path),
            404,
            'Berkas modul tidak ditemukan.'
        );

        return Storage::disk('local')->download($file->path, $file->original_name);
    }
}
