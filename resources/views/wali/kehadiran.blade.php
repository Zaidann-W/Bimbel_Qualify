@extends('layouts.wali')

@section('title', 'Kehadiran Anak - Orang tua')

{{-- shortcut: filter bulan/status, search, pagination, Ajukan Izin, dan Ganti Nomor WhatsApp masih mock UI; sambungkan ke backend presensi --}}
@section('content')
@php
    $stats = [
        ['Kehadiran Fisik', '38', '/40 Sesi', 'Sangat Disiplin', 'check-circle', 'bg-emerald-50 text-emerald-600', 'bg-emerald-50 text-emerald-600'],
        ['Izin', '2', 'Sesi', null, 'info', 'bg-ember-50 text-ember-600', null],
        ['Sakit', '0', 'Sesi', null, 'help-circle', 'bg-bark-100 text-bark-500', null],
        ['Alpa / Tanpa Izin', '0', 'Sesi', null, 'x', 'bg-rose-50 text-rose-500', null],
    ];

    // [hariTgl, masuk, masukTone, mapel, tentor, ruang, status, statusTone, nilai, catatan]
    $riwayat = [
        ['Rabu, 12 Mar 2025', 'Masuk 15.24 WIB • (Sesi Sore)', 'text-emerald-600', 'Fisika Saintek', 'Kak Sarah Anastasia, S.Pd', 'Ruang Eureka (Lt. 2)',
         'Hadir Tepat Waktu', 'bg-emerald-50 text-emerald-600', 'Kak Hendra: 92/100', 'Laporan sesi terkirim ke WhatsApp'],
        ['Senin, 10 Mar 2025', 'Masuk 15.20 WIB • (Sesi Sore)', 'text-emerald-600', 'Matematika IPA', 'Kak Hendra Wijaya, M. Sc', 'Ruang Newton (Lt. 1)',
         'Hadir Tepat Waktu', 'bg-emerald-50 text-emerald-600', 'Kak Hendra: 88/100', 'Integral Tentu & Tak Tentu, Aryanti'],
        ['Jumat, 07 Mar 2025', 'Izin (Orang Tua Mengajukan)', 'text-ember-600', 'Kimia Intensif', 'Kak Citra Larasati, S. Si', 'Ruang Curie (Lt. 3)',
         'Izin Lomba OSN', 'bg-amber-50 text-amber-600', 'Diizinkan Disesuaikan', 'Sertifikat lomba diunggah via WhatsApp'],
        ['Rabu, 05 Mar 2025', 'Masuk 15.25 WIB • (Sesi Sore)', 'text-emerald-600', 'Biologi UTBK', 'Eva Rosary Halim, S.Pd', 'Ruang Eureka (Lt. 2)',
         'Hadir Tepat Waktu', 'bg-emerald-50 text-emerald-600', 'Kak Hendra: 96/100', 'Genetika & Hereditas, Aurora'],
    ];
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[24px] font-bold tracking-tight text-bark-800">Monitoring Kehadiran &amp; Presensi Ananda</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Pantau status kehadiran real-time lewat notifikasi presensi dan rekam jejak yang transparan.</p>
        </div>
        <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-emerald-700">
            @include('layouts.partials.icon', ['name' => 'phone', 'class' => 'h-4 w-4'])
            Hubungi Admin Bimbel
        </button>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as [$label, $value, $unit, $badge, $icon, $tone, $badgeTone])
            <div class="rounded-2xl border border-bark-100 bg-white p-4">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                    <div @class(['grid h-8 w-8 shrink-0 place-items-center rounded-lg', $tone])>
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4'])
                    </div>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-[26px] font-bold leading-none tracking-tight text-bark-800">{{ $value }}</span>
                    <span class="text-[11.5px] font-medium text-bark-400">{{ $unit }}</span>
                </div>
                @if ($badge)
                    <span @class(['mt-3 inline-flex rounded-md px-2 py-1 text-[10.5px] font-bold', $badgeTone])>{{ $badge }}</span>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Rasio + status kesehatan --}}
    <section class="flex flex-wrap items-center gap-x-8 gap-y-4 rounded-2xl border border-bark-100 bg-white px-5 py-4">
        <div class="min-w-[240px] flex-1">
            <div class="flex items-center justify-between gap-2">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">Rasio Kehadiran Periode Ini</p>
                <p class="text-[15px] font-bold text-emerald-600">95%</p>
            </div>
            <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full rounded-full bg-emerald-500" style="width: 95%"></div>
            </div>
        </div>
        <div class="flex flex-wrap gap-6">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">Kontrol Kesehatan</p>
                <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-600">
                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                    Sehat &amp; Prima
                </span>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">Pengaturan</p>
                <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-600">
                    @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'h-3.5 w-3.5'])
                    Aman &amp; Terkendali
                </span>
            </div>
        </div>
    </section>

    {{-- Kehadiran terakhir --}}
    <section class="rounded-2xl bg-espresso px-5 py-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'log-in', 'class' => 'h-5 w-5'])
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-gold">Kehadiran Terakhir</p>
                    <p class="mt-1 text-[15.5px] font-bold text-white">Rabu, 12 Maret 2025 • Pukul 15:24 WIB</p>
                    <p class="mt-0.5 text-[12px] text-bark-300">Fisika Saintek di <span class="font-semibold text-gold">Ruang Eureka (Lantai 2)</span> • Ananda sudah berada di dalam ruang kelas tercatat.</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 rounded-xl border border-emerald-400/40 bg-emerald-400/10 px-4 py-2.5">
                @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-5 w-5 text-emerald-400'])
                <div class="leading-tight">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-300/90">Status Presensi</p>
                    <p class="mt-0.5 text-[14px] font-bold text-white">Tepat Waktu</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Riwayat --}}
    <section class="overflow-hidden rounded-2xl border border-bark-100 bg-white">
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-5 py-4">
            <div>
                <h2 class="text-[15.5px] font-bold text-bark-800">Riwayat Kehadiran Ananda</h2>
                <p class="mt-0.5 text-[11.5px] text-bark-400">Daftar kelas bimbel terakhir dan waktu kedatangan anak terakhir.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5'])
                    Bulan Maret 2025
                    @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5'])
                </button>
                <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                    Semua Status
                    @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5'])
                </button>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                    <input type="search" placeholder="Cari mata pelajaran…"
                           class="w-[180px] rounded-lg border border-bark-200 bg-white py-2 pl-8 pr-3 text-[12px] text-bark-800 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                </div>
            </div>
        </header>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left">
                <thead>
                    <tr class="border-b border-bark-100 bg-bark-50/60 text-[10px] font-bold uppercase tracking-wide text-bark-400">
                        <th class="px-5 py-3">Tanggal &amp; Waktu Masuk</th>
                        <th class="px-4 py-3">Mata Pelajaran &amp; Tentor</th>
                        <th class="px-4 py-3">Ruang Belajar</th>
                        <th class="px-4 py-3">Status Kehadiran</th>
                        <th class="px-5 py-3">Catatan Sesi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bark-100">
                    @foreach ($riwayat as [$tgl, $masuk, $masukTone, $mapel, $tentor, $ruang, $status, $statusTone, $nilai, $catatan])
                        <tr class="transition hover:bg-bark-50/60">
                            <td class="px-5 py-3.5 align-top">
                                <p class="text-[13px] font-bold text-bark-800">{{ $tgl }}</p>
                                <p @class(['mt-0.5 text-[11.5px] font-semibold', $masukTone])>{{ $masuk }}</p>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <p class="text-[13px] font-bold text-bark-800">{{ $mapel }}</p>
                                <p class="mt-0.5 text-[11.5px] text-bark-400">{{ $tentor }}</p>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <span class="inline-flex items-center gap-1.5 text-[12px] text-bark-500">
                                    @include('layouts.partials.icon', ['name' => 'building', 'class' => 'h-3.5 w-3.5 text-bark-400'])
                                    {{ $ruang }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <span @class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold', $statusTone])>
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 align-top">
                                <p class="text-[12.5px] font-semibold text-bark-700">{{ $nilai }}</p>
                                <p class="mt-0.5 text-[11.5px] text-bark-400">{{ $catatan }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-bark-100 px-5 py-3.5">
            <p class="text-[11.5px] text-bark-400">Menampilkan 1 – 4 dari total 40 sesi belajar semester ini.</p>
            <div class="flex items-center gap-1.5">
                <button type="button" class="rounded-lg border border-bark-200 bg-white px-3 py-1.5 text-[11.5px] font-semibold text-bark-500 transition hover:bg-bark-50">Sebelumnya</button>
                @foreach ([1, 2, 3] as $p)
                    <button type="button"
                            @class([
                                'h-7 w-7 rounded-lg text-[11.5px] font-bold transition',
                                'bg-ember-400 text-bark-900' => $p === 1,
                                'border border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => $p !== 1,
                            ])>{{ $p }}</button>
                @endforeach
                <span class="px-1 text-[11.5px] text-bark-400">…</span>
                <button type="button" class="h-7 w-7 rounded-lg border border-bark-200 bg-white text-[11.5px] font-bold text-bark-500 transition hover:bg-bark-50">10</button>
                <button type="button" class="rounded-lg border border-bark-200 bg-white px-3 py-1.5 text-[11.5px] font-semibold text-bark-500 transition hover:bg-bark-50">Selanjutnya</button>
            </div>
        </footer>
    </section>

    {{-- Aksi bawah --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <section class="flex items-start gap-3 rounded-2xl border border-bark-100 bg-gold/15 px-5 py-4">
            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                @include('layouts.partials.icon', ['name' => 'clipboard-form', 'class' => 'h-[18px] w-[18px]'])
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-[14.5px] font-bold text-bark-800">Perlu Izin atau Sakit untuk Ananda?</h3>
                <p class="mt-1 text-[12px] leading-relaxed text-bark-500">Pemberitahuan izin disarankan sebelum hari dimulai agar status anak tetap tercatat di rekap (presensi) harian.</p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <button type="button" class="rounded-lg bg-ember-500 px-3.5 py-2 text-[12px] font-bold text-white transition hover:bg-ember-600">
                        Ajukan Izin Sekarang
                    </button>
                    <a href="#" class="inline-flex items-center gap-1 text-[12px] font-bold text-bark-600 hover:text-bark-800">
                        Panduan Dispensasi
                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                    </a>
                </div>
            </div>
        </section>

        <section class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4">
            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-emerald-500 text-white">
                @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-[18px] w-[18px]'])
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-[14.5px] font-bold text-bark-800">Notifikasi WhatsApp Otomatis</h3>
                <p class="mt-1 text-[12px] leading-relaxed text-bark-500">Sistem presensi Qualify akan langsung mengirim pesan konfirmasi ke nomor HP Ayah/Bunda: <span class="font-semibold text-bark-700">+62 812-4455-8891</span></p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-1 text-[10.5px] font-bold text-emerald-600 ring-1 ring-emerald-200">
                        @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                        Notifikasi Aktif &amp; Lancar
                    </span>
                    <a href="#" class="inline-flex items-center gap-1 text-[12px] font-bold text-bark-600 hover:text-bark-800">
                        Ganti Nomor WhatsApp
                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                    </a>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
