@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-5">

    {{-- Hero sapaan --}}
    <section class="rounded-2xl bg-espresso px-6 py-5">
        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.1em] text-bark-200">
            <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
            {{ $kelas }}
        </span>
        <h1 class="mt-3.5 flex w-fit items-center gap-2 text-[20px] font-bold tracking-tight text-white">
            Halo, {{ $student?->nickname ?? $student?->full_name ?? auth()->user()->name }}! Semangat belajar hari ini
            @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'h-5 w-5 text-gold'])
        </h1>
    </section>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($stats as $stat)
            <div class="rounded-2xl border border-bark-100 bg-white p-4">
                <div class="flex items-start justify-between">
                    @if ($stat['chip'])
                        <span class="rounded-md bg-emerald-50 px-2 py-1 text-[10.5px] font-bold text-emerald-700">{{ $stat['label'] }}</span>
                    @else
                        <p class="text-[11.5px] font-semibold text-bark-400">{{ $stat['label'] }}</p>
                    @endif
                    <div @class(['grid h-8 w-8 place-items-center rounded-lg', $stat['icon_tone']])>
                        @include('layouts.partials.icon', ['name' => $stat['icon'], 'class' => 'h-4 w-4'])
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <span class="text-[24px] font-bold leading-none tracking-tight text-bark-800">{{ $stat['value'] }}</span>
                    <span @class(['text-[11.5px]', $stat['sub_tone']])>{{ $stat['sub'] }}</span>
                </div>
                @if ($stat['footer_kind'] === 'link')
                    <a href="{{ $stat['href'] }}" class="mt-3 inline-block text-[11px] font-bold text-ember-600 hover:underline">{{ $stat['footer'] }}</a>
                @elseif ($stat['footer_kind'] === 'duo')
                    <div class="mt-3 flex gap-2 text-[11px] font-bold">
                        <a href="{{ $stat['href'] }}" class="text-emerald-600 hover:underline">{{ $stat['footer'] }}</a>
                        <span class="text-bark-200">•</span>
                        <span class="text-amber-600">{{ $stat['footer_extra'] }}</span>
                    </div>
                @else
                    <p class="mt-3 text-[11px] font-medium text-bark-400">{{ $stat['footer'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-[1fr_300px]">

        {{-- Jadwal pekan ini --}}
        <section class="rounded-2xl border border-bark-100 bg-white p-5">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <h2 class="text-[15px] font-bold text-bark-800">Jadwal Belajar Pekan Ini</h2>
                    <p class="mt-0.5 text-[11.5px] text-bark-400">{{ $periode }}</p>
                </div>
                <a href="{{ route('siswa.jadwal') }}" class="text-[12px] font-bold text-ember-600 hover:underline">Kalender Lengkap →</a>
            </div>

            <div class="mt-4 space-y-2.5">
                @forelse ($jadwal as $sesi)
                    <div @class([
                        'relative flex flex-wrap items-center gap-3 rounded-xl border p-3 pr-4',
                        'border-gold bg-gold/5' => $sesi['hari_ini'],
                        'border-bark-100' => ! $sesi['hari_ini'],
                    ])>
                        @if ($sesi['hari_ini'])
                            <span class="absolute right-3 top-3 rounded-md bg-gold px-2 py-0.5 text-[9.5px] font-bold text-espresso">Hari Ini</span>
                        @endif
                        <div @class([
                            'grid h-12 w-12 shrink-0 place-items-center rounded-xl',
                            'bg-gold text-espresso' => $sesi['hari_ini'],
                            'bg-bark-50 text-bark-500' => ! $sesi['hari_ini'],
                        ])>
                            <span class="text-[9px] font-bold uppercase tracking-wide">{{ $sesi['hari'] }}</span>
                            <span class="text-[15px] font-bold leading-none">{{ $sesi['tgl'] }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13.5px] font-bold text-bark-800">{{ $sesi['mapel'] }}</p>
                            <p class="mt-0.5 text-[12.5px] font-semibold text-ember-700">{{ $sesi['topik'] }}</p>
                            <p class="mt-1 text-[11px] text-bark-400">{{ $sesi['waktu'] }}</p>
                            <p class="mt-0.5 text-[11px] text-bark-400">{{ $sesi['tentor'] }}</p>
                        </div>
                        <a href="{{ route('siswa.jadwal') }}" class="shrink-0 self-center pt-4 text-[12px] font-bold text-bark-500 transition hover:text-ember-600 @if ($sesi['hari_ini']) mr-16 @endif">Lihat Detail</a>
                    </div>
                @empty
                    <p class="rounded-xl border border-dashed border-bark-100 px-4 py-8 text-center text-[12px] text-bark-400">
                        Belum ada jadwal kelas untuk pekan ini.
                    </p>
                @endforelse
            </div>
        </section>

        {{-- Pengumuman --}}
        <aside class="h-fit rounded-2xl bg-espresso p-4">
            <div class="flex items-start justify-between gap-2">
                <p class="text-[10px] font-bold uppercase leading-relaxed tracking-[0.1em] text-gold">Pengumuman Pergantian Jam</p>
                @if ($pengumuman?->priority === 'penting')
                    <span class="shrink-0 rounded bg-ember-600 px-1.5 py-0.5 text-[9px] font-bold text-white">Penting</span>
                @endif
            </div>

            @if ($pengumuman === null)
                <p class="mt-3 text-[11.5px] leading-relaxed text-bark-300">Belum ada pengumuman untuk kelasmu.</p>
            @else
                <p class="mt-3 text-[13px] font-bold leading-snug text-white">{{ $pengumuman->title }}</p>
                <p class="mt-0.5 text-[11.5px] font-semibold text-gold">{{ $pengumuman->published_at?->locale('id')->translatedFormat('j F Y') }}</p>
                <p class="mt-2.5 text-[11.5px] leading-relaxed text-bark-300">{{ $pengumuman->body }}</p>
                <div class="mt-3 flex items-center justify-between gap-2 border-t border-white/10 pt-3">
                    <p class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-[11px] text-bark-200">
                        @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-3.5 w-3.5 text-gold'])
                        {{ trim(sprintf('%s %s', $pengumuman->room?->name ?? 'Ruang belum ditentukan', $pengumuman->room?->floor ?? '')) }}
                    </p>
                    <a href="{{ route('siswa.jadwal') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-gold px-3 py-2 text-[11.5px] font-bold text-espresso transition hover:brightness-95">
                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5'])
                        Lihat Jadwal Baru
                    </a>
                </div>
            @endif
        </aside>

        {{-- Materi & modul baru --}}
        <section class="rounded-2xl border border-gold/60 bg-white p-5 xl:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <h2 class="text-[15px] font-bold text-bark-800">Materi &amp; Modul Baru Rilis</h2>
                    <p class="mt-0.5 text-[11.5px] text-bark-400">Rangkuman konsep kunci + bank soal berbasis UTBK resmi</p>
                </div>
                <a href="{{ route('siswa.materi') }}" class="text-[12px] font-bold text-ember-600 hover:underline">Semua Modul →</a>
            </div>

            @if ($sesiUnggulan !== null)
                <div class="mt-4 flex flex-wrap items-center gap-3 rounded-xl border border-bark-100 p-3">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500">
                        <span class="text-[9px] font-bold uppercase tracking-wide">{{ $sesiUnggulan['hari'] }}</span>
                        <span class="text-[15px] font-bold leading-none">{{ $sesiUnggulan['tgl'] }}</span>
                    </div>
                    <div>
                        <p class="text-[13.5px] font-bold text-bark-800">{{ $sesiUnggulan['mapel'] }}</p>
                        <p class="mt-0.5 text-[12.5px] font-semibold text-ember-700">{{ $sesiUnggulan['topik'] }}</p>
                        <p class="mt-1 text-[11px] text-bark-400">{{ $sesiUnggulan['waktu'] }} • {{ $sesiUnggulan['tentor'] }}</p>
                    </div>
                </div>
            @endif

            @if ($modulBaru->isEmpty())
                <p class="mt-3 rounded-xl border border-dashed border-bark-100 px-4 py-8 text-center text-[12px] text-bark-400">
                    Belum ada modul yang dirilis untuk kelasmu.
                </p>
            @else
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach ($modulBaru as $modul)
                        <div class="flex flex-col rounded-xl border border-bark-100 p-3.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="rounded-md bg-gold px-1.5 py-0.5 text-[9.5px] font-bold text-espresso">{{ $modul['badge'] }}</span>
                                <span class="text-[10px] font-medium text-bark-300">{{ $modul['ukuran'] }}</span>
                            </div>
                            <p class="mt-2.5 text-[13px] font-bold leading-snug text-bark-800">{{ $modul['judul'] }}</p>
                            <p class="mt-1 flex-1 text-[11px] leading-relaxed text-bark-400">{{ $modul['deskripsi'] }}</p>
                            <a href="{{ route('siswa.materi') }}" class="mt-3 rounded-lg bg-gold px-3 py-2 text-center text-[12px] font-bold text-espresso transition hover:brightness-95">Baca Modul</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
