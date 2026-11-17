@extends('layouts.tentor')

@section('title', 'Materi Pembelajaran')

@section('content')
<div class="space-y-5">

    {{-- Breadcrumb --}}
    <ol class="flex flex-wrap items-center gap-1.5 text-[12px] text-bark-400">
        <li><a href="{{ route('tentor.dashboard') }}" class="font-medium hover:text-ember-600">Dasbor Tentor</a></li>
        <li aria-hidden="true">›</li>
        <li class="font-semibold text-bark-700">Materi Pembelajaran</li>
    </ol>

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Bank Materi &amp; Bahan Ajar</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Modul ajar terbit untuk kelas yang Anda ampu. Berkas dapat dibuka langsung di kelas atau diunduh bila diizinkan.</p>
        </div>
        <div class="flex max-w-xs items-start gap-2.5 rounded-xl border border-ember-200 bg-ember-50 px-4 py-3">
            @include('layouts.partials.icon', ['name' => 'info', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
            <div>
                <p class="text-[12.5px] font-bold text-bark-700">Modul per Kelas</p>
                <p class="mt-0.5 text-[11.5px] leading-relaxed text-bark-500">Daftar mengikuti kelas tempat Anda mengajar</p>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($stats as $stat)
            <div class="relative overflow-hidden rounded-2xl border border-bark-100 bg-white p-5">
                <div class="absolute inset-x-0 top-0 h-1 bg-ember-400"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">{{ $stat['label'] }}</p>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="text-[28px] font-bold leading-none tracking-tight text-bark-800">{{ $stat['value'] }}</span>
                            <span class="text-[12px] font-medium text-bark-400">{{ $stat['unit'] }}</span>
                        </div>
                    </div>
                    <div @class(['grid h-9 w-9 place-items-center rounded-xl', $stat['tone']])>
                        @include('layouts.partials.icon', ['name' => $stat['icon'], 'class' => 'h-[18px] w-[18px]'])
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-bark-400">{{ $stat['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <section class="rounded-2xl border border-bark-100 bg-white p-4">
        <form method="GET" action="{{ route('tentor.materi') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-[220px] flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-4-4"/></svg>
                <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari judul modul, bab, atau materi…"
                       class="w-full rounded-lg border border-bark-200 bg-white py-2.5 pl-9 pr-3 text-[13px] text-bark-800 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
            </div>

            <select name="subject_id" class="rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[12.5px] font-medium text-bark-600 focus:border-ember-300 focus:outline-none">
                <option value="">Semua Mata Pelajaran</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected($filters['subject_id'] === $subject->id)>{{ $subject->name }}</option>
                @endforeach
            </select>

            <select name="class_group_id" class="rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[12.5px] font-medium text-bark-600 focus:border-ember-300 focus:outline-none">
                <option value="">Semua Kelas</option>
                @foreach ($classGroups as $kelas)
                    <option value="{{ $kelas->id }}" @selected($filters['class_group_id'] === $kelas->id)>{{ $kelas->code }}</option>
                @endforeach
            </select>

            <button type="submit" class="rounded-lg bg-bark-800 px-3.5 py-2.5 text-[12.5px] font-semibold text-white transition hover:bg-bark-900">
                Terapkan
            </button>

            @if ($filters['q'] !== '' || $filters['subject_id'] !== null || $filters['class_group_id'] !== null)
                <a href="{{ route('tentor.materi') }}" class="rounded-lg border border-bark-200 bg-white px-3.5 py-2.5 text-[12.5px] font-semibold text-bark-500 transition hover:bg-bark-50">
                    Reset
                </a>
            @endif
        </form>
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
                        <span @class([
                            'rounded-md px-2 py-0.5 text-[9.5px] font-bold',
                            'bg-sand text-muted' => $m->allow_download,
                            'bg-sandgold text-bronze' => ! $m->allow_download,
                        ])>{{ $m->access_label }}</span>
                    </div>

                    <h3 class="mt-3 text-[14px] font-bold leading-snug text-bark-800">{{ $m->title }}</h3>
                    <p class="mt-1.5 line-clamp-2 flex-1 text-[12px] leading-relaxed text-bark-400">{{ $m->description ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-1.5">
                        @foreach ($m->classGroups as $kelas)
                            <span class="rounded-md bg-bark-50 px-2 py-0.5 text-[10px] font-bold text-bark-500">{{ $kelas->code }}</span>
                        @endforeach
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-2 border-t border-bark-100 pt-3">
                        <p class="inline-flex items-center gap-1.5 text-[11px] text-bark-500">
                            @include('layouts.partials.icon', ['name' => $m->file_icon, 'class' => 'h-3.5 w-3.5 text-bark-400'])
                            {{ $m->file_type }}
                        </p>
                        <p class="shrink-0 text-[10.5px] text-bark-400">{{ $m->formatted_date }}</p>
                    </div>

                    @if ($m->allow_download)
                        <a href="{{ route('tentor.materi.unduh', $m) }}"
                           class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-bark-800 px-3 py-2.5 text-[12px] font-bold text-white transition hover:bg-bark-900">
                            @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'h-3.5 w-3.5'])
                            Buka / Unduh Modul
                        </a>
                    @else
                        <span class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-lg border border-bark-200 bg-bark-50 px-3 py-2.5 text-[12px] font-bold text-bark-500">
                            @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4'])
                            Hanya Baca di Kelas
                        </span>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-[11.5px] text-bark-400">Menampilkan {{ $materials->firstItem() }} – {{ $materials->lastItem() }} dari {{ $materials->total() }} modul</p>
            <div class="flex items-center gap-1.5">
                {{ $materials->links() }}
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-bark-100 bg-bark-50/50 p-12 text-center">
            <p class="text-[12px] text-bark-400">Belum ada modul ajar untuk kelas yang Anda ampu.</p>
        </div>
    @endif

</div>
@endsection
