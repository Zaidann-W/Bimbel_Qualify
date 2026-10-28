<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\QualifixTicket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function materi(Request $request): View
    {
        $student = $request->user()->student;
        $classGroupId = $student?->enrollments()->where('status', 'aktif')->value('class_group_id');

        $materials = Material::query()
            ->with(['subject', 'file'])
            ->where('status', 'terbit')
            ->when($classGroupId !== null, fn ($q) => $q->whereHas(
                'classGroups',
                fn ($q) => $q->where('class_groups.id', $classGroupId),
            ))
            ->orderByDesc('published_at')
            ->paginate(12);

        $stats = [
            ['label' => 'Modul Tersedia', 'value' => (string) $materials->total(), 'unit' => 'Modul', 'sub' => 'Terverifikasi kurikulum', 'icon' => 'book-open', 'tone' => 'bg-ember-50 text-ember-600'],
            ['label' => 'Latihan Mengerjakan', 'value' => '0', 'unit' => 'Paket', 'sub' => 'Bank soal tiap pekan', 'icon' => 'check-circle', 'tone' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Video Penjelasan', 'value' => '0', 'unit' => 'Video', 'sub' => 'Ringkasan animatif', 'icon' => 'presentation', 'tone' => 'bg-bark-50 text-bark-500'],
            ['label' => 'Relevan Skor UTBK', 'value' => '0%', 'unit' => 'Rekomendasi UTBK', 'sub' => 'Selaras kisi-kisi SNBT', 'icon' => 'star', 'tone' => 'bg-ember-50 text-ember-600'],
        ];

        $jenjang = ['Semua bentuk', 'Kelas 12 X/UTBK', 'Kelas 11 SMA', 'Kelas 10 SMA', 'SMP/SD'];
        $activeJenjang = $student?->classGroup?->grade === '12' ? 'Kelas 12 X/UTBK' : 'Semua bentuk';

        return view('siswa.materi', compact('materials', 'stats', 'jenjang', 'activeJenjang'));
    }

    public function qualifix(Request $request): View
    {
        $student = $request->user()->student;
        $studentId = $student?->id;

        $tickets = $studentId !== null
            ? QualifixTicket::query()->where('student_id', $studentId)
            : QualifixTicket::query()->whereRaw('1=0');

        $stats = [
            ['label' => 'Total Diajukan', 'value' => (clone $tickets)->count(), 'unit' => 'Soal', 'icon' => 'file', 'tone' => 'bg-bark-100 text-bark-600', 'active' => false],
            ['label' => 'Menunggu Jawaban', 'value' => (clone $tickets)->whereIn('status', ['menunggu_tentor', 'sedang_dibahas'])->count(), 'unit' => 'Pertanyaan', 'icon' => 'clock', 'tone' => 'bg-ember-100 text-ember-600', 'active' => false],
            ['label' => 'Sudah Dijawab', 'value' => (clone $tickets)->where('status', 'dijawab')->count(), 'unit' => 'Tersedia', 'icon' => 'message-circle', 'tone' => 'bg-bark-100 text-bark-600', 'active' => false],
            ['label' => 'Selesai / Dipahami', 'value' => (clone $tickets)->where('status', 'selesai')->count(), 'unit' => 'Arsip', 'icon' => 'check-circle', 'tone' => 'bg-emerald-50 text-emerald-600', 'active' => false],
        ];

        $tabs = [
            ['label' => 'Semua', 'count' => $stats[0]['value'], 'active' => true],
            ['label' => 'Menunggu', 'count' => $stats[1]['value'], 'active' => false],
            ['label' => 'Dijawab', 'count' => $stats[2]['value'], 'active' => false],
            ['label' => 'Selesai', 'count' => $stats[3]['value'], 'active' => false],
        ];

        $soal = $tickets->with(['subject', 'student'])->orderByDesc('created_at')->paginate(10);

        return view('siswa.qualifix', compact('stats', 'tabs', 'soal'));
    }

    public function profil(Request $request): View
    {
        $student = $request->user()->student;

        abort_if($student === null, 403, 'Akun ini belum terhubung ke data siswa.');

        $classGroup = $student->enrollments()->where('status', 'aktif')->first()?->classGroup;
        $guardian = $student->guardians()->wherePivot('is_primary_contact', true)->first();

        $dataDiri = [
            ['label' => 'Nama Lengkap', 'value' => $student->full_name, 'slug' => 'nama-lengkap'],
            ['label' => 'Nama Panggilan', 'value' => $student->nickname ?? '-', 'slug' => 'nama-panggilan'],
            ['label' => 'Asal Sekolah', 'value' => $student->school_name ?? '-', 'slug' => 'asal-sekolah'],
            ['label' => 'Tanggal Lahir', 'value' => $student->birth_date?->locale('id')->translatedFormat('j F Y') ?? '-', 'slug' => 'tanggal-lahir'],
            ['label' => 'No. WhatsApp Siswa', 'value' => $student->phone_wa ?? '-', 'slug' => 'no-whatsapp-siswa'],
            ['label' => 'Alamat Domisili', 'value' => $student->address ?? '-', 'slug' => 'alamat-domisili'],
        ];

        return view('siswa.profil', compact('student', 'classGroup', 'guardian', 'dataDiri'));
    }
}
