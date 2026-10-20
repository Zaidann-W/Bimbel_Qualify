@extends('layouts.tentor')

@section('title', 'Profil Saya')

{{-- shortcut: form profil, ubah kata sandi, jadwal aktif, dan toggle notifikasi masih mock UI; sambungkan ke backend akun tentor --}}
@section('content')
@php
    $fields = [
        ['Nama Lengkap & Gelar', true, 'Hendra Wijaya, M.Sc.', null],
        ['Pendidikan Terakhir', true, 'S2 Magister Matematika - ITB', null],
        ['Email Pengajar Qualify', true, 'hendra.wijaya@bimbelqualify.co', 'Digunakan untuk akses Google Workspace & SSO Akademik'],
        ['Nomor WhatsApp Aktif', true, '+62 813-9821-7740', 'Terhubung ke Portal Siswa & Layanan Wali Murid'],
    ];

    $keahlian = ['Matematika Saintek', 'TPS Kuantitatif', 'Pembina OSN (Olimpiade Sains Nasional)'];
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Profil Pengajar &amp; Akun Tentor</h1>
            <p class="mt-1 text-[13px] text-bark-400">Kelola identitas pengajar, kontak konsultasi siswa, preferensi mengajar, dan keamanan akun.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-[11.5px] font-bold text-emerald-600 ring-1 ring-emerald-100">
            @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'h-3.5 w-3.5'])
            Terverifikasi Akademik
        </span>
    </div>

    {{-- Kartu identitas --}}
    <section class="rounded-2xl border border-ember-200 bg-ember-50/60 p-5">
        <div class="flex flex-wrap items-start gap-5">
            <div class="relative">
                <div class="grid h-20 w-20 place-items-center rounded-xl bg-bark-800 text-[22px] font-bold text-white">HW</div>
                <span class="absolute -bottom-1 -right-1 grid h-6 w-6 place-items-center rounded-full bg-ember-400 text-bark-900 ring-2 ring-ember-50">
                    @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3.5 w-3.5'])
                </span>
            </div>

            <div class="min-w-0 flex-1">
                <h2 class="text-[19px] font-bold text-bark-800">Kak Hendra Wijaya, M.Sc</h2>
                <span class="mt-1 inline-block rounded-md bg-ember-100 px-2 py-[3px] text-[10px] font-bold text-ember-700">Tentor Aktif Semester Genap 2024/2025</span>
                <p class="mt-2 text-[13.5px] font-semibold text-bark-600">Master Trainer Matematika &amp; Fisika Medika</p>
                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11.5px] text-bark-400">
                    <span class="inline-flex items-center gap-1.5">
                        @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3.5 w-3.5'])
                        NIP: 1-06812
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5'])
                        Bergabung sejak Jul 2021
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-3.5 w-3.5'])
                        Kampus Pusat Dipati Ukur Bandung
                    </span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($keahlian as $k)
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-ember-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-ember-700">
                            @include('layouts.partials.icon', ['name' => 'flame', 'class' => 'h-3 w-3'])
                            {{ $k }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="flex shrink-0 flex-col gap-2">
                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-ember-500 px-4 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-ember-600">
                    @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-3.5 w-3.5'])
                    Edit Profil
                </button>
                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-bark-200 bg-white px-4 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                    @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-3.5 w-3.5'])
                    Ubah Kata Sandi
                </button>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Kiri: informasi pribadi --}}
        <section class="rounded-2xl border border-bark-100 bg-white lg:col-span-2">
            <header class="border-b border-bark-100 px-5 py-4">
                <div class="flex items-center gap-2.5">
                    <div class="grid h-9 w-9 place-items-center rounded-xl bg-ember-50 text-ember-500">
                        @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Informasi Pribadi &amp; Kontak</h2>
                        <p class="text-[11px] text-bark-400">Data kontak tentor untuk keperluan koordinasi dan koordinasi akademik.</p>
                    </div>
                </div>
            </header>

            <div class="px-5 py-4">
                <div class="grid grid-cols-1 gap-x-5 gap-y-4 sm:grid-cols-2">
                    @foreach ($fields as [$label, $required, $value, $hint])
                        <div>
                            <label class="text-[12px] font-bold text-bark-700">
                                {{ $label }}@if ($required)<span class="text-ember-500"> *</span>@endif
                            </label>
                            <input type="text" value="{{ $value }}"
                                   class="mt-1.5 w-full rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                            @if ($hint)
                                <p class="mt-1 text-[10.5px] text-bark-400">{{ $hint }}</p>
                            @endif
                        </div>
                    @endforeach

                    <div class="sm:col-span-2">
                        <label class="text-[12px] font-bold text-bark-700">Alamat Domisili Mengajar</label>
                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-bark-300">
                                @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-4 w-4'])
                            </span>
                            <input type="text" value="Jl. Dayang Sumbi No. 14, Coblong, Kota Bandung, Jawa Barat"
                                   class="w-full rounded-lg border border-bark-200 bg-white py-2.5 pl-9 pr-3 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex items-start gap-2.5 rounded-xl border border-ember-200 bg-ember-50 px-4 py-3">
                    @include('layouts.partials.icon', ['name' => 'info', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
                    <p class="text-[11.5px] leading-relaxed text-bark-500">
                        Untuk memperbarui nomor kontak aktif, NIK, atau izin transfer honorarium, silakan mengajukan permohonan ke
                        <span class="font-bold text-bark-700">Bio Administrasi Kepegawaian (HR)</span>.
                    </p>
                </div>
            </div>
        </section>

        {{-- Kanan: preferensi & notifikasi --}}
        <section class="self-start rounded-2xl border border-bark-100 bg-white">
            <header class="border-b border-bark-100 px-5 py-4">
                <div class="flex items-center gap-2.5">
                    <div class="grid h-9 w-9 place-items-center rounded-xl bg-ember-50 text-ember-500">
                        @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Preferensi Mengajar &amp; Notifikasi</h2>
                        <p class="text-[11px] text-bark-400">Atur jadwal operasional &amp; notifikasi jawaban</p>
                    </div>
                </div>
            </header>

            <div class="space-y-5 px-5 py-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Jadwal Mengajar Aktif</p>
                    <div class="mt-2 rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
                        <p class="text-[14px] font-bold text-bark-800">Senin, Rabu, &amp; Kamis</p>
                        <p class="mt-0.5 text-[12px] text-bark-400">Sesi Sore (15.30 – 18.30)</p>
                    </div>
                </div>

                <div class="flex items-start justify-between gap-3 rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
                    <div>
                        <p class="text-[13px] font-bold text-bark-800">Status Notifikasi Klinik PR Mandiri</p>
                        <p class="mt-0.5 text-[11.5px] leading-relaxed text-bark-400">Dapatkan alert real-time ketika ada siswa masuk di subjek Matematika/Fisika</p>
                    </div>
                    <button type="button" role="switch" aria-checked="true" aria-label="Status notifikasi Klinik PR Mandiri"
                            class="mt-0.5 h-6 w-11 shrink-0 rounded-full bg-ember-500 p-0.5 transition">
                        <span class="block h-5 w-5 translate-x-5 rounded-full bg-white shadow-sm transition"></span>
                    </button>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
