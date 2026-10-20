@extends('layouts.tentor')

@section('title', 'Dashboard Overview')

@section('content')
@php
    $stats = [
        ['Total Siswa Aktif', '98', 'Orang', '+3 minggu ini', 'up', 'users', 'text-ember-600'],
        ['Kelas Hari Ini', '4', 'Sesi', '2 sudah selesai', 'up', 'clock', 'text-emerald-600'],
        ['PR Masuk Klinik', '12', 'Soal', '5 menunggu review', 'up', 'help-circle', 'text-rose-600'],
        ['Rating Mengajar', '4.8', '/ 5.0', 'dari 60 ulasan', 'up', 'award', 'text-ember-600'],
    ];

    $jadwal = [
        ['08.00 – 09.30', 'Matematika XI-IPA-2', 'Ruang Arimbi · 28 Siswa', 'Akan Dimulai 45 Menit Lagi', 'bg-ember-100 text-ember-700', true, ['XII IPA', ' Semester Ganjil', ' Persiapan UTBK']],
        ['10.00 – 11.30', 'Matematika XII-IPA-1', 'Ruang Arimbi · 30 Siswa', 'Terjadwal', 'bg-bark-100 text-bark-600', false, ['XII IPA', ' Persiapan UTBK']],
        ['13.00 – 14.30', 'Matematika X-1', 'Ruang Brom Orwell · 32 Siswa', 'Terjadwal', 'bg-bark-100 text-bark-600', false, ['X IPA', ' Wajib']],
        ['15.30 – 17.00', 'Bimbingan Privat', 'Ruang Privat 2 · 5 Siswa', 'Terjadwal', 'bg-bark-100 text-bark-600', false, ['Privat', ' 1-on-5']],
    ];

    $klinik = [
        ['Zahra Aulia', 'ZA', 'XI-IPA-1', 'Limit Trigonometri', 'Halo Kak, kalau soal integral substitusi kayak nomor 5, kapan harus pakai substitusi trigonometri?', '16 mnt lalu', true],
        ['Bagas Pratama', 'BP', 'XII-IPA-2', ' Bent Tak Hingga', 'Bagian limit tak hingga ini, kok hasilnya beda ya sama kunci jawaban? Padahal langkahnya sama.', '32 mnt lalu', false],
        ['Aisyah Putri', 'AP', 'X-2', 'Deret Aritmetika', 'Kak, materi deret aritmetika barusan, contoh nomor 3 bisa dijelaskan ulang?', '1 jam lalu', false],
        ['Rafi Ananda', 'RA', 'XI-IPA-3', 'Transformasi Geometri', 'Untuk transformasi geometri, rotasi pusat (a,b) rumusnya gimana ya Kak?', '2 jam lalu', false],
    ];

    $akses = [
        ['Catat Absensi Siswa', 'Hadir, izin, sakit — real-time per kelas', 'clipboard-check', 'bg-ember-50 text-ember-600'],
        ['Buka Materi Ajar', 'Slide, latihan, dan bank soal', 'book-open', 'bg-emerald-50 text-emerald-600'],
        ['Jawab Klinik PR', '5 pertanyaan menunggu jawabanmu', 'help-circle', 'bg-rose-50 text-rose-600'],
        ['Update Profil Mengajar', 'Bio, jam terbang, keahlian', 'user-check', 'bg-indigo-50 text-indigo-600'],
    ];
@endphp

