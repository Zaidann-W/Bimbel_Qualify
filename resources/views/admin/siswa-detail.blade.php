@extends('layouts.admin')

@section('title', 'Detail Siswa')

@section('content')
@php
    $statusPil = [
        'aktif' => ['label' => 'Aktif', 'kelas' => 'bg-sandgold text-bronze'],
        'cuti' => ['label' => 'Cuti', 'kelas' => 'bg-sand text-muted'],
        'nonaktif' => ['label' => 'Nonaktif', 'kelas' => 'bg-sand text-muted'],
        'lulus' => ['label' => 'Lulus', 'kelas' => 'bg-espresso text-white'],
    ];
    $status = $statusPil[$student->status] ?? ['label' => ucfirst($student->status), 'kelas' => 'bg-sand text-muted'];
    $guardian = $student->guardians->firstWhere('pivot.is_primary_contact', true) ?? $student->guardians->first();
    $kelasAktif = $student->enrollments->firstWhere('status', 'aktif');
    $inisial = \Illuminate\Support\Str::of($student->full_name)->explode(' ')->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('');
    $totalTagihan = $student->invoices->sum('amount');
    $totalLunas = $student->invoices->where('status', 'lunas')->sum('amount');
    $detail = [
        'NIS' => $student->nis,
        'NISN' => $student->nisn,
        'Nama Panggilan' => $student->nickname,
        'Tempat Lahir' => $student->birth_place,
        'Tanggal Lahir' => $student->birth_date?->translatedFormat('d F Y'),
        'Email' => $student->email,
        'WhatsApp' => $student->phone_wa,
        'Alamat' => $student->address,
        'Jenjang' => $student->education_level,
        'Tingkat' => $student->school_grade,
        'Sekolah' => $student->school_name,
        'Jurusan' => $student->major,
        'Kurikulum' => $student->curriculum,
    ];
@endphp

<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <nav class="flex items-center gap-1.5 text-[11.5px] font-semibold text-bark-400" aria-label="Breadcrumb">
        <a href="{{ route('admin.dataSiswa') }}" class="hover:text-bronze">Data Siswa</a>
        <span aria-hidden="true">/</span>
        <span class="text-ink">Detail</span>
    </nav>

    <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-3.5">
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-espresso text-[17px] font-bold text-gold" aria-hidden="true">
                {{ $inisial }}
            </span>
            <div>
                <h1 class="text-[26px] font-extrabold leading-tight tracking-tight text-ink">{{ $student->full_name }}</h1>
                <p class="mt-1.5 text-[12.5px] leading-snug text-muted">
                    {{ $student->nis ?? 'NIS belum ada' }} <span class="text-bark-300">•</span>
                    {{ $student->education_level }} {{ $student->school_grade }}
                    <span class="text-bark-300">•</span>
                    {{ $kelasAktif?->classGroup?->code ?? 'Belum ada kelas' }}
                </p>
                <span class="mt-2.5 inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $status['kelas'] }}">{{ $status['label'] }}</span>
            </div>
        </div>

        <div class="flex shrink-0 flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.dataSiswa') }}"
               class="rounded-lg border border-line bg-white px-4 py-2.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand">
                Kembali
            </a>
            <a href="{{ route('admin.dataSiswa.edit', $student) }}"
               class="inline-flex items-center gap-2 rounded-lg bg-espresso px-4 py-2.5 text-[12.5px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4 text-gold'])
                Edit Data
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-lg border border-forest-500/30 bg-forest-50 px-4 py-3 text-[12.5px] font-semibold text-forest-600">
            {{ session('status') }}
        </div>
    @endif
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        <section class="rounded-2xl border border-line bg-white p-5 xl:col-span-2">
            <h2 class="text-[15px] font-bold text-ink">Identitas Siswa</h2>
            <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                @foreach ($detail as $label => $value)
                    <div>
                        <dt class="text-[10.5px] font-bold uppercase tracking-[0.1em] text-muted">{{ $label }}</dt>
                        <dd class="mt-1 text-[13px] font-medium leading-snug text-ink">{{ filled($value) ? $value : '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <div class="space-y-5">
            <section class="rounded-2xl border border-line bg-white p-5">
                <h2 class="text-[15px] font-bold text-ink">Orang Tua / Wali</h2>
                @if ($guardian)
                    <p class="mt-3 text-[13.5px] font-bold text-ink">{{ $guardian->name }}</p>
                    <p class="mt-1 text-[12.5px] text-muted">{{ $guardian->phone_wa ?? 'Nomor belum ada' }}</p>
                    <p class="mt-1 text-[11.5px] uppercase tracking-[0.08em] text-bark-400">{{ $guardian->pivot->relationship ?? 'wali' }}</p>
                @else
                    <p class="mt-3 text-[12.5px] text-muted">Belum ada data wali. Tambahkan melalui Edit Data.</p>
                @endif
            </section>

            <section class="rounded-2xl border border-line bg-white p-5">
                <h2 class="text-[15px] font-bold text-ink">Ringkasan Tagihan</h2>
                <div class="mt-3 space-y-2.5 text-[12.5px]">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-muted">Total Tagihan</span>
                        <span class="font-bold tabular-nums text-ink">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-muted">Sudah Lunas</span>
                        <span class="font-bold tabular-nums text-forest-600">Rp {{ number_format($totalLunas, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-line pt-2.5">
                        <span class="text-muted">Jumlah Invoice</span>
                        <span class="font-bold tabular-nums text-ink">{{ $student->invoices->count() }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-line bg-white">
        <header class="px-5 py-4">
            <h2 class="text-[15px] font-bold text-ink">Riwayat Kelas</h2>
        </header>
        <div class="overflow-x-auto border-t border-line">
            <table class="w-full min-w-[560px] text-left">
                <thead>
                    <tr class="bg-shell">
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Kelas</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Periode</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Mulai</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Selesai</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($student->enrollments->sortByDesc('started_on') as $enrollment)
                        <tr class="border-t border-line odd:bg-white even:bg-shell">
                            <td class="px-5 py-3.5 text-[12.5px] font-semibold text-ink">{{ $enrollment->classGroup?->code ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-[12.5px] text-bark-500">{{ $enrollment->classGroup?->academicPeriod?->academic_year ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-[12.5px] text-bark-500">{{ $enrollment->started_on?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-[12.5px] text-bark-500">{{ $enrollment->ended_on?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $enrollment->status === 'aktif' ? 'bg-sandgold text-bronze' : 'bg-sand text-muted' }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-line">
                            <td colspan="5" class="px-5 py-10 text-center text-[12.5px] text-muted">Belum ada riwayat kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
