@extends('layouts.tentor')

@section('title', 'Materi Pembelajaran')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Bank Materi &amp; Bahan Ajar</h1>
            <p class="mt-1 text-[13px] text-bark-400">Repositori modul kurikulum riset dan bank tayang ajar kelas terverifikasi. Terstandarisasi untuk kegiatan belajar mengajar.</p>
        </div>
        <div class="flex max-w-xs items-start gap-2.5 rounded-xl border border-ember-200 bg-ember-50 px-4 py-3">
            @include('layouts.partials.icon', ['name' => 'info', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
            <div>
                <p class="text-[12.5px] font-bold text-bark-700">Mode Baca Langsung</p>
                <p class="mt-0.5 text-[11.5px] leading-relaxed text-bark-500">Bahan ajar dapat dibuka &amp; ditayangkan langsung di layar kelas.</p>
            </div>
        </div>
    </div>

    {{-- Stat cards (Sesuai Database) --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($stats as [$label, $value, $unit, $sub, $icon, $tone])
            <div class="relative overflow-hidden rounded-2xl border border-bark-100 bg-white p-5 shadow-xs">
                <div class="absolute inset-x-0 top-0 h-1 bg-ember-400"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="text-[28px] font-bold leading-none tracking-tight text-bark-800">{{ $value }}</span>
                            <span class="text-[12px] font-medium text-bark-400">{{ $unit }}</span>
                        </div>
                    </div>
                    <div @class(['grid h-9 w-9 place-items-center rounded-xl', $tone])>
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-[18px] w-[18px]'])
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-bark-400">{{ $sub }}</p>
            </div>
        @endforeach
    </div>

    {{-- Pencarian + filter --}}
    <section class="rounded-2xl border border-bark-100 bg-white p-4 shadow-xs">
        <form method="GET" action="{{ route('tentor.materi') }}" class="space-y-4">
            {{-- Pertahankan filter aktif saat melakukan submit search bar --}}
            @if ($activeJenjang !== 'semua')
                <input type="hidden" name="jenjang" value="{{ $activeJenjang }}">
            @endif
            @if ($activeKategori !== 'semua')
                <input type="hidden" name="kategori" value="{{ $activeKategori }}">
            @endif

            <div class="flex flex-wrap items-center gap-3">
                {{-- Input Pencarian --}}
                <div class="relative min-w-[220px] flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul modul, bab, atau materi…"
                           class="w-full rounded-lg border border-bark-200 bg-white py-2.5 pl-9 pr-3 text-[13px] text-bark-800 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                </div>

                {{-- Dropdown Mapel --}}
                <select name="mapel" onchange="this.form.submit()"
                        class="rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[12.5px] font-medium text-bark-600 focus:border-ember-300 focus:outline-none">
                    <option value="semua">Semua Mata Pelajaran</option>
                    @foreach ($subjects as $s)
                        <option value="{{ $s->id }}" @selected((string)request('mapel') === (string)$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>

                {{-- Tombol Pencarian --}}
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-bark-800 px-4 py-2.5 text-[12.5px] font-bold text-white transition hover:bg-bark-900">
                    @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                    <span>Cari</span>
                </button>

                @if (request()->filled('q') || (request()->filled('mapel') && request('mapel') !== 'semua') || $activeJenjang !== 'semua' || $activeKategori !== 'semua')
                    <a href="{{ route('tentor.materi') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-bark-50 px-3 py-2.5 text-[12px] font-medium text-bark-600 transition hover:bg-bark-100" title="Reset semua filter">
                        @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-3.5 w-3.5'])
                        <span>Reset</span>
                    </a>
                @endif
            </div>

            <div class="space-y-3 pt-1 border-t border-bark-100/60">
                {{-- Filter Jenjang Kelas --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="mr-1 text-[10px] font-bold uppercase tracking-wide text-bark-400">Jenjang Kelas</span>
                    @foreach ($jenjangList as $j)
                        @php
                            $isActive = $activeJenjang === $j['key'];
                            $params = array_filter([
                                'q' => request('q'),
                                'mapel' => request('mapel') && request('mapel') !== 'semua' ? request('mapel') : null,
                                'jenjang' => $j['key'] !== 'semua' ? $j['key'] : null,
                                'kategori' => $activeKategori !== 'semua' ? $activeKategori : null,
                            ]);
                        @endphp
                        <a href="{{ route('tentor.materi', $params) }}"
                           @class([
                               'rounded-lg px-3 py-1.5 text-[12px] font-semibold transition',
                               'bg-bark-800 text-white' => $isActive,
                               'border border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => ! $isActive,
                           ])>
                            {{ $j['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Filter Kategori --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="mr-1 text-[10px] font-bold uppercase tracking-wide text-bark-400">Kategori</span>
                    @foreach ($kategoriList as $k)
                        @php
                            $isActive = $activeKategori === $k['key'];
                            $params = array_filter([
                                'q' => request('q'),
                                'mapel' => request('mapel') && request('mapel') !== 'semua' ? request('mapel') : null,
                                'jenjang' => $activeJenjang !== 'semua' ? $activeJenjang : null,
                                'kategori' => $k['key'] !== 'semua' ? $k['key'] : null,
                            ]);
                        @endphp
                        <a href="{{ route('tentor.materi', $params) }}"
                           @class([
                               'rounded-lg px-3 py-1.5 text-[12px] font-semibold transition',
                               'bg-bark-800 text-white' => $isActive,
                               'border border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => ! $isActive,
                           ])>
                            {{ $k['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </form>
    </section>

    {{-- Grid materi (Susunan tiap kotak seragam) --}}
    @if ($materials->isNotEmpty())
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($materials as $m)
                @php
                    $isSlide = str_contains(strtolower($m->title), 'slide') ||
                               str_contains(strtolower($m->title), 'presentasi') ||
                               str_contains(strtolower(optional($m->file)->mime_type ?? ''), 'presentation') ||
                               str_contains(strtolower(optional($m->file)->original_name ?? ''), '.ppt');

                    $isLatihan = str_contains(strtolower($m->title), 'latihan') ||
                                 str_contains(strtolower($m->title), 'soal') ||
                                 str_contains(strtolower($m->title), 'paket') ||
                                 str_contains(strtolower($m->title), 'try out') ||
                                 str_contains(strtolower($m->title), 'tryout');

                    $badgeLabel = $isSlide ? 'Slide Presentasi' : ($isLatihan ? 'Paket Latihan' : 'Modul Pembelajaran');
                    $badgeClass = $isSlide ? 'bg-bark-100 text-bark-700' : ($isLatihan ? 'bg-emerald-50 text-emerald-700' : 'bg-ember-50 text-ember-700');

                    $subInfo = $m->class_codes ? ($m->class_codes . ' • ' . ($m->subject?->name ?? 'Umum')) : ($m->subject?->name ?? 'Materi Umum');
                @endphp
                <article class="flex flex-col justify-between h-full rounded-2xl border border-bark-100 bg-white p-5 shadow-xs transition hover:border-bark-200 hover:shadow-sm">
                    <div>
                        {{-- Chip Baris Atas --}}
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex max-w-[65%] items-center truncate rounded-md bg-bark-100 px-2.5 py-1 text-[10.5px] font-bold text-bark-600" title="{{ $subInfo }}">
                                {{ $subInfo }}
                            </span>
                            <span class="inline-flex shrink-0 items-center rounded-md px-2 py-0.5 text-[10.5px] font-bold {{ $badgeClass }}">
                                {{ $badgeLabel }}
                            </span>
                        </div>

                        {{-- Judul Materi (Tinggi seragam 2 baris) --}}
                        <h3 class="mt-3 min-h-[42px] text-[14.5px] font-bold leading-snug text-bark-800 line-clamp-2" title="{{ $m->title }}">
                            {{ $m->title }}
                        </h3>

                        {{-- Deskripsi (Tinggi seragam 2 baris) --}}
                        <p class="mt-1.5 min-h-[36px] text-[12px] leading-relaxed text-bark-400 line-clamp-2" title="{{ $m->description }}">
                            {{ $m->description ?: 'Bahan ajar dan modul pendalaman materi kurikulum terverifikasi.' }}
                        </p>
                    </div>

                    {{-- Footer Info & Action Button (Tersusun rapi di bagian bawah) --}}
                    <div class="mt-4 flex flex-col gap-3 border-t border-bark-100/70 pt-3">
                        <div class="flex items-center justify-between text-[11px] text-bark-400">
                            <span class="inline-flex items-center gap-1.5 truncate">
                                @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5 shrink-0'])
                                <span>Rilis {{ $m->published_at ? $m->published_at->format('d M Y') : $m->created_at->format('d M Y') }}</span>
                            </span>
                            <span class="inline-flex shrink-0 items-center rounded-md bg-bark-50 px-2 py-0.5 text-[10px] font-semibold text-bark-600">
                                {{ $m->file_type }} • {{ $m->formatted_size }}
                            </span>
                        </div>

                        @if ($m->file)
                            <a href="{{ route('tentor.materi.buka', $m->id) }}" target="_blank"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-bark-800 px-3 py-2.5 text-[12px] font-bold text-white transition hover:bg-bark-900">
                                @include('layouts.partials.icon', ['name' => $isSlide ? 'presentation' : 'book-open', 'class' => 'h-3.5 w-3.5'])
                                <span>{{ $isSlide ? 'Buka Slide Presentasi' : 'Buka Materi' }}</span>
                            </a>
                        @else
                            <button type="button" disabled
                                    class="inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-lg bg-bark-100 px-3 py-2.5 text-[12px] font-semibold text-bark-400">
                                @include('layouts.partials.icon', ['name' => 'alert-circle', 'class' => 'h-3.5 w-3.5'])
                                <span>Berkas Belum Tersedia</span>
                            </button>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination (Maksimal 10 materi per halaman) --}}
        @if ($materials->hasPages())
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-bark-100 bg-white px-5 py-3 shadow-xs">
                <p class="text-[12px] text-bark-500">
                    Menampilkan <span class="font-bold text-bark-700">{{ $materials->firstItem() }}</span> – <span class="font-bold text-bark-700">{{ $materials->lastItem() }}</span> dari <span class="font-bold text-bark-700">{{ $materials->total() }}</span> materi
                </p>
                <div>
                    {{ $materials->links() }}
                </div>
            </div>
        @endif
    @else
        <div class="rounded-2xl border border-dashed border-bark-200 bg-white p-12 text-center">
            <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-2xl bg-bark-50 text-bark-400">
                @include('layouts.partials.icon', ['name' => 'file-text', 'class' => 'h-6 w-6'])
            </div>
            <h3 class="text-[15px] font-bold text-bark-800">Tidak ada materi ditemukan</h3>
            <p class="mt-1 text-[12.5px] text-bark-400">Coba ubah kata kunci pencarian atau sesuaikan filter jenjang dan kategori.</p>
            <a href="{{ route('tentor.materi') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-bark-800 px-4 py-2 text-[12px] font-semibold text-white transition hover:bg-bark-900">
                Reset Semua Filter
            </a>
        </div>
    @endif

</div>
@endsection