<div class="space-y-6">

    {{-- Header: greeting + date card --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Selamat datang, Kak Hendra!</h1>
            <p class="mt-1 text-[13px] text-bark-400">Pantau kelas, absensi, dan tanya jawab PR siswa dalam satu tempat.</p>
        </div>
        <div class="rounded-xl bg-ember-100 px-4 py-2.5 text-right">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-ember-700">Senin</p>
            <p class="text-[15px] font-bold text-bark-800">20 Oktober 2026</p>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as [$label, $value, $unit, $sub, $trend, $icon, $tone])
            <div class="relative overflow-hidden rounded-2xl border border-bark-100 bg-white p-5">
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
                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-bark-400">
                    <svg class="h-3 w-3 @if($trend === 'up') text-emerald-500 @else text-rose-500 @endif" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="@if($trend === 'up')m6 14 6-6 6 6 @else m6 10 6 6 6-6 @endif"/></svg>
                    <span>{{ $sub }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Two-column --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Left: jadwal + akses cepat --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Jadwal Mengajar --}}
            <section class="overflow-hidden rounded-2xl border border-bark-100 bg-white">
                <header class="flex items-center justify-between border-b border-bark-100 px-5 py-4">
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Jadwal Mengajar Hari Ini</h2>
                        <p class="text-[11px] text-bark-400">4 sesi terjadwal · 1 berlangsung</p>
                    </div>
                    <a href="#" class="flex items-center gap-1 text-[12px] font-semibold text-ember-600 hover:text-ember-700">
                        Lihat semua
                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                    </a>
                </header>

                <ul class="divide-y divide-bark-100">
                    @foreach ($jadwal as [$jam, $mapel, $meta, $status, $badge, $highlight, $tags])
                        @php
                            [$room, $siswa] = array_map('trim', explode('·', $meta));
                        @endphp
                        <li @class([
                            'jadwal-row flex gap-4 px-5 py-4 transition hover:bg-bark-50/60 cursor-pointer',
                            'border-l-[3px] border-ember-400 bg-ember-50/40' => $highlight,
                        ]) role="button" tabindex="0"
                            aria-label="Detail sesi {{ $mapel }}"
                            data-title="{{ $mapel }}"
                            data-subtitle="{{ trim($tags[0]) }} • Hari ini, {{ explode(' – ', $jam)[0] }} WIB"
                            data-room="{{ $room }}"
                            data-siswa="{{ $siswa }}">
                            <div class="w-24 shrink-0 pt-0.5">
                                <p class="text-[13px] font-bold text-bark-800">{{ explode(' – ', $jam)[0] }}</p>
                                <p class="text-[11px] text-bark-400">{{ explode(' – ', $jam)[1] }}</p>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-[14px] font-bold text-bark-800">{{ $mapel }}</p>
                                    <span @class(['rounded-full px-2 py-[3px] text-[10px] font-bold', $badge])>{{ $status }}</span>
                                </div>
                                <p class="mt-0.5 text-[12px] text-bark-400">{{ $meta }}</p>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach ($tags as $tag)
                                        <span class="rounded-md bg-bark-50 px-2 py-0.5 text-[10px] font-medium text-bark-500">{{ trim($tag) }}</span>
                                    @endforeach
                                </div>
                                @if ($highlight)
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-500 px-3 py-2 text-[12px] font-semibold text-white shadow-sm transition hover:bg-ember-600">
                                            @include('layouts.partials.icon', ['name' => 'log-in', 'class' => 'h-3.5 w-3.5'])
                                            Buka Kelas
                                        </button>
                                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                                            @include('layouts.partials.icon', ['name' => 'clipboard-check', 'class' => 'h-3.5 w-3.5'])
                                            Absensi
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Akses Cepat Pengajar --}}
            <section class="rounded-2xl border border-bark-100 bg-white">
                <header class="border-b border-bark-100 px-5 py-4">
                    <h2 class="text-[15px] font-bold text-bark-800">Akses Cepat Pengajar</h2>
                    <p class="text-[11px] text-bark-400">Pintasan tugas harian</p>
                </header>
                <ul class="grid grid-cols-1 sm:grid-cols-2">
                    @foreach ($akses as [$title, $desc, $icon, $tone])
                        <li @class(['border-b border-bark-100', 'sm:border-r' => $loop->odd])>
                            <a href="#" class="flex items-center gap-3 px-5 py-4 transition hover:bg-bark-50/60">
                                <div @class(['grid h-10 w-10 shrink-0 place-items-center rounded-xl', $tone])>
                                    @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-5 w-5'])
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[13px] font-bold text-bark-800">{{ $title }}</p>
                                    <p class="truncate text-[11px] text-bark-400">{{ $desc }}</p>
                                </div>
                                @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 text-bark-300'])
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        {{-- Right: Klinik PR --}}
        <section class="flex flex-col rounded-2xl border border-bark-100 bg-white">
            <header class="flex items-center justify-between border-b border-bark-100 px-5 py-4">
                <div>
                    <h2 class="text-[15px] font-bold text-bark-800">Klinik PR Siswa</h2>
                    <p class="text-[11px] text-bark-400">Antrean pertanyaan masuk</p>
                </div>
                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-600">5 baru</span>
            </header>

            <ul class="flex-1 space-y-3 p-4">
                @foreach ($klinik as [$nama, $initial, $kelas, $topik, $tanya, $waktu, $baru])
                    <li @class([
                        'rounded-xl border p-3.5 transition',
                        'border-ember-200 bg-ember-50/50' => $baru,
                        'border-bark-100 bg-white' => ! $baru,
                    ])>
                        <div class="flex items-center gap-2.5">
                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-ember-100 text-[12px] font-bold text-ember-700">{{ $initial }}</div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13px] font-bold text-bark-800">{{ $nama }}</p>
                                <p class="text-[11px] text-bark-400">{{ $kelas }}</p>
                            </div>
                            <span class="text-[11px] text-bark-300">{{ $waktu }}</span>
                        </div>
                        <div class="mt-2.5">
                            <span class="inline-flex items-center gap-1 rounded-md bg-ember-100 px-2 py-0.5 text-[10px] font-bold text-ember-700">
                                @include('layouts.partials.icon', ['name' => 'tag', 'class' => 'h-3 w-3'])
                                {{ $topik }}
                            </span>
                        </div>
                        <p class="mt-2 line-clamp-3 text-[12.5px] leading-relaxed text-bark-600">{{ $tanya }}</p>
                        <div class="mt-3">
                            <button type="button" class="w-full rounded-lg bg-ember-500 px-3 py-2 text-[12px] font-semibold text-white shadow-sm transition hover:bg-ember-600">
                                Jawab Pertanyaan
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="border-t border-bark-100 p-3">
                <a href="#" class="block rounded-lg py-2.5 text-center text-[12px] font-bold text-ember-600 transition hover:bg-ember-50">
                    Lihat semua pertanyaan
                </a>
            </div>
        </section>
    </div>
</div>

<x-jadwal-detail-modal />
@endsection