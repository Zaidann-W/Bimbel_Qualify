@extends('layouts.admin')

@section('title', 'Data Siswa & Kelas')

@section('content')
@php
    $statusPil = [
        'aktif' => ['label' => 'Aktif', 'kelas' => 'bg-sandgold text-bronze'],
        'cuti' => ['label' => 'Cuti', 'kelas' => 'bg-sand text-muted'],
        'nonaktif' => ['label' => 'Nonaktif', 'kelas' => 'bg-sand text-muted'],
        'lulus' => ['label' => 'Lulus', 'kelas' => 'bg-espresso text-white'],
    ];
    $avatarPil = ['bg-espresso text-gold', 'bg-sand text-bark-400'];
@endphp

{{-- Kepala halaman --}}
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">
                Data Siswa
            </h1>
            <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Database induk siswa terdaftar dan informasi kontak wali bimbel QUALIFY.
            </p>
        </div>

        <div class="flex shrink-0 flex-col gap-2.5 sm:flex-row">
            <a href="{{ route('admin.dataSiswa', array_merge(request()->query(), ['export' => 'csv'])) }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg border border-line bg-white px-4 py-3 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4 text-bronze'])
                Ekspor Data
            </a>
            <a href="{{ route('admin.dataSiswa.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-espresso px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'user-plus', 'class' => 'h-4 w-4 text-gold'])
                Tambah Siswa
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-lg border border-forest-500/30 bg-forest-50 px-4 py-3 text-[12.5px] font-semibold text-forest-600">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-ember-500/30 bg-ember-100 px-4 py-3 text-[12.5px] font-semibold text-ember-600">
            {{ $errors->first() }}
        </div>
    @endif
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Tiga angka pokok data siswa --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($kpis as $kpi)
            <article class="rounded-xl border border-line bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">{{ $kpi['label'] }}</p>
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ $kpi['tile'] }}">
                        @include('layouts.partials.icon', ['name' => $kpi['ikon'], 'class' => 'h-4.5 w-4.5'])
                    </span>
                </div>
                <p class="mt-3 flex items-baseline gap-2">
                    <span class="text-[27px] font-extrabold leading-none tracking-tight tabular-nums {{ $kpi['nilai_kelas'] }}">{{ $kpi['nilai'] }}</span>
                    <span class="text-[12.5px] font-semibold text-muted">{{ $kpi['satuan'] }}</span>
                </p>
                <p class="mt-3.5 text-[11.5px] font-medium leading-snug text-bark-700">{{ $kpi['catatan'] }}</p>
            </article>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        {{-- Daftar siswa --}}
        <section class="overflow-hidden rounded-2xl border border-line bg-white xl:col-span-2">

            {{-- Pencarian & filter --}}
            <form method="GET" action="{{ route('admin.dataSiswa') }}" class="flex flex-col gap-2.5 px-4 py-4 sm:flex-row sm:items-center sm:px-5">
                <div class="relative min-w-0 flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                        @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                    </span>
                    <input type="search" name="q" value="{{ $filters['q'] }}" aria-label="Cari nama siswa, NIS, atau nama sekolah"
                           placeholder="Cari nama siswa, NIS, atau nama sekolah..."
                           class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                </div>

                <div class="relative shrink-0 sm:w-44">
                    <select name="jenjang" aria-label="Filter jenjang sekolah" onchange="this.form.submit()"
                            class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-3 pr-9 text-[12.5px] font-semibold text-espresso transition hover:bg-sand focus:outline-none focus:ring-2 focus:ring-gold/30">
                        <option value="semua">Semua Jenjang</option>
                        @foreach (['sd' => 'Jenjang SD', 'smp' => 'Jenjang SMP', 'sma' => 'Jenjang SMA'] as $value => $label)
                            <option value="{{ $value }}" @selected($filters['jenjang'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 grid w-9 place-items-center text-bark-400">
                        @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                    </span>
                </div>

                <div class="relative shrink-0 sm:w-40">
                    <select name="status" aria-label="Filter status siswa" onchange="this.form.submit()"
                            class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-3 pr-9 text-[12.5px] font-semibold text-espresso transition hover:bg-sand focus:outline-none focus:ring-2 focus:ring-gold/30">
                        <option value="semua">Semua Status</option>
                        @foreach ($statusPil as $value => $item)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $item['label'] }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 grid w-9 place-items-center text-bark-400">
                        @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                    </span>
                </div>
            </form>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] border-collapse text-left">
                    <thead>
                        <tr class="bg-shell">
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Nama Siswa &amp; NIS</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Orang Tua / Wali</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</th>
                            <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswa as $row)
                            @php
                                $status = $statusPil[$row->status] ?? ['label' => ucfirst($row->status), 'kelas' => 'bg-sand text-muted'];
                                $guardian = $row->guardians->firstWhere('pivot.is_primary_contact', true) ?? $row->guardians->first();
                                $kelasAktif = $row->enrollments->firstWhere('status', 'aktif');
                                $inisial = \Illuminate\Support\Str::of($row->full_name)->explode(' ')->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('');
                            @endphp
                            <tr class="border-t border-line align-top odd:bg-white even:bg-shell">
                                <td class="px-5 py-4">
                                    <div class="flex items-start gap-3">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-[12.5px] font-bold {{ $avatarPil[$row->id % 2] }}" aria-hidden="true">
                                            {{ $inisial }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $row->full_name }}</p>
                                            <p class="mt-1.5 text-[11.5px] leading-snug text-muted">
                                                {{ $row->nis ?? 'NIS belum ada' }} <span class="text-bark-300">•</span> {{ $row->school_name ?? '-' }}
                                                @if ($kelasAktif)
                                                    <span class="text-bark-300">•</span> {{ $kelasAktif->classGroup?->code }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[13px] font-medium leading-snug text-ink">{{ $guardian?->name ?? '-' }}</p>
                                    <p class="mt-1.5 text-[11.5px] leading-snug text-muted">{{ $guardian?->phone_wa ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $status['kelas'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.dataSiswa.show', $row) }}"
                                           class="inline-flex shrink-0 items-center rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand">
                                            Detail
                                        </a>
                                        <a href="{{ route('admin.dataSiswa.edit', $row) }}" aria-label="Edit data {{ $row->full_name }}"
                                           class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                            @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                                        </a>
                                        <form method="POST" action="{{ route('admin.dataSiswa.destroy', $row) }}"
                                              onsubmit="return confirm('Hapus siswa {{ $row->full_name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Hapus data {{ $row->full_name }}"
                                                    class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-ember-100 hover:text-ember-600">
                                                @include('layouts.partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-line">
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <p class="text-[13.5px] font-bold text-ink">Tidak ada siswa yang cocok</p>
                                    <p class="mt-1.5 text-[12.5px] text-muted">Ubah kata kunci pencarian, pilih Semua Jenjang, atau pilih Semua Status.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Kaki tabel --}}
            <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-[12px] leading-snug text-muted">
                    Menampilkan <span class="font-bold text-ink">{{ $siswa->firstItem() ?? 0 }} – {{ $siswa->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-ink">{{ $siswa->total() }}</span> Siswa Terdaftar
                </p>

                <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman daftar siswa">
                    @if ($siswa->onFirstPage())
                        <span aria-hidden="true" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-bark-300">
                            @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 rotate-180'])
                        </span>
                    @else
                        <a href="{{ $siswa->previousPageUrl() }}" aria-label="Halaman sebelumnya"
                           class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-espresso transition hover:bg-sand">
                            @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 rotate-180'])
                        </a>
                    @endif

                    <span class="px-2 text-[12.5px] font-semibold text-bark-500">Halaman {{ $siswa->currentPage() }} / {{ $siswa->lastPage() }}</span>

                    @if ($siswa->hasMorePages())
                        <a href="{{ $siswa->nextPageUrl() }}" aria-label="Halaman berikutnya"
                           class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-espresso transition hover:bg-sand">
                            @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4'])
                        </a>
                    @else
                        <span aria-hidden="true" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-bark-300">
                            @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4'])
                        </span>
                    @endif
                </nav>
            </footer>
        </section>

        {{-- Distribusi jenjang --}}
        <section class="rounded-2xl border border-line bg-white p-5 self-start">
            <header class="flex items-center gap-2.5">
                <span class="h-2 w-2 shrink-0 rounded-full bg-gold"></span>
                <h2 class="text-[15px] font-bold leading-snug text-ink">Siswa per Jenjang</h2>
            </header>

            <div class="mt-4 space-y-4">
                @foreach ($distribusi as $bar)
                    <div>
                        <div class="flex items-baseline justify-between gap-3">
                            <p class="text-[12.5px] font-semibold leading-snug text-muted">{{ $bar['jenjang'] }}</p>
                            <p class="shrink-0 text-[12px] font-bold leading-snug tabular-nums text-ink">{{ $bar['jumlah'] }}</p>
                        </div>
                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-sand"
                             role="img" aria-label="{{ $bar['jenjang'] }} {{ $bar['jumlah'] }}">
                            <span class="block h-full rounded-full bg-gold" style="width: {{ $bar['persen'] }}%"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    {{-- Daftar kelas --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-white">
        <header class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
            <div class="flex items-center gap-2.5">
                <span class="h-2 w-2 shrink-0 rounded-full bg-bronze"></span>
                <h2 class="text-[15px] font-bold leading-snug text-ink">Kelas &amp; Rombel</h2>
                <span class="rounded-full bg-sandgold px-2 py-0.5 text-[10.5px] font-bold text-bronze">{{ $kelas->count() }} Kelas</span>
            </div>
            <a href="{{ route('admin.kelas.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-espresso px-4 py-2.5 text-[12.5px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4 text-gold'])
                Tambah Kelas
            </a>
        </header>

        <div class="overflow-x-auto border-t border-line">
            <table class="w-full min-w-[720px] text-left">
                <thead>
                    <tr class="bg-shell">
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Kode Kelas</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Jenjang / Tingkat</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Periode</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Siswa</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $rombel)
                        <tr class="border-t border-line odd:bg-white even:bg-shell">
                            <td class="px-5 py-4">
                                <p class="text-[13.5px] font-bold text-ink">{{ $rombel->code }}</p>
                                <p class="mt-1 text-[11.5px] text-muted">{{ $rombel->major ?? 'Reguler' }} • {{ $rombel->curriculum ?? 'Kurikulum standar' }}</p>
                            </td>
                            <td class="px-5 py-4 text-[12.5px] font-medium text-bark-500">{{ $rombel->education_level ?? '-' }} {{ $rombel->grade ?? '' }}</td>
                            <td class="px-5 py-4 text-[12.5px] font-medium text-bark-500">{{ $rombel->academicPeriod?->academic_year ?? '-' }} ({{ $rombel->academicPeriod?->semester ?? '-' }})</td>
                            <td class="px-5 py-4 text-[12.5px] font-bold text-ink">{{ $rombel->siswa_count }} / {{ $rombel->capacity ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $rombel->status === 'aktif' ? 'bg-sandgold text-bronze' : 'bg-sand text-muted' }}">
                                    {{ ucfirst($rombel->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.kelas.edit', $rombel) }}" aria-label="Edit kelas {{ $rombel->code }}"
                                       class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                        @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                                    </a>
                                    <form method="POST" action="{{ route('admin.kelas.destroy', $rombel) }}"
                                          onsubmit="return confirm('Hapus kelas {{ $rombel->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Hapus kelas {{ $rombel->code }}"
                                                class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-ember-100 hover:text-ember-600">
                                            @include('layouts.partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-line">
                            <td colspan="6" class="px-5 py-10 text-center text-[12.5px] text-muted">Belum ada kelas. Tambahkan rombel pertama.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
