<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\File;
use App\Models\Material;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ManajemenMateriController extends Controller
{
    /**
     * Tampilkan daftar materi ajar yang data-nya diambil sepenuhnya dari database.
     */
    public function index(Request $request): View
    {
        $query = Material::with(['subject', 'file', 'uploader', 'classGroups'])
            ->orderByDesc('materials.created_at');

        // Pencarian judul, deskripsi, mata pelajaran, atau nama pengunggah.
        if ($request->filled('q')) {
            $kata = $request->q;
            $query->where(function ($q) use ($kata) {
                $q->where('materials.title', 'like', "%{$kata}%")
                    ->orWhere('materials.description', 'like', "%{$kata}%")
                    ->orWhereHas('subject', function ($s) use ($kata) {
                        $s->where('name', 'like', "%{$kata}%");
                    })
                    ->orWhereHas('uploader', function ($u) use ($kata) {
                        $u->where('name', 'like', "%{$kata}%");
                    });
            });
        }

        // Filter status publikasi (draf / terbit).
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('materials.status', $request->status);
        }

        // Filter jenjang kelas sasaran (SD / SMP / SMA).
        if ($request->filled('jenjang') && $request->jenjang !== 'semua') {
            $query->forJenjang($request->jenjang);
        }

        $materi = $query->paginate(10)->withQueryString();

        // Angka pokok repositori materi, semua dihitung dari database.
        $kpis = [
            [
                'nilai' => Material::count(),
                'satuan' => 'Materi',
                'label' => 'Total Materi Ajar',
                'ikon' => 'file-text',
                'tile' => 'bg-sandgold text-bronze',
            ],
            [
                'nilai' => ClassGroup::active()->count(),
                'satuan' => 'Kelas',
                'label' => 'Kelas Terdistribusi',
                'ikon' => 'users',
                'tile' => 'bg-sand text-ink',
            ],
            [
                'nilai' => Material::published()->count(),
                'satuan' => 'Materi',
                'label' => 'Materi Diterbitkan',
                'ikon' => 'eye',
                'tile' => 'bg-palegold text-espresso',
            ],
        ];

        // Tab jenjang dengan jumlah materi yang dihitung dari database.
        $jenjangList = ['sma' => 'SMA', 'smp' => 'SMP', 'sd' => 'SD'];
        $tabs = [
            ['key' => 'semua', 'label' => 'Semua', 'jumlah' => Material::count()],
        ];

        foreach ($jenjangList as $key => $label) {
            $tabs[] = [
                'key' => $key,
                'label' => $label,
                'jumlah' => Material::forJenjang($key)->count(),
            ];
        }

        // Data dropdown formulir unggah, diambil dari database.
        $subjects = Subject::orderBy('name')->get();
        $kelas = $this->kelasAktif();

        return view('admin.manajemen-materi', compact(
            'materi',
            'kpis',
            'tabs',
            'subjects',
            'kelas'
        ));
    }

    /**
     * Simpan materi ajar baru dengan berkas yang diunggah dari admin.
     */
    public function store(Request $request): Response
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'berkas' => ['required', 'file', 'mimes:pdf,pptx,ppt,doc,docx', 'max:61440'],
            'mapel' => ['required', 'exists:subjects,id'],
            'target' => ['required', 'array'],
            'target.*' => ['required', 'exists:class_groups,id'],
            'status' => ['nullable', 'in:draf,terbit'],
            'akses' => ['boolean'],
        ]);

        $status = $validated['status'] ?? 'terbit';

        $file = $this->simpanBerkas($request->file('berkas'));

        $materi = Material::create([
            'title' => $validated['judul'],
            'description' => $validated['deskripsi'] ?? null,
            'subject_id' => $validated['mapel'],
            'file_id' => $file->id,
            'uploaded_by' => auth()->id(),
            'status' => $status,
            'allow_download' => (bool) $request->boolean('akses'),
            'published_at' => $status === 'terbit' ? now() : null,
        ]);

        $materi->classGroups()->sync($validated['target']);

        return redirect()->route('admin.manajemenMateri')->with('sukses', 'Materi ajar "'.$materi->title.'" berhasil diunggah.');
    }

    /**
     * Tampilkan formulir ubah materi ajar yang sudah tersimpan.
     */
    public function edit(Material $materi): View
    {
        $materi->load(['subject', 'file', 'classGroups']);

        return view('admin.manajemen-materi-form', [
            'materi' => $materi,
            'subjects' => Subject::orderBy('name')->get(),
            'kelas' => $this->kelasAktif(),
        ]);
    }

    /**
     * Perbarui data materi ajar yang ada.
     */
    public function update(Request $request, Material $materi): Response
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'berkas' => ['nullable', 'file', 'mimes:pdf,pptx,ppt,doc,docx', 'max:61440'],
            'mapel' => ['required', 'exists:subjects,id'],
            'target' => ['required', 'array'],
            'target.*' => ['required', 'exists:class_groups,id'],
            'akses' => ['boolean'],
        ]);

        // Status publikasi tidak diubah dari halaman edit, materi yang sudah
        // terbit tetap terbit dan draf tetap draf.
        $attributes = [
            'title' => $validated['judul'],
            'description' => $validated['deskripsi'] ?? null,
            'subject_id' => $validated['mapel'],
            'allow_download' => (bool) $request->boolean('akses'),
        ];

        // Berkas hanya diganti bila admin mengunggah berkas baru.
        $fileLama = $materi->file;

        if ($request->hasFile('berkas')) {
            $fileBaru = $this->simpanBerkas($request->file('berkas'));

            $attributes['file_id'] = $fileBaru->id;
        }

        $materi->update($attributes);

        // Berkas lama dibersihkan setelah materi memakai berkas baru.
        if ($fileLama !== null && isset($attributes['file_id']) && $fileLama->id !== $attributes['file_id']) {
            $fileLama->delete();

            if (Storage::disk('local')->exists($fileLama->path)) {
                Storage::disk('local')->delete($fileLama->path);
            }
        }

        $materi->classGroups()->sync($validated['target']);

        return redirect()->route('admin.manajemenMateri')->with('sukses', 'Materi ajar "'.$materi->title.'" berhasil diperbarui.');
    }

    /**
     * Hapus (soft delete) materi ajar dari database.
     */
    public function destroy(Material $materi): Response
    {
        $judul = $materi->title;
        $berkasPath = optional($materi->file)->path;

        $materi->delete();

        if ($berkasPath && Storage::disk('local')->exists($berkasPath)) {
            Storage::disk('local')->delete($berkasPath);
        }

        return redirect()->route('admin.manajemenMateri')->with('sukses', 'Materi ajar "'.$judul.'" berhasil dihapus.');
    }

    /**
     * Kelas aktif yang bisa dipilih sebagai target materi ajar.
     */
    private function kelasAktif(): Collection
    {
        $urutanJenjang = ['SD' => 1, 'SMP' => 2, 'SMA' => 3];

        return ClassGroup::with('academicPeriod')
            ->active()
            ->get()
            ->sortBy(fn (ClassGroup $kelas) => [
                (int) $kelas->grade,
                $urutanJenjang[$kelas->education_level] ?? 4,
                $kelas->code,
            ])
            ->values();
    }

    /**
     * Simpan berkas unggahan ke disk privat dan catat metadata-nya.
     */
    private function simpanBerkas(UploadedFile $berkas): File
    {
        $namaDisimpan = $berkas->store('materi', 'local');

        return File::create([
            'original_name' => $berkas->getClientOriginalName(),
            'path' => $namaDisimpan,
            'mime_type' => $berkas->getMimeType(),
            'size_bytes' => $berkas->getSize(),
            'uploaded_by' => auth()->id(),
        ]);
    }
}
