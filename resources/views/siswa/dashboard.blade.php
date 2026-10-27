@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

{{-- shortcut: data masih mock UI; sambungkan ke ClassSession, StudentAttendance, QualifixTicket, materials, dan announcements --}}
@section('content')
@php
    $stats = [
        // [label, chip?, nilai, sub, sub_tone, footer_kind, footer, href, ikon, ikon_tone]
        ['Modul Belajar', false, '28', 'Materi Siap Baca', 'text-bark-400 font-medium', 'link', 'Buka Modul', route('siswa.materi'), 'book-open', 'bg-bark-50 text-bark-400'],
        ['Presensi', true, '98.5%', 'Sangat Baik', 'text-emerald-600 font-bold', 'text', '38 Hadir / 40 Sesi', null, 'clipboard-check', 'bg-emerald-50 text-emerald-600'],
        ['Klinik Soal', false, '2', 'Pertanyaan Aktif', 'text-bark-400 font-medium', 'duo', '1 Terjawab', route('siswa.qualifix'), 'message-circle', 'bg-ember-50 text-ember-500'],
    ];

    // [hari, tgl, mapel, topik, waktu_ruang, tentor, hari_ini]
    $jadwal = [
        ['SEN', '17', 'Matematika Saintek', 'Integral Substitusi & Parsial', '15.30 – 17.00 WIB • Ruang Eureka Lt. 2', 'Kak Hendro Wijaya, M.Sc', true],
        ['RAB', '19', 'Fisika', 'Dualisme Gelombang Partikel', '07.30 – 09.00 WIB • Ruang Newton Lt. 1', 'Kak Dimas Prasetyo', false],
        ['KAM', '20', 'Fisika', 'Dualisme Gelombang Partikel', '16.00 – 17.30 WIB • Ruang Newton Lt. 1', 'Kak Dimas Prasetyo', false],
    ];

    // [badge, judul, ukuran, deskripsi]
    $modulBaru = [
        ['Baru', 'Modul 08: Trik Cepat Integral & Limit Aljabar', 'PDF • 4.2 MB', 'Metode Huruf, L\'Hopital, rumus cepat limit wajib.'],
        ['Baru', 'Ringkasan Rumus Pater Fisika Modern', 'PDF • 3.1 MB', 'Peta konsep interferensi, celah ganda, efek fotolistrik.'],
        ['Baru', 'Bedah Soal Penalaran Matematika', 'PDF • 5.8 MB', 'Kompilasi 45 soal edisi besar langkah demi langkah lengkap.'],
    ];
@endphp

