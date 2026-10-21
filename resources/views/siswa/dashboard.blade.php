@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

{{-- shortcut: tombol Lihat Detail, Baca Modul, kalender, dan link "Semua Modul" masih mock UI; sambungkan ke route materi/jadwal siswa kalau halamannya sudah ada --}}
@section('content')
@php
    $jadwal = [
        ['SEN', '17', 'Matematika Dasar: Integral Substitusi & Parsial', '16.30 – 17.00 WIB • Ruang Eureka Lt. 2', 'Kak Hendra Wijaya, M.Sc', true],
        ['RAB', '19', 'Fisika: Dualisme Gelombang Partikel', '16.30 – 17.00 WIB • Ruang Eureka Lt. 1', 'Kak Hendra Wijaya, M.Sc', false],
        ['KAM', '20', 'Fisika Dualisme Gelombang Partikel', '16.30 – 17.00 WIB • Ruang Eureka Lt. 1', 'Kak Hendra Wijaya, M.Sc', false],
    ];

    $modul = [
        ['BAB 1', 'PDF • 6.2 MB', 'Modul: Bab Trigonometri Lanjutan', 'Teks bacaan, latihan, dan pembahasan UTBK terbaru.'],
        ['BAB 2', 'PDF • 5.1 MB', 'Rangkuman Rumus Fisika', 'Ringkasan rumus penting dengan contoh soal tiap bab.'],
        ['BAB 3', 'PDF • 7.3 MB', 'Bank Soal Penalaran', 'Kompilasi 45 soal latihan beserta kunci jawaban.'],
    ];
@endphp

<div class="space-y-5">

    {{-- Banner --}}
    <section class="rounded-2xl bg-bark-800 px-5 py-5">
        <span class="inline-block rounded-md bg-ember-400 px-2 py-[3px] text-[10px] font-bold text-bark-900">Kelas 12 SMA-IPA</span>
        <h1 class="mt-2.5 text-[22px] font-bold tracking-tight text-white">Halo, Aisyah! Semangat belajar hari ini</h1>
    </section>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        {{-- Modul --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">Modul Belajar</p>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="text-[26px] font-bold leading-none tracking-tight text-bark-800">28</span>
                        <span class="text-[11.5px] font-medium text-bark-400">Total Modul</span>
                    </div>
                </div>
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-ember-50 text-ember-600">
                    @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'h-[18px] w-[18px]'])
                </div>
            </div>
            <span class="mt-3 inline-block rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600">Baru 5 Modul</span>
        </div>

        {{-- Kehadiran --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">Persentase Hadir</p>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="text-[26px] font-bold leading-none tracking-tight text-bark-800">98.5%</span>
                    </div>
                </div>
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                    @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-[18px] w-[18px]'])
                </div>
            </div>
            <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full w-[98.5%] rounded-full bg-emerald-500"></div>
            </div>
            <p class="mt-1.5 text-[10.5px] text-bark-400">36 Hadir / 40 Sesi</p>
        </div>

        {{-- Pertanyaan --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">Pertanyaan Aktif</p>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="text-[26px] font-bold leading-none tracking-tight text-bark-800">2</span>
                    </div>
                </div>
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-ember-50 text-ember-600">
                    @include('layouts.partials.icon', ['name' => 'help-circle', 'class' => 'h-[18px] w-[18px]'])
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5">
                <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600">1 Terjawab</span>
                <span class="rounded-md bg-ember-100 px-2 py-0.5 text-[10px] font-bold text-ember-700">1 Menunggu</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- Kiri: jadwal + materi --}}
        <div class="space-y-5 lg:col-span-2">

            {{-- Jadwal belajar --}}
            <section class="rounded-2xl border border-bark-100 bg-white">
                <header class="flex items-center justify-between border-b border-bark-100 px-5 py-4">
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Jadwal Belajar Pekan Ini</h2>
                        <p class="text-[11px] text-bark-400">Periode: 17 – 23 Maret 2025 • Kelas 12 SMA-IPA</p>
                    </div>
                    <a href="#" class="flex items-center gap-1 text-[12px] font-semibold text-ember-600 hover:text-ember-700">
                        Kalender Lengkap
                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                    </a>
                </header>

                <ul class="space-y-3 p-4">
                    @foreach ($jadwal as [$hari, $tanggal, $topik, $waktu, $guru, $today])
                        <li @class([
                            'flex flex-wrap items-center gap-3 rounded-xl border px-4 py-3',
                            'border-ember-200 bg-ember-50/50' => $today,
                            'border-bark-100 bg-white' => ! $today,
                        ])>
                            <div @class([
                                'grid h-12 w-12 shrink-0 place-items-center rounded-lg text-center',
                                'bg-ember-400 text-bark-900' => $today,
                                'bg-bark-50 text-bark-500' => ! $today,
                            ])>
                                <div>
                                    <p class="text-[9px] font-bold uppercase leading-none tracking-wide">{{ $hari }}</p>
                                    <p class="mt-0.5 text-[15px] font-bold leading-none">{{ $tanggal }}</p>
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-[13.5px] font-bold text-bark-800">{{ $topik }}</p>
                                <p class="mt-0.5 text-[11.5px] text-bark-400">{{ $waktu }}</p>
                                <p class="mt-1 inline-flex items-center gap-1.5 text-[11px] text-bark-500">
                                    @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3 w-3 text-bark-300'])
                                    {{ $guru }}
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                @if ($today)
                                    <span class="rounded-full bg-ember-100 px-2 py-[3px] text-[10px] font-bold text-ember-700">Hari ini</span>
                                @endif
                                <button type="button" class="rounded-lg border border-bark-200 bg-white px-3 py-2 text-[11.5px] font-semibold text-bark-600 transition hover:bg-bark-50">
                                    Lihat Detail
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Materi baru --}}
            <section class="rounded-2xl border border-bark-100 bg-white">
                <header class="flex items-center justify-between border-b border-bark-100 px-5 py-4">
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Materi &amp; Modul Baru Rilis</h2>
                        <p class="text-[11px] text-bark-400">Rangkuman konsep asal &amp; soal latihan UTBK terkini</p>
                    </div>
                    <a href="#" class="flex items-center gap-1 text-[12px] font-semibold text-ember-600 hover:text-ember-700">
                        Semua Modul
                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                    </a>
                </header>

                <div class="space-y-3 p-4">
                    {{-- Modul unggulan --}}
                    <div class="flex flex-wrap items-center gap-3 rounded-xl border border-bark-100 bg-bark-50/60 px-4 py-3">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-lg bg-bark-100 text-center text-bark-600">
                            <div>
                                <p class="text-[9px] font-bold uppercase leading-none tracking-wide">Kam</p>
                                <p class="mt-0.5 text-[15px] font-bold leading-none">20</p>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13.5px] font-bold text-bark-800">Kimia Organik: Isomer &amp; Gugus Fungsi</p>
                            <p class="mt-0.5 text-[11.5px] text-bark-400">15.30 – 17.00 WIB • Ruang Eureka Lt. 2 • 2 Kali Rapat Rutin</p>
                        </div>
                    </div>

                    {{-- Kartu modul --}}
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        @foreach ($modul as [$bab, $berkas, $judul, $deskripsi])
                            <article class="flex flex-col rounded-xl border border-bark-100 bg-white p-3.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="rounded-md bg-bark-800 px-2 py-0.5 text-[9.5px] font-bold text-white">{{ $bab }}</span>
                                    <span class="text-[9.5px] font-semibold text-bark-400">{{ $berkas }}</span>
                                </div>
                                <h3 class="mt-2 text-[12.5px] font-bold leading-snug text-bark-800">{{ $judul }}</h3>
                                <p class="mt-1 line-clamp-2 flex-1 text-[11px] leading-relaxed text-bark-400">{{ $deskripsi }}</p>
                                <button type="button" class="mt-3 w-full rounded-lg border border-bark-200 bg-white px-3 py-2 text-[11.5px] font-semibold text-bark-600 transition hover:bg-bark-50">
                                    Baca Modul
                                </button>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        {{-- Kanan: pengumuman --}}
        <section class="self-start rounded-2xl bg-bark-800 p-5">
            <div class="flex items-center justify-between gap-2">
                <p class="text-[9.5px] font-bold uppercase tracking-[0.15em] text-ember-400">Pengumuman Terbaru</p>
                <span class="rounded-full bg-bark-700 px-2 py-0.5 text-[9.5px] font-bold text-bark-200">1 Baru</span>
            </div>

            <h2 class="mt-3 text-[15px] font-bold leading-snug text-white">Perpanjangan Jam: Fisika Saintek Musim UAS!</h2>
            <p class="mt-1 text-[11px] text-bark-300">19 Maret 2025</p>

            <p class="mt-3 text-[12px] leading-relaxed text-bark-200">Jadwal sesi fisika diperpanjang khusus UAS! 17.30–19.30 WIB (sebelumnya 16.30–17.30 WIB). Ruang: New Lt. 1. Harap persiapkan diri.</p>

            <div class="mt-4 flex items-center justify-between gap-3 border-t border-bark-700 pt-3">
                <div class="flex items-center gap-2">
                    <div class="grid h-7 w-7 place-items-center rounded-full bg-bark-600 text-[9.5px] font-bold text-bark-100">DP</div>
                    <p class="text-[11px] font-semibold text-bark-200">Kak Dimas Prasetyo</p>
                </div>
                <button type="button" class="rounded-lg bg-ember-500 px-3 py-2 text-[11.5px] font-bold text-white transition hover:bg-ember-600">
                    Lihat Jadwal Baru
                </button>
            </div>
        </section>
    </div>
</div>
@endsection
