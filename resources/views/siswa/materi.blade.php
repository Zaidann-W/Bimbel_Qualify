@extends('layouts.siswa')

@section('title', 'Materi Pembelajaran Siswa')

@section('content')
<div class="space-y-5">

    {{-- Breadcrumb + target --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <ol class="flex flex-wrap items-center gap-1.5 text-[12px] text-bark-400">
            <li><a href="{{ route('siswa.dashboard') }}" class="font-medium hover:text-ember-600">Dasbor Siswa</a></li>
            <li aria-hidden="true">›</li>
            <li class="font-semibold text-bark-700">Materi Pembelajaran</li>
        </ol>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-ember-50 px-3 py-1.5 text-[11px] font-bold text-ember-700 ring-1 ring-ember-200">
            @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'h-3.5 w-3.5'])
            Target: UTBK SNBT 2025 • Regional Intensif 12 IPA
        </span>
    </div>

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-3xl">
            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-ember-600">Pusat Repositori Akademik</p>
            <h1 class="mt-1.5 text-[22px] font-bold tracking-tight text-bark-800">Katalog & Bank Modul Belajar Digital</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Ribuan modul PDF, video pelajaran, dan bank soal berkualitas tersusun sistematis sesuai jenjang/rapor akademik dan universitas Qualify.</p>
        </div>
        <div class="flex shrink-0 gap-2">
            <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-bark-200 bg-white px-3.5 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                @include('layouts.partials.icon', ['name' => 'bookmark', 'class' => 'h-4 w-4'])
                Simpananku
            </button>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-bark-200 bg-white px-3.5 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                @include('layouts.partials.icon', ['name' => 'star', 'class' => 'h-4 w-4'])
                Rekomendasi Belajar
            </button>
        </div>
    </div>

    {{-- Banner unggulan --}}
    @if ($materials->isNotEmpty())
        @php($unggulan = $materials->first())
        <section class="rounded-2xl bg-bark-800 p-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 max-w-2xl">
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-md bg-gold px-2 py-0.5 text-[10px] font-bold text-espresso">SBMPTN/UTBK Prep</span>
                        <span class="rounded-md border border-white/20 px-2 py-0.5 text-[10px] font-bold text-bark-200">Bank Soal HOTS UTBK</span>
                        <span class="rounded-md border border-white/20 px-2 py-0.5 text-[10px] font-bold text-bark-200">Andalan Penerimaan</span>
                    </div>
                    <h2 class="mt-3 text-[19px] font-bold leading-snug text-white">{{ $unggulan->title }}</h2>
                    <p class="mt-1.5 text-[12.5px] leading-relaxed text-bark-300">{{ $unggulan->description ?? 'Modul unggulan terbaru untuk persiapan UTBK SNBT.' }}</p>
                    <div class="mt-3 flex flex-wrap gap-4 text-[11px] text-bark-300">
                        <span class="inline-flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'play', 'class' => 'h-3.5 w-3.5 text-ember-400'])
                            Konten: {{ $unggulan->file ? 'PDF' : 'Materi' }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('siswa.materi') }}" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[12.5px] font-bold text-espresso transition hover:brightness-95">
                    @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'h-4 w-4'])
                    Baca Modul Cepat (GRATIS)
                </a>
            </div>
        </section>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="rounded-2xl border border-bark-100 bg-white p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">{{ $stat['label'] }}</p>
                        <div class="mt-1.5 flex items-baseline gap-1.5">
                            <span class="text-[26px] font-bold leading-none tracking-tight text-bark-800">{{ $stat['value'] }}</span>
                            <span class="text-[11.5px] font-medium text-bark-400">{{ $stat['unit'] }}</span>
                        </div>
                    </div>
                    <div @class(['grid h-9 w-9 place-items-center rounded-xl', $stat['tone']])>
                        @include('layouts.partials.icon', ['name' => $stat['icon'], 'class' => 'h-[18px] w-[18px]'])
                    </div>
                </div>
                <p class="mt-3 text-[10.5px] text-bark-400">{{ $stat['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <section class="rounded-2xl border border-bark-100 bg-white p-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-[220px] flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                <input type="search" placeholder="Cari judul modul, bab, atau materi…"
                       class="w-full rounded-lg border border-bark-200 bg-white py-2.5 pl-9 pr-3 text-[13px] text-bark-800 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
            </div>
            <select class="rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[12.5px] font-medium text-bark-600 focus:border-ember-300 focus:outline-none">
                <option>Semua Mata Pelajaran</option>
                <option>Matematika</option>
                <option>Fisika</option>
                <option>Kimia</option>
                <option>Biologi</option>
                <option>TPS</option>
            </select>
            <select class="rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[12.5px] font-medium text-bark-600 focus:border-ember-300 focus:outline-none">
                <option>Urutan Terbaru</option>
                <option>Urutan Terlama</option>
                <option>Paling Banyak Dibaca</option>
            </select>
            <button type="button" class="rounded-lg border border-bark-200 bg-white px-3.5 py-2.5 text-[12.5px] font-semibold text-bark-500 transition hover:bg-bark-50">
                Reset
            </button>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <span class="mr-1 text-[10px] font-bold uppercase tracking-wide text-bark-400">Jenjang</span>
            @foreach ($jenjang as $j)
                @php($active = $j === $activeJenjang)
                <button type="button"
                        @class([
                            'rounded-lg px-3 py-1.5 text-[12px] font-semibold transition',
                            'bg-ember-400 text-bark-900' => $active,
                            'border border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => ! $active,
                        ])>{{ $j }}</button>
            @endforeach
        </div>
    </section>

    {{-- Grid modul --}}
    @if ($materials->isNotEmpty())
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($materials as $m)
                <article class="flex flex-col rounded-2xl border border-bark-100 bg-white p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-wrap gap-1.5">
                            @if ($m->subject)
                                <span class="rounded-md bg-bark-100 px-2 py-0.5 text-[9.5px] font-bold text-bark-500">{{ $m->subject->name }}</span>
                            @endif
                            @if ($m->published_at && $m->published_at->gt(now()->subDays(7)))
                                <span class="rounded-md bg-gold px-2 py-0.5 text-[9.5px] font-bold text-espresso">Baru</span>
                            @endif
                        </div>
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ember-50 text-ember-600">
                            @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'h-4 w-4'])
                        </div>
                    </div>

                    <p class="mt-3 text-[9.5px] font-bold uppercase tracking-wide text-ember-600">{{ $m->subject?->name ?? 'Mata Pelajaran' }}</p>
                    <h3 class="mt-1 text-[14px] font-bold leading-snug text-bark-800">{{ $m->title }}</h3>
                    <p class="mt-1.5 line-clamp-2 flex-1 text-[12px] leading-relaxed text-bark-400">{{ $m->description ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="mt-3 flex items-center justify-between gap-2 border-t border-bark-100 pt-3">
                        <p class="text-[11px] text-bark-500">Tentor: <span class="font-semibold text-bark-700">{{ $m->uploader?->name ?? '-' }}</span></p>
                        <p class="shrink-0 text-[10.5px] text-bark-400">{{ $m->published_at?->locale('id')->translatedFormat('j M Y') ?? '-' }}</p>
                    </div>

                    <a href="{{ route('siswa.materi') }}" class="mt-3 w-full rounded-lg bg-bark-800 px-3 py-2.5 text-[12px] font-bold text-white transition hover:bg-bark-900">
                        Baca Modul
                    </a>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-[11.5px] text-bark-400">Menampilkan {{ $materials->firstItem() }} – {{ $materials->lastItem() }} dari {{ $materials->total() }} hasil modul</p>
            <div class="flex items-center gap-1.5">
                {{ $materials->links() }}
            </div>
        </div>
    @else
        <p class="rounded-xl border border-dashed border-bark-100 px-4 py-8 text-center text-[12px] text-bark-400">
            Belum ada modul yang dirilis untuk kelasmu.
        </p>
    @endif

    {{-- Hak cipta --}}
    <section class="flex items-start gap-3 rounded-2xl border border-ember-200 bg-ember-50 px-5 py-4">
        @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
        <div>
            <p class="text-[13px] font-bold text-bark-800">Perlindungan Hak Cipta & Hak Use</p>
            <p class="mt-1 text-[11.5px] leading-relaxed text-bark-500">Seluruh materi & modul milik QUALIFY dilindungi undang-undang dan hanya boleh dibaca bersifat internal untuk peserta ujian pribadi saja, ya! Dilarang keras menyebarluaskan materi kecuali dalam grup belajar kelas terkontrol.</p>
        </div>
    </section>
</div>
@endsection