<div class="space-y-5">

    {{-- Hero sapaan --}}
    <section class="rounded-2xl bg-espresso px-6 py-5">
        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.1em] text-bark-200">
            <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
            Kelas 12 SMA – IPA
        </span>
        <h1 class="mt-3.5 flex w-fit items-center gap-2 text-[20px] font-bold tracking-tight text-white">
            Halo, Aisyah! Semangat belajar hari ini
            @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'h-5 w-5 text-gold'])
        </h1>
    </section>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($stats as [$label, $chip, $nilai, $sub, $subTone, $footerKind, $footer, $href, $ikon, $ikonTone])
            <div class="rounded-2xl border border-bark-100 bg-white p-4">
                <div class="flex items-start justify-between">
                    @if ($chip)
                        <span class="rounded-md bg-emerald-50 px-2 py-1 text-[10.5px] font-bold text-emerald-700">{{ $label }}</span>
                    @else
                        <p class="text-[11.5px] font-semibold text-bark-400">{{ $label }}</p>
                    @endif
                    <div @class(['grid h-8 w-8 place-items-center rounded-lg', $ikonTone])>
                        @include('layouts.partials.icon', ['name' => $ikon, 'class' => 'h-4 w-4'])
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <span class="text-[24px] font-bold leading-none tracking-tight text-bark-800">{{ $nilai }}</span>
                    <span @class(['text-[11.5px]', $subTone])>{{ $sub }}</span>
                </div>
                @if ($footerKind === 'link')
                    <a href="{{ $href }}" class="mt-3 inline-block text-[11px] font-bold text-ember-600 hover:underline">{{ $footer }}</a>
                @elseif ($footerKind === 'duo')
                    <div class="mt-3 flex gap-2 text-[11px] font-bold">
                        <a href="{{ $href }}" class="text-emerald-600 hover:underline">{{ $footer }}</a>
                        <span class="text-bark-200">•</span>
                        <span class="text-amber-600">1 Menunggu</span>
                    </div>
                @else
                    <p class="mt-3 text-[11px] font-medium text-bark-400">{{ $footer }}</p>
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
                    <p class="mt-0.5 text-[11.5px] text-bark-400">Periode 17 – 23 Maret 2025 • Kelas 12 SMA IPA</p>
                </div>
                <a href="{{ route('siswa.jadwal') }}" class="text-[12px] font-bold text-ember-600 hover:underline">Kalender Lengkap →</a>
            </div>

            <div class="mt-4 space-y-2.5">
                @foreach ($jadwal as [$hari, $tgl, $mapel, $topik, $waktu, $tentor, $hariIni])
                    <div @class([
                        'relative flex flex-wrap items-center gap-3 rounded-xl border p-3 pr-4',
                        'border-gold bg-gold/5' => $hariIni,
                        'border-bark-100' => ! $hariIni,
                    ])>
                        @if ($hariIni)
                            <span class="absolute right-3 top-3 rounded-md bg-gold px-2 py-0.5 text-[9.5px] font-bold text-espresso">Hari Ini</span>
                        @endif
                        <div @class([
                            'grid h-12 w-12 shrink-0 place-items-center rounded-xl',
                            'bg-gold text-espresso' => $hariIni,
                            'bg-bark-50 text-bark-500' => ! $hariIni,
                        ])>
                            <span class="text-[9px] font-bold uppercase tracking-wide">{{ $hari }}</span>
                            <span class="text-[15px] font-bold leading-none">{{ $tgl }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13.5px] font-bold text-bark-800">{{ $mapel }}</p>
                            <p class="mt-0.5 text-[12.5px] font-semibold text-ember-700">{{ $topik }}</p>
                            <p class="mt-1 text-[11px] text-bark-400">{{ $waktu }}</p>
                            <p class="mt-0.5 text-[11px] text-bark-400">{{ $tentor }}</p>
                        </div>
                        <a href="{{ route('siswa.jadwal.detail') }}" class="shrink-0 self-center pt-4 text-[12px] font-bold text-bark-500 transition hover:text-ember-600 @if ($hariIni) mr-16 @endif">Lihat Detail</a>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Pengumuman --}}
        <aside class="h-fit rounded-2xl bg-espresso p-4">
            <div class="flex items-start justify-between gap-2">
                <p class="text-[10px] font-bold uppercase leading-relaxed tracking-[0.1em] text-gold">Pengumuman Pergantian Jam</p>
                <span class="shrink-0 rounded bg-ember-600 px-1.5 py-0.5 text-[9px] font-bold text-white">Penting</span>
            </div>
            <p class="mt-3 text-[13px] font-bold leading-snug text-white">Pergantian Jam: Fisika Saintek (Kelas 12 IPA)</p>
            <p class="mt-0.5 text-[11.5px] font-semibold text-gold">19 Maret 2025</p>
            <p class="mt-2.5 text-[11.5px] leading-relaxed text-bark-300">Jadwal selasa ketiga digeser dari pukul 16.00 – 17.00 WIB menjadi 15.30 – 17.00 WIB di Ruang Newton Lt. 1 karena penyesuaian jadwal tentor Kak Dimas Prasetyo.</p>
            <div class="mt-3 flex items-center justify-between gap-2 border-t border-white/10 pt-3">
                <p class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-[11px] text-bark-200">@include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-3.5 w-3.5 text-gold']) Ruangan Newton Lt. 1</p>
                <a href="{{ route('siswa.jadwal') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-gold px-3 py-2 text-[11.5px] font-bold text-espresso transition hover:brightness-95">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5'])
                    Lihat Jadwal Baru
                </a>
            </div>
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

            <div class="mt-4 flex flex-wrap items-center gap-3 rounded-xl border border-bark-100 p-3">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500">
                    <span class="text-[9px] font-bold uppercase tracking-wide">KAM</span>
                    <span class="text-[15px] font-bold leading-none">20</span>
                </div>
                <div>
                    <p class="text-[13.5px] font-bold text-bark-800">Kimia Organik</p>
                    <p class="mt-0.5 text-[12.5px] font-semibold text-ember-700">Isomeri &amp; Gugus Fungsi</p>
                    <p class="mt-1 text-[11px] text-bark-400">15.30 – 17.00 WIB • Ruang Eureka Lt. 2 • Kak Nadia Rahma</p>
                </div>
            </div>

            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                @foreach ($modulBaru as [$badge, $judul, $ukuran, $deskripsi])
                    <div class="flex flex-col rounded-xl border border-bark-100 p-3.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-md bg-gold px-1.5 py-0.5 text-[9.5px] font-bold text-espresso">{{ $badge }}</span>
                            <span class="text-[10px] font-medium text-bark-300">{{ $ukuran }}</span>
                        </div>
                        <p class="mt-2.5 text-[13px] font-bold leading-snug text-bark-800">{{ $judul }}</p>
                        <p class="mt-1 flex-1 text-[11px] leading-relaxed text-bark-400">{{ $deskripsi }}</p>
                        <a href="{{ route('siswa.materi') }}" class="mt-3 rounded-lg bg-gold px-3 py-2 text-center text-[12px] font-bold text-espresso transition hover:brightness-95">Baca Modul</a>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection
