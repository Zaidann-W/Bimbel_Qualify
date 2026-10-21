@extends('layouts.wali')

@section('title', 'Dashboard Orang Tua')

{{-- shortcut: tombol sinkronisasi, kelola profil anak, konfirmasi kehadiran, unggah berkas, dan akses pintas masih mock UI; sambungkan ke backend portal wali --}}
@section('content')
@php
    // [avatar, nama, chip, chipTone, meta, tombol]
    $anak = [
        ['AP', 'Aisyah Putri Azzahra', 'Terdaftar', 'bg-gold text-espresso', 'NISN 0098234121 • Kelas 12 IPA', 'Kelola Profil', true],
        ['AR', 'Anindya Putri Rahayu', 'Perlu Perhatian', 'bg-rose-100 text-rose-600', 'NISN 0098123499 • Kelas 10 IPA', 'Buka Berkas', false],
    ];

    $stats = [
        ['Presensi Semester Berjalan', '95.0%', ['38 Hadir • 2 Izin • 0 Alpa'], 'Sangat Baik', 'bg-emerald-50 text-emerald-600', 'bg-emerald-50 text-emerald-600', 'graduation-cap', false],
        ['Status SPP (Bulan Ini)', 'Lunas (Maret)', ['Nominal Terbayar: Rp. 1.450.000'], 'Menunggu', 'bg-bark-50 text-bark-500', 'bg-gold/20 text-espresso', 'credit-card', false],
        ['Jadwal Kelas Hari Ini', '15:30 – 17:30 WIB', ['Kalkulus & Integral Lanjutan', 'R. Eureka Lt. 2 • Kak Hendra'], 'Tatap Muka', 'bg-bark-50 text-bark-500', 'bg-bark-800 text-white', 'clock', false],
        ['Pendaftaran Anindya', 'Perbaikan Berkas', ['Unggah scan Kartu Keluarga yang masih perlu diperbaiki.'], 'Unggah Berkas Sekarang', 'bg-ember-50 text-ember-600', 'bg-ember-500 text-white', 'pen-line', true],
    ];

    $pintas = [
        ['Pantau Kehadiran', 'user-check', false],
        ['Rapor & Hasil Try Out', 'award', false],
        ['Invoice & Riwayat Bayar', 'file', false],
        ['Hubungi Konsultan Cabang', 'phone', true],
    ];

    // [hari, tgl, judul, chip, meta, metode, metodeTone]
    $jadwal = [
        ['SEN', '24', 'Kalkulus & Matematika Lanjutan', 'Hari Ini', '15.30 – 17.30 WIB • R. Eureka Lt. 2 • Tutor: Kak Hendra Wijaya, M. Sc', 'Tatap Muka', 'bg-bark-800 text-white'],
        ['RAB', '26', 'Fisika Modern & Mekanika Kuantum', null, '15.30 – 17.30 WIB • Lab Newton Lt. 1 • Tutor: Dr. Ir. Syaiful Anwar', 'Tatap Muka', 'bg-bark-800 text-white'],
        ['KAM', '27', 'Kimia Organik & Stoikiometri Reaksi', null, '09.00 – 10.30 WIB • R. Mendeleev Lt. 3 • Tutor: Kak Citra Larasati, S. Si', 'Tatap Muka', 'bg-bark-800 text-white'],
        ['SAB', '29', 'Simulasi Try Out Akbar CBT SNBT #05', 'Wajib', '08.00 – 12.00 WIB • Ruang CBT Digital Center Lt. 3', 'CBT Lab', 'border border-ember-300 bg-white text-ember-600'],
    ];

    // [dotTone, judul, waktu, detail, sub]
    $presensi = [
        ['bg-amber-400', 'Aisyah Tap-In Masuk Bimbel', 'Hari ini • 15.20 WIB', 'Terdeteksi di Gerbang Utama Lantai 1 (Mapan RFID Reader).', 'Sesi: Kalkulus & Matematika Lanjutan'],
        ['bg-emerald-500', 'Setoran SPP Bulan Maret Terbit', 'Kemarin • 10.14 WIB', 'Transfer Mandiri (#INV-2025-03-992) terverifikasi.', 'Bukti bayar tersedia.'],
    ];
@endphp

<div class="space-y-5">

    {{-- Sambutan --}}
    <section class="rounded-2xl border border-bark-100 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gold/25 px-2.5 py-1 text-[10.5px] font-bold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-3.5 w-3.5'])
                    Portal Wali Murid Terhubung
                </span>
                <h1 class="mt-2.5 text-[22px] font-bold leading-snug tracking-tight text-bark-800">Selamat datang kembali, Bapak Ir. Hendra Gunawan!</h1>
                <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Pantau perkembangan akademik, presensi tap-in RFID, serta instruksi putra-putri Anda secara transparan dan terintegrasi.</p>

                {{-- Anak --}}
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($anak as [$avatar, $nama, $chip, $chipTone, $meta, $tombol, $active])
                        <div @class([
                            'flex items-center justify-between gap-3 rounded-xl border p-3',
                            'border-ember-300 bg-ember-50/50' => $active,
                            'border-bark-100 bg-bark-50/60' => ! $active,
                        ])>
                            <div class="flex min-w-0 items-center gap-2.5">
                                <div @class([
                                    'grid h-9 w-9 shrink-0 place-items-center rounded-full text-[11.5px] font-bold',
                                    'bg-bark-800 text-white' => $active,
                                    'bg-bark-200 text-bark-600' => ! $active,
                                ])>{{ $avatar }}</div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <p class="text-[13px] font-bold text-bark-800">{{ $nama }}</p>
                                        <span @class(['rounded-md px-1.5 py-[2px] text-[9px] font-bold', $chipTone])>{{ $chip }}</span>
                                    </div>
                                    <p class="truncate text-[11px] text-bark-400">{{ $meta }}</p>
                                </div>
                            </div>
                            <button type="button" class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-bark-200 bg-white px-2.5 py-1.5 text-[11px] font-bold text-bark-600 transition hover:bg-bark-50">
                                {{ $tombol }}
                                @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3 w-3'])
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Status sinkronisasi --}}
            <div class="flex shrink-0 items-center gap-3 rounded-xl border border-bark-100 bg-bark-50 px-3.5 py-3">
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gold px-3 py-2 text-[11.5px] font-bold text-espresso transition hover:brightness-95">
                    @include('layouts.partials.icon', ['name' => 'refresh', 'class' => 'h-3.5 w-3.5'])
                    Sinkron
                </button>
                <div class="leading-tight">
                    <p class="text-[10.5px] font-bold uppercase tracking-wide text-bark-700">Sinkronisasi Realtime RFID Aktif</p>
                    <p class="mt-0.5 text-[11px] text-bark-500">WhatsApp: +62 812-4455-8891</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as [$label, $value, $lines, $badge, $iconTone, $badgeTone, $icon, $action])
            <div @class([
                'flex flex-col rounded-2xl border bg-white p-4',
                'border-ember-300 ring-1 ring-ember-100' => $action,
                'border-bark-100' => ! $action,
            ])>
                <div class="flex items-start justify-between gap-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                    <div @class(['grid h-8 w-8 shrink-0 place-items-center rounded-lg', $iconTone])>
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4'])
                    </div>
                </div>
                <p @class([
                    'mt-2 text-[20px] font-bold leading-tight tracking-tight',
                    'text-ember-600' => $action,
                    'text-bark-800' => ! $action,
                ])>{{ $value }}</p>
                <div class="mt-1 space-y-0.5">
                    @foreach ($lines as $line)
                        <p class="text-[11.5px] leading-relaxed text-bark-400">{{ $line }}</p>
                    @endforeach
                </div>
                <div class="mt-auto pt-3">
                    @if ($action)
                        <button type="button" class="w-full rounded-lg bg-ember-500 px-3 py-2 text-[12px] font-bold text-white transition hover:bg-ember-600">
                            {{ $badge }}
                        </button>
                    @else
                        <span @class(['inline-flex rounded-md px-2 py-1 text-[10.5px] font-bold', $badgeTone])>{{ $badge }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Akses pintas --}}
    <section class="rounded-2xl border border-bark-100 bg-white px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-bark-400">Akses Pintas Cepat Orang Tua</p>
        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($pintas as [$label, $icon, $gold])
                <button type="button" @class([
                    'flex items-center justify-center gap-2 rounded-xl border px-3 py-3 text-[12.5px] font-bold transition',
                    'border-gold bg-gold/20 text-espresso hover:brightness-95' => $gold,
                    'border-bark-100 bg-white text-bark-600 hover:bg-bark-50' => ! $gold,
                ])>
                    @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4'])
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">

        {{-- Kiri: jadwal pekan ini --}}
        <section class="self-start rounded-2xl border border-bark-100 bg-white">
            <header class="flex flex-wrap items-center justify-between gap-2 border-b border-bark-100 px-5 py-4">
                <div>
                    <h2 class="text-[15px] font-bold text-bark-800">Jadwal Belajar Pekan Ini</h2>
                    <p class="mt-0.5 text-[11.5px] text-bark-400">Aisyah Putri Azzahra • Periode 24 – 29 Maret 2025</p>
                </div>
                <a href="#" class="inline-flex items-center gap-1 text-[12px] font-bold text-ember-600 hover:text-ember-700">
                    Kalender Lengkap
                    @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                </a>
            </header>

            <ul class="divide-y divide-bark-100">
                @foreach ($jadwal as [$hari, $tgl, $judul, $chip, $meta, $metode, $metodeTone])
                    <li class="flex items-start gap-3 px-5 py-3.5">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-bark-800 text-center leading-none text-white">
                            <span class="text-[8.5px] font-bold uppercase text-bark-300">{{ $hari }}</span>
                            <span class="mt-0.5 text-[15px] font-bold">{{ $tgl }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <p class="text-[13.5px] font-bold text-bark-800">{{ $judul }}</p>
                                @if ($chip)
                                    <span @class([
                                        'rounded-md px-1.5 py-[2px] text-[9.5px] font-bold',
                                        'bg-gold text-espresso' => $chip === 'Hari Ini',
                                        'border border-ember-300 bg-ember-50 text-ember-600' => $chip === 'Wajib',
                                    ])>{{ $chip }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-[11.5px] leading-relaxed text-bark-400">{{ $meta }}</p>
                        </div>
                        <span @class(['shrink-0 rounded-md px-2.5 py-1.5 text-[10.5px] font-bold', $metodeTone])>{{ $metode }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Kanan: pengumuman + presensi --}}
        <div class="space-y-6">

            <section class="rounded-2xl bg-espresso px-5 py-5">
                <p class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.15em] text-gold">
                    @include('layouts.partials.icon', ['name' => 'megaphone', 'class' => 'h-3.5 w-3.5'])
                    Pengumuman Khusus Orang Tua
                </p>
                <h2 class="mt-2 text-[16px] font-bold leading-snug text-white">Agenda Konsultasi Triwulan</h2>
                <p class="mt-1.5 text-[12px] leading-relaxed text-bark-300">Bimbel Qualify mengundang Bapak/Ibu Hendra Gunawan pada:</p>
                <ul class="mt-2.5 space-y-1.5 text-[12px] text-bark-200">
                    <li class="flex items-start gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                        Sabtu, 05 April 2025 • 09.30 – 11.30 WIB
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                        Ruang Rapat &amp; Sesi Tanya Jawab Langsung (Wali Murid)
                    </li>
                </ul>
                <p class="mt-2.5 text-[11.5px] leading-relaxed text-bark-300">Pembahasan: Program Studi bersama tim pakar psikolog pendidikan.</p>

                <div class="mt-4 flex items-center gap-2">
                    <button type="button" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[12.5px] font-bold text-espresso transition hover:brightness-95">
                        Konfirmasi Kehadiran
                    </button>
                    <button type="button" aria-label="Bagikan pengumuman" class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-white/20 text-bark-200 transition hover:bg-white/10">
                        @include('layouts.partials.icon', ['name' => 'share', 'class' => 'h-4 w-4'])
                    </button>
                </div>
            </section>

            <section class="rounded-2xl border border-bark-100 bg-white px-5 py-4">
                <header class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-[14.5px] font-bold text-bark-800">Presensi Tap-In &amp; Aktivitas</h2>
                        <p class="mt-0.5 text-[11.5px] text-bark-400">Notifikasi langsung realtime</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10.5px] font-bold text-emerald-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Live
                    </span>
                </header>

                <ul class="mt-4 space-y-4">
                    @foreach ($presensi as [$dot, $judul, $waktu, $detail, $sub])
                        <li class="flex items-start gap-2.5">
                            <span @class(['mt-1.5 h-2 w-2 shrink-0 rounded-full', $dot])></span>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center justify-between gap-x-2">
                                    <p class="text-[12.5px] font-bold text-bark-800">{{ $judul }}</p>
                                    <p class="text-[10.5px] text-bark-400">{{ $waktu }}</p>
                                </div>
                                <p class="mt-0.5 text-[11.5px] leading-relaxed text-bark-500">{{ $detail }}</p>
                                <p class="mt-0.5 text-[11px] text-bark-400">{{ $sub }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection
