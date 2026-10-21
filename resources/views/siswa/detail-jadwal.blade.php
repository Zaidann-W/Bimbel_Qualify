@extends('layouts.siswa')

@section('title', 'Detail Jadwal Siswa')

{{-- shortcut: breadcrumb, Tambah ke Kalender, Buka/Unduh berkas, Tanya Qualifix masih mock UI; tombol Ajukan Formulir Izin membuka modal statis --}}
@section('content')
<div class="space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex flex-wrap items-center justify-between gap-3">
        <ol class="flex flex-wrap items-center gap-1.5 text-[12px] text-bark-400">
            <li><a href="{{ route('siswa.dashboard') }}" class="font-medium hover:text-ember-600">Portal Siswa Qualify</a></li>
            <li aria-hidden="true">›</li>
            <li><a href="{{ route('siswa.jadwal.detail') }}" class="font-medium hover:text-ember-600">Jadwal Saya</a></li>
            <li aria-hidden="true">›</li>
            <li class="font-semibold text-bark-700">Detail Sesi Belajar</li>
        </ol>
        <a href="{{ route('siswa.jadwal.detail') }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-ember-600 hover:text-ember-700">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6-6m-6 6 6 6"/></svg>
            Kembali ke Jadwal Saya
        </a>
    </nav>

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-3xl">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-ember-100 px-2.5 py-1 text-[11px] font-bold text-ember-700">
                    @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-3.5 w-3.5'])
                    Hari Ini • 15.30 – 17.00 WIB
                </span>
                <span class="rounded-full bg-bark-100 px-2.5 py-1 text-[11px] font-bold text-bark-600">12 Super Intensif UTBK SNBT</span>
                <span class="rounded-full bg-bark-100 px-2.5 py-1 text-[11px] font-bold text-bark-600">Sesi ke-14 dari 24</span>
            </div>
            <h1 class="mt-3 text-[22px] font-bold tracking-tight text-bark-800">Kalkulus &amp; Aplikasi Integral: Luas &amp; Volume Benda Putar</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Pendalaman teknik integral substitusi dan parsial untuk penyelesaian irisan kurva tertutup dan tipe pemodelan spesial UTBK SNBT.</p>
        </div>
        <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-bark-200 bg-white px-4 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
            @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4'])
            Tambah ke Kalender
        </button>
    </div>

    {{-- Kartu atas: waktu, ruangan, tentor --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        {{-- 1. Waktu & jadwal --}}
        <section class="rounded-2xl border border-bark-100 bg-white p-4">
            <div class="flex items-center gap-2">
                <div class="grid h-7 w-7 place-items-center rounded-lg bg-ember-50 text-ember-600">
                    @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-4 w-4'])
                </div>
                <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">1. Waktu &amp; Jadwal</p>
            </div>
            <p class="mt-3 text-[20px] font-bold tracking-tight text-bark-800">15.30 – 17.00 WIB</p>
            <p class="mt-1 text-[12px] text-bark-400">Durasi 90 Menit</p>
            <div class="mt-3 border-t border-bark-100 pt-3">
                <span class="inline-flex items-center gap-1.5 rounded-md bg-bark-50 px-2 py-1 text-[10.5px] font-semibold text-bark-500">
                    @include('layouts.partials.icon', ['name' => 'user-check', 'class' => 'h-3.5 w-3.5 text-emerald-500'])
                    Presensi RFID/Dibuka mulai 16.15 WIB
                </span>
            </div>
        </section>

        {{-- 2. Kelas & ruangan --}}
        <section class="rounded-2xl border border-bark-100 bg-white p-4">
            <div class="flex items-center gap-2">
                <div class="grid h-7 w-7 place-items-center rounded-lg bg-ember-50 text-ember-600">
                    @include('layouts.partials.icon', ['name' => 'building', 'class' => 'h-4 w-4'])
                </div>
                <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">2. Kelas &amp; Ruangan</p>
            </div>
            <p class="mt-3 text-[17px] font-bold leading-tight text-bark-800">Ruangan Eureka <span class="text-ember-600">(Lantai 2)</span></p>
            <p class="mt-1 text-[12px] text-bark-400">Kapasitas 15 Siswa</p>
            <div class="mt-3 border-t border-bark-100 pt-3">
                <span class="inline-flex items-center gap-1.5 rounded-md bg-ember-50 px-2 py-1 text-[10.5px] font-semibold text-ember-700">
                    @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'h-3.5 w-3.5'])
                    Target Top 3 PTN
                </span>
                <p class="mt-2 text-[11px] text-bark-400">Smart Board + AC • Maksimal 15 Siswa</p>
            </div>
        </section>

        {{-- 3. Tentor pengampu --}}
        <section class="rounded-2xl border border-bark-100 bg-white p-4">
            <div class="flex items-center gap-2">
                <div class="grid h-7 w-7 place-items-center rounded-lg bg-ember-50 text-ember-600">
                    @include('layouts.partials.icon', ['name' => 'users', 'class' => 'h-4 w-4'])
                </div>
                <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">3. Tentor Pengampu</p>
            </div>
            <div class="mt-3 flex items-center gap-2.5">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-ember-100 text-[13px] font-bold text-ember-700">HW</div>
                <div class="min-w-0">
                    <p class="truncate text-[15px] font-bold text-bark-800">Kak Hendra Wijaya, M.Sc</p>
                    <p class="truncate text-[11.5px] text-bark-400">Master Trainer Matematika &amp; Fisika</p>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between gap-2 border-t border-bark-100 pt-3">
                <span class="inline-flex items-center gap-1.5 text-[12px] font-bold text-bark-700">
                    @include('layouts.partials.icon', ['name' => 'star', 'class' => 'h-4 w-4 text-ember-400'])
                    4.96 <span class="font-medium text-bark-400">(320 ulasan)</span>
                </span>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-bark-800 px-3 py-2 text-[11.5px] font-bold text-white transition hover:bg-bark-900">
                    @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-3.5 w-3.5'])
                    Tanya Qualifix
                </button>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- 4. Materi & bahan ajar --}}
        <section class="rounded-2xl border border-bark-100 bg-white lg:col-span-2">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-5 py-4">
                <div class="flex items-start gap-2.5">
                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-ember-50 text-ember-600">
                        @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'h-4 w-4'])
                    </div>
                    <div>
                        <h2 class="text-[14.5px] font-bold text-bark-800">4. Materi &amp; Bahan Ajar Sesi</h2>
                        <p class="text-[11px] text-bark-400">Unduh atau praktikum modul sebelum kelas berlangsung</p>
                    </div>
                </div>
                <span class="rounded-full bg-bark-50 px-2.5 py-1 text-[10.5px] font-bold text-bark-500">3 Berkas</span>
            </header>

            <ul class="divide-y divide-bark-100">
                <li class="flex flex-wrap items-center gap-3 px-5 py-3.5">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-bark-50 text-bark-500">
                        @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-4 w-4'])
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-bold text-bark-800">Modul #11: Aplikasi Integral</p>
                        <p class="text-[11px] text-bark-400">Format PDF • 4.2 MB • Edisi Cetak 2025</p>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <button type="button" class="rounded-lg border border-bark-200 bg-white px-3 py-2 text-[11.5px] font-semibold text-bark-600 transition hover:bg-bark-50">Buka</button>
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-100 px-3 py-2 text-[11.5px] font-bold text-ember-700 transition hover:bg-ember-200">
                            @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-3.5 w-3.5'])
                            Unduh
                        </button>
                    </div>
                </li>
                <li class="flex flex-wrap items-center gap-3 px-5 py-3.5">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-bark-50 text-bark-500">
                        @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-4 w-4'])
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-bold text-bark-800">Lembar Latihan Soal HOTS UTBK</p>
                        <p class="text-[11px] text-bark-400">Format PDF • 1.8 MB • 18 Butir Soal Proyeksi</p>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <button type="button" class="rounded-lg border border-bark-200 bg-white px-3 py-2 text-[11.5px] font-semibold text-bark-600 transition hover:bg-bark-50">Buka</button>
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-100 px-3 py-2 text-[11.5px] font-bold text-ember-700 transition hover:bg-ember-200">
                            @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-3.5 w-3.5'])
                            Unduh
                        </button>
                    </div>
                </li>
                <li class="flex flex-wrap items-center gap-3 px-5 py-3.5">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-bark-50 text-bark-500">
                        @include('layouts.partials.icon', ['name' => 'presentation', 'class' => 'h-4 w-4'])
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-bold text-bark-800">Slide Interaktif Smart Board Eureka</p>
                        <p class="text-[11px] text-bark-400">Akses Online • Live Anotasi saat sesi</p>
                    </div>
                    <button type="button" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-bark-800 px-3 py-2 text-[11.5px] font-bold text-white transition hover:bg-bark-900">
                        @include('layouts.partials.icon', ['name' => 'presentation', 'class' => 'h-3.5 w-3.5'])
                        Buka Slide
                    </button>
                </li>
            </ul>
        </section>

        {{-- 5. Status presensi & izin --}}
        <section class="self-start rounded-2xl border border-bark-100 bg-white">
            <header class="border-b border-bark-100 px-5 py-4">
                <div class="flex items-start gap-2.5">
                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-ember-50 text-ember-600">
                        @include('layouts.partials.icon', ['name' => 'clipboard-check', 'class' => 'h-4 w-4'])
                    </div>
                    <div>
                        <h2 class="text-[14.5px] font-bold text-bark-800">5. Status Presensi &amp; Izin</h2>
                        <p class="text-[11px] text-bark-400">Rekap ketertentuan dan konfirmasi ketidakhadiran</p>
                    </div>
                </div>
            </header>

            <div class="p-4">
                <div class="rounded-xl border border-ember-200 bg-ember-50 px-4 py-4">
                    <p class="text-[13.5px] font-bold text-bark-800">Berhalangan Hadir pada Sesi Ini?</p>
                    <p class="mt-1.5 text-[11.5px] leading-relaxed text-bark-500">Ajukan izin sakit atau dispensasi sekolah, sebelum pukul 14.00 WIB agar kelas dan penggantian modul disiapkan.</p>
                    <button type="button" data-izin-open
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg bg-bark-800 px-3 py-2.5 text-[12.5px] font-bold text-white transition hover:bg-bark-900">
                        @include('layouts.partials.icon', ['name' => 'clipboard-form', 'class' => 'h-4 w-4'])
                        Ajukan Formulir Izin
                    </button>
                </div>
            </div>
        </section>
    </div>
</div>

<x-izin-siswa-modal />
@endsection
