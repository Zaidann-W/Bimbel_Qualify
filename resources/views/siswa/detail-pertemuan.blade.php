@extends('layouts.siswa')

@section('title', 'Detail Pertemuan Siswa')

{{-- shortcut: filter matpel, dropdown rentang minggu, dan tombol Masuk ke Kelas masih mock UI; sambungkan ke backend jadwal siswa --}}
@section('content')
@php
    $filter = ['Semua Matpel', 'Matematika', 'Fisika', 'Kimia', 'Biologi', 'TPS Penalaran'];

    // [hari, tanggal, matpel, status, topik, sesi, guru, ruang, berjalan]
    $sesi = [
        ['SEN', '10', 'Matematika Saintek', 'Sesi Berjalan', 'Kalkulus & Integral Lanjutan', 'Sesi Sore (15.30 – 17.00 WIB)', 'Kak Hendra Wijaya, M.Sc', 'Ruang Eureka Lt. 2', true],
        ['JUM', '12', 'Fisika Saintek', 'Terjadwal', 'Dinamika Gerak & Listrik Magnet', 'Sesi Sore (15.30 – 17.00 WIB)', 'Kak Sarah Anastasia, S.Pd', 'Ruang Newton Lt. 1', false],
        ['SAB', '12', 'Fisika Saintek', 'Terjadwal', 'Dinamika Gerak & Listrik Magnet', 'Sesi Sore (15.30 – 17.00 WIB)', 'Kak Sarah Anastasia, S.Pd', 'Ruang Newton Lt. 1', false],
        ['KAM', '13', 'Kimia Saintek', 'Terjadwal', 'Termokimia & Kesetimbangan Kimia', 'Sesi Sore (15.30 – 17.00 WIB)', 'Dr Hartanto, M.Pd', 'Ruang Volta Lt. 2', false],
    ];
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-ember-50 px-3 py-1.5 text-[11.5px] font-bold text-ember-700 ring-1 ring-ember-200">
            @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5'])
            Semester Genap 2024/2025 • Kelas 12 IPA
        </span>
        <h1 class="mt-3 text-[22px] font-bold tracking-tight text-bark-800">Jadwal Belajar Siswa</h1>
        <p class="mt-1 text-[13px] text-bark-400">Jadwal kelas terdaftar otomatis sesuai sem belajar semester berjalan.</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-bark-100 bg-white p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">Sesi Berjalan Hari Ini</p>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="text-[26px] font-bold leading-none tracking-tight text-bark-800">1</span>
                        <span class="text-[11.5px] font-medium text-bark-400">Sesi</span>
                    </div>
                </div>
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-ember-50 text-ember-600">
                    @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-[18px] w-[18px]'])
                </div>
            </div>
            <span class="mt-3 inline-block rounded-md bg-ember-100 px-2 py-0.5 text-[10px] font-bold text-ember-700">Senin 16.30 – 17.00</span>
        </div>

        <div class="rounded-2xl border border-bark-100 bg-white p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">Total Sesi Pekan Ini</p>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="text-[26px] font-bold leading-none tracking-tight text-bark-800">4</span>
                        <span class="text-[11.5px] font-medium text-bark-400">Sesi</span>
                    </div>
                </div>
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-bark-50 text-bark-500">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-[18px] w-[18px]'])
                </div>
            </div>
            <p class="mt-3 text-[10.5px] text-bark-400">Terhitung dari minggu ini</p>
        </div>

        <div class="rounded-2xl border border-bark-100 bg-white p-4">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">Ruangan Belajar Utama</p>
                    <p class="mt-1.5 text-[17px] font-bold leading-tight text-bark-800">Eureka (Lantai 2)</p>
                </div>
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                    @include('layouts.partials.icon', ['name' => 'building', 'class' => 'h-[18px] w-[18px]'])
                </div>
            </div>
            <span class="mt-3 inline-block rounded-md bg-bark-50 px-2 py-0.5 text-[10px] font-bold text-bark-500">Ruang / 15 Siswa</span>
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-bark-100 bg-white px-4 py-3">
        <div class="flex flex-wrap items-center gap-1">
            @foreach ($filter as $i => $f)
                @php $active = $i === 0; @endphp
                <button type="button"
                        @class([
                            'rounded-lg px-3 py-1.5 text-[12px] font-semibold transition',
                            'bg-bark-800 text-white' => $active,
                            'text-bark-500 hover:bg-bark-50' => ! $active,
                        ])>{{ $f }}</button>
            @endforeach
        </div>
        <div class="flex items-center gap-3">
            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3 py-1.5 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                Minggu Ini
                @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5'])
            </button>
            <span class="text-[11.5px] text-bark-400">10 – 16 Maret 2025</span>
        </div>
    </div>

    {{-- Daftar sesi --}}
    <ul class="space-y-3">
        @foreach ($sesi as [$hari, $tanggal, $matpel, $status, $topik, $waktu, $guru, $ruang, $berjalan])
            <li @class([
                'flex flex-wrap items-center gap-4 rounded-2xl border px-4 py-4',
                'border-ember-300 bg-ember-50/50' => $berjalan,
                'border-bark-100 bg-white' => ! $berjalan,
            ])>
                <div @class([
                    'grid h-14 w-14 shrink-0 place-items-center rounded-xl text-center',
                    'bg-ember-400 text-bark-900' => $berjalan,
                    'bg-bark-50 text-bark-600' => ! $berjalan,
                ])>
                    <div>
                        <p class="text-[9px] font-bold uppercase leading-none tracking-wide">{{ $hari }}</p>
                        <p class="mt-1 text-[18px] font-bold leading-none">{{ $tanggal }}</p>
                    </div>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span @class([
                            'rounded-md px-2 py-[3px] text-[10px] font-bold',
                            'bg-ember-100 text-ember-700' => $berjalan,
                            'bg-bark-100 text-bark-500' => ! $berjalan,
                        ])>{{ $matpel }}</span>
                        <span @class([
                            'rounded-full px-2 py-[3px] text-[10px] font-bold',
                            'bg-ember-500 text-white' => $berjalan,
                            'bg-bark-50 text-bark-400' => ! $berjalan,
                        ])>{{ $status }}</span>
                    </div>

                    <p class="mt-2 text-[15px] font-bold text-bark-800">{{ $topik }}</p>

                    <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11.5px] text-bark-400">
                        <span class="inline-flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-3.5 w-3.5'])
                            {{ $waktu }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3.5 w-3.5'])
                            {{ $guru }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-3.5 w-3.5'])
                            {{ $ruang }}
                        </span>
                    </div>
                </div>

                @if ($berjalan)
                    <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-bark-800 px-4 py-2.5 text-[12.5px] font-bold text-white transition hover:bg-bark-900">
                        @include('layouts.partials.icon', ['name' => 'log-in', 'class' => 'h-3.5 w-3.5'])
                        Masuk ke Kelas
                    </button>
                @else
                    <a href="{{ route('siswa.jadwal') }}" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-bark-200 bg-white px-4 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                        Detail Pertemuan
                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endsection
