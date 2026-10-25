@extends('layouts.admin')

@section('title', 'Absensi & Jadwal')

{{-- Halaman masih memakai data mock UI sesuai rancangan docs/absensi-admin.png;
     sambungkan ke tabel jadwal sesi, presensi tentor/siswa, dan pengajuan izin begitu modulnya tersedia --}}
@section('content')
@php
    $sesi_pil = [
        'sore' => [
            'label' => 'Sesi Sore',
            'rentang' => '(15:30 - 18:00)',
            'judul' => 'Daftar Kelas — Sesi Sore (15:30 - 17:30)',
            'jumlah' => 8,
        ],
        'malam' => [
            'label' => 'Sesi Malam',
            'rentang' => '(18:30 - 20:00)',
            'judul' => 'Daftar Kelas — Sesi Malam (18:30 - 20:00)',
            'jumlah' => 6,
        ],
    ];

    $kpis = [
        [
            'label' => 'Total Sesi Hari Ini',
            'kunci' => null,
            'nilai' => '14',
            'satuan' => 'Kelas',
            'satuan_kelas' => 'text-muted',
            'ikon' => 'calendar',
            'tile' => 'bg-sandgold text-bronze',
            'aksen' => 'border-t-gold',
            'gelap' => false,
            'catatan' => 'Sesi Sore: 8',
            'catatan_2' => 'Sesi Malam: 6',
            'lencana' => null,
            'tautan' => null,
        ],
        [
            'label' => 'Presensi Siswa',
            'kunci' => null,
            'nilai' => '94.8%',
            'satuan' => '',
            'satuan_kelas' => '',
            'ikon' => 'users',
            'tile' => 'bg-palegold text-espresso',
            'aksen' => 'border-t-gold',
            'gelap' => false,
            'catatan' => '324 Hadir',
            'catatan_2' => '18 Tidak Hadir',
            'lencana' => null,
            'tautan' => null,
        ],
        [
            'label' => 'Kehadiran Tentor',
            'kunci' => null,
            'nilai' => '14 / 14',
            'satuan' => 'Lengkap',
            'satuan_kelas' => 'text-gold',
            'ikon' => 'user-check',
            'tile' => 'bg-gold text-espresso',
            'aksen' => 'border-t-espresso',
            'gelap' => true,
            'catatan' => '100% Mengajar',
            'catatan_2' => null,
            'lencana' => 'Tepat Waktu',
            'tautan' => null,
        ],
        [
            'label' => 'Pengajuan Izin / Sakit',
            'kunci' => 'izin',
            'nilai' => '3',
            'satuan' => 'Menunggu',
            'satuan_kelas' => 'text-bronze',
            'ikon' => 'mail',
            'tile' => 'bg-sandgold text-bronze',
            'aksen' => 'border-t-gold',
            'gelap' => false,
            'catatan' => 'Perlu verifikasi admin',
            'catatan_2' => null,
            'lencana' => null,
            'tautan' => 'Lihat Panel',
        ],
    ];

    $avatar_pil = [
        'espresso' => 'bg-espresso text-white',
        'palegold' => 'bg-palegold text-espresso',
        'sand' => 'bg-sand text-bark-500',
    ];

    $jadwal = [
        [
            'sesi' => 'sore', 'waktu' => '15:30 - 17:00', 'mapel' => 'Matematika',
            'tentor' => 'Dimas Wahyu, S.Pd', 'inisial' => 'DW', 'avatar' => 'espresso',
            'checkin' => '15:15', 'rombel' => '12-IPA-1', 'ruang' => 'R. Bima (Lt. 2)', 'ruang_key' => 'bima',
        ],
        [
            'sesi' => 'sore', 'waktu' => '15:45 - 17:15', 'mapel' => 'Fisika',
            'tentor' => 'Rina Novianti, M.Si', 'inisial' => 'RN', 'avatar' => 'palegold',
            'checkin' => '15:35', 'rombel' => '11-IPA-2', 'ruang' => 'R. Gajah Mada (Lt. 1)', 'ruang_key' => 'gajah-mada',
        ],
        [
            'sesi' => 'sore', 'waktu' => '15:30 - 17:00', 'mapel' => 'Bahasa Inggris',
            'tentor' => 'Fajar Anshori, B.Ed', 'inisial' => 'FA', 'avatar' => 'sand',
            'checkin' => '15:20', 'rombel' => '12-IPS-1', 'ruang' => 'R. Diponegoro (Lt. 1)', 'ruang_key' => 'diponegoro',
        ],
        [
            'sesi' => 'sore', 'waktu' => '16:00 - 17:30', 'mapel' => 'Kimia',
            'tentor' => 'Dr. Azhar Pratama', 'inisial' => 'AZ', 'avatar' => 'espresso',
            'checkin' => '15:50', 'rombel' => '10-IPA-1', 'ruang' => 'R. Cut Nyak Dien (Lt. 2)', 'ruang_key' => 'cut-nyak-dien',
        ],
        [
            'sesi' => 'sore', 'waktu' => '15:30 - 17:00', 'mapel' => 'Bahasa Indonesia',
            'tentor' => 'Sdri. Nur Aini, S.Pd', 'inisial' => 'NA', 'avatar' => 'palegold',
            'checkin' => '15:25', 'rombel' => '9-SMP-A', 'ruang' => 'R. Pattimura (Lt. 1)', 'ruang_key' => 'pattimura',
        ],
        [
            'sesi' => 'sore', 'waktu' => '15:45 - 17:15', 'mapel' => 'Biologi',
            'tentor' => 'Ust. Dwi Hermanto, M.Pd', 'inisial' => 'DH', 'avatar' => 'sand',
            'checkin' => '15:40', 'rombel' => '11-IPA-1', 'ruang' => 'R. Sultan Agung (Lt. 3)', 'ruang_key' => 'sultan-agung',
        ],
        [
            'sesi' => 'sore', 'waktu' => '16:00 - 17:30', 'mapel' => 'Matematika OSN',
            'tentor' => 'Ust. Ahmad Fauzi, S.Pd', 'inisial' => 'AF', 'avatar' => 'espresso',
            'checkin' => '15:55', 'rombel' => 'SD-OSN-Bintang', 'ruang' => 'R. Jayawijaya (Lt. 2)', 'ruang_key' => 'jayawijaya',
        ],
        [
            'sesi' => 'sore', 'waktu' => '16:00 - 17:30', 'mapel' => 'Bahasa Inggris SD',
            'tentor' => 'Sdri. Laila Hasanah, S.Pd', 'inisial' => 'LH', 'avatar' => 'palegold',
            'checkin' => '15:58', 'rombel' => '5-SD-B', 'ruang' => 'R. Hasanuddin (Lt. 1)', 'ruang_key' => 'hasanuddin',
        ],
        [
            'sesi' => 'malam', 'waktu' => '18:30 - 20:00', 'mapel' => 'Matematika',
            'tentor' => 'Ust. Rizky Maulana, M.Pd', 'inisial' => 'RM', 'avatar' => 'espresso',
            'checkin' => null, 'rombel' => '12-IPA-2', 'ruang' => 'R. Bima (Lt. 2)', 'ruang_key' => 'bima',
        ],
        [
            'sesi' => 'malam', 'waktu' => '18:30 - 20:00', 'mapel' => 'Fisika',
            'tentor' => 'Sdri. Maya Anggraini, M.Si', 'inisial' => 'MA', 'avatar' => 'palegold',
            'checkin' => null, 'rombel' => '11-IPA-1', 'ruang' => 'R. Gajah Mada (Lt. 1)', 'ruang_key' => 'gajah-mada',
        ],
        [
            'sesi' => 'malam', 'waktu' => '18:45 - 20:00', 'mapel' => 'Bahasa Indonesia',
            'tentor' => 'Ust. Yusuf Hakim, S.Pd', 'inisial' => 'YH', 'avatar' => 'sand',
            'checkin' => null, 'rombel' => '9-SMP-B', 'ruang' => 'R. Diponegoro (Lt. 1)', 'ruang_key' => 'diponegoro',
        ],
        [
            'sesi' => 'malam', 'waktu' => '18:45 - 20:00', 'mapel' => 'Ekonomi',
            'tentor' => 'Sdri. Fitri Handayani, S.E.', 'inisial' => 'FH', 'avatar' => 'palegold',
            'checkin' => null, 'rombel' => '12-IPS-2', 'ruang' => 'R. Cut Nyak Dien (Lt. 2)', 'ruang_key' => 'cut-nyak-dien',
        ],
        [
            'sesi' => 'malam', 'waktu' => '19:00 - 20:00', 'mapel' => 'Kimia',
            'tentor' => 'Ust. Ilham Saputra, S.Si', 'inisial' => 'IS', 'avatar' => 'espresso',
            'checkin' => null, 'rombel' => '10-IPA-2', 'ruang' => 'R. Pattimura (Lt. 1)', 'ruang_key' => 'pattimura',
        ],
        [
            'sesi' => 'malam', 'waktu' => '19:00 - 20:00', 'mapel' => 'Sosiologi',
            'tentor' => 'Sdri. Rahma Dewi Lestari, S.Sos', 'inisial' => 'RL', 'avatar' => 'sand',
            'checkin' => null, 'rombel' => '11-IPS-1', 'ruang' => 'R. Sultan Agung (Lt. 3)', 'ruang_key' => 'sultan-agung',
        ],
    ];

    $izin_pil = [
        'sakit' => ['label' => 'Sakit', 'kelas' => 'bg-ember-100 text-ember-700'],
        'lomba' => ['label' => 'Izin Lomba', 'kelas' => 'bg-gold text-espresso'],
        'pribadi' => ['label' => 'Izin Pribadi', 'kelas' => 'bg-sandgold text-bronze'],
    ];

    $pengajuan = [
        [
            'nama' => 'Aura Clarissa Putri', 'jenis' => 'sakit',
            'kelas' => 'Kelas 11-IPA-2', 'wali' => 'Wali: Ibu Dewi R.',
        ],
        [
            'nama' => 'Muhammad Farrel', 'jenis' => 'lomba',
            'kelas' => 'Kelas 11-IPA-2', 'wali' => 'Wali: Bpk. Hendra',
        ],
        [
            'nama' => 'Nabila Ayu Prameswari', 'jenis' => 'pribadi',
            'kelas' => 'Kelas 10-IPA-1', 'wali' => 'Wali: Ibu Sri H.',
        ],
    ];

    $ruangan = [
        ['nama' => 'R. Bima', 'status' => 'Sesi Sore Aktif', 'aktif' => true],
        ['nama' => 'R. Gajah Mada', 'status' => 'Sesi Sore Aktif', 'aktif' => true],
        ['nama' => 'R. Diponegoro', 'status' => 'Sesi Sore Aktif', 'aktif' => true],
        ['nama' => 'R. Cut Nyak Dien', 'status' => 'Siap Sesi Malam', 'aktif' => false],
    ];

    $pekan = [
        ['hari' => 'Senin', 'sesi' => '12 Sesi', 'status' => '92% Hadir', 'status_kelas' => 'text-muted', 'hari_ini' => false],
        ['hari' => 'Selasa', 'sesi' => '14 Sesi', 'status' => '95% Hadir', 'status_kelas' => 'text-muted', 'hari_ini' => false],
        ['hari' => 'Rabu', 'sesi' => '15 Sesi', 'status' => '93% Hadir', 'status_kelas' => 'text-muted', 'hari_ini' => false],
        ['hari' => 'Kamis (Hari ini)', 'sesi' => '14 Sesi', 'status' => '94.8% Hadir', 'status_kelas' => 'text-ember-700', 'hari_ini' => true],
        ['hari' => 'Jumat', 'sesi' => '10 Sesi', 'status' => 'Terjadwal', 'status_kelas' => 'text-muted', 'hari_ini' => false],
        ['hari' => 'Sabtu', 'sesi' => '18 Sesi', 'status' => 'Terjadwal Akhir', 'status_kelas' => 'text-muted', 'hari_ini' => false],
    ];
@endphp

{{-- Kepala halaman --}}
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <p class="flex flex-wrap items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-700">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                Modul 5.3 Operasional &amp; Akademik
                <span class="text-bark-300" aria-hidden="true">•</span>
                Bimbel Qualify T.A. 2024/2025
            </p>
            <h1 class="mt-2.5 text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">
                Manajemen Jadwal &amp; Presensi Bimbel
            </h1>
            <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Pemantauan jadwal sesi kelas, presensi kehadiran tentor dan siswa, serta pencatatan izin / sakit / alpa secara terpadu.
            </p>
        </div>

        <div class="flex shrink-0 items-start gap-2.5">
            <button type="button" id="cetak-rekap"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-line bg-shell px-4 py-3 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'printer', 'class' => 'h-4 w-4 text-bronze'])
                Cetak Rekap
            </button>
            <button type="button" id="segarkan-data" aria-label="Muat ulang jadwal dan presensi terbaru"
                    class="grid h-[46px] w-[46px] place-items-center rounded-lg bg-espresso text-gold transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'refresh', 'class' => 'h-5 w-5'])
            </button>
        </div>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Empat angka pokok hari ini --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kpis as $kpi)
            <article @class([
                'rounded-xl border border-line border-t-[3px] p-4',
                'bg-espresso border-espresso' => $kpi['gelap'],
                'bg-white' => ! $kpi['gelap'],
                $kpi['aksen'] => ! $kpi['gelap'],
            ])>
                <div class="flex items-start justify-between gap-3">
                    <p @class([
                        'text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em]',
                        'text-palegold' => $kpi['gelap'],
                        'text-muted' => ! $kpi['gelap'],
                    ])>{{ $kpi['label'] }}</p>
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ $kpi['tile'] }}">
                        @include('layouts.partials.icon', ['name' => $kpi['ikon'], 'class' => 'h-4.5 w-4.5'])
                    </span>
                </div>

                <p class="mt-3 flex items-baseline gap-1.5">
                    <span @class([
                        'text-[27px] font-extrabold leading-none tracking-tight tabular-nums',
                        'text-white' => $kpi['gelap'],
                        'text-ink' => ! $kpi['gelap'],
                    ]) @if ($kpi['kunci']) data-kunci="{{ $kpi['kunci'] }}" @endif>{{ $kpi['nilai'] }}</span>
                    @if ($kpi['satuan'] !== '')
                        <span class="text-[13px] font-bold leading-none {{ $kpi['satuan_kelas'] }}">{{ $kpi['satuan'] }}</span>
                    @endif
                </p>

                <p @class([
                    'mt-3.5 flex flex-wrap items-center gap-x-2 gap-y-1.5 text-[11.5px] font-medium leading-snug',
                    'text-bark-200' => $kpi['gelap'],
                    'text-bark-700' => ! $kpi['gelap'],
                ])>
                    <span>{{ $kpi['catatan'] }}</span>
                    @if ($kpi['catatan_2'])
                        <span class="text-bark-300" aria-hidden="true">•</span>
                        <span>{{ $kpi['catatan_2'] }}</span>
                    @endif
                    @if ($kpi['lencana'])
                        <span class="rounded-md bg-white/10 px-2 py-1 text-[10.5px] font-bold leading-none text-gold">{{ $kpi['lencana'] }}</span>
                    @endif
                    @if ($kpi['tautan'])
                        <a href="#verifikasi-izin" class="font-bold text-bronze underline decoration-bronze/40 underline-offset-2 transition hover:text-gold hover:decoration-gold">
                            {{ $kpi['tautan'] }}
                        </a>
                    @endif
                </p>
            </article>
        @endforeach
    </div>

    <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-3">

        {{-- Kolom utama: jadwal sesi --}}
        <div class="space-y-5 xl:col-span-2">

            {{-- Pemilih sesi & tanggal --}}
            <section class="rounded-2xl border border-line bg-white p-3">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex min-w-0 flex-1 basis-72 gap-1 rounded-xl bg-sand p-1" role="group" aria-label="Pilih sesi belajar">
                        @foreach ($sesi_pil as $key => $sesi)
                            <button type="button" data-sesi-btn data-sesi="{{ $key }}"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                    @class([
                                        'flex min-w-0 flex-1 items-center gap-2 rounded-lg px-3 py-2 transition',
                                        'border border-line bg-white shadow-[0_1px_2px_rgba(20,20,20,0.06)]' => $loop->first,
                                        'hover:bg-white/60' => ! $loop->first,
                                    ])>
                                <span data-titik @class([
                                    'h-1.5 w-1.5 shrink-0 rounded-full',
                                    'bg-gold' => $loop->first,
                                    'bg-bark-200' => ! $loop->first,
                                ])}></span>
                                <span class="min-w-0 flex-1 text-left leading-tight">
                                    <span data-nama @class([
                                        'block text-[12.5px]',
                                        'font-bold text-ink' => $loop->first,
                                        'font-semibold text-bark-400' => ! $loop->first,
                                    ])>{{ $sesi['label'] }}</span>
                                    <span data-rentang @class([
                                        'block text-[10.5px]',
                                        'font-medium text-muted' => $loop->first,
                                        'text-bark-300' => ! $loop->first,
                                    ])>{{ $sesi['rentang'] }}</span>
                                </span>
                                <span data-angka @class([
                                    'shrink-0 rounded-full px-1.5 py-0.5 text-[10px] font-bold leading-none',
                                    'bg-gold text-espresso' => $loop->first,
                                    'border border-line bg-white text-muted' => ! $loop->first,
                                ])>{{ $sesi['jumlah'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <p class="flex shrink-0 items-center gap-2 rounded-lg border border-line bg-white px-3 py-2.5 text-[12.5px] font-semibold text-ink">
                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4 text-bronze'])
                        <time datetime="2026-10-24">Kamis, 24/10/2026</time>                    </p>
                </div>
            </section>

            {{-- Pencarian & filter ruang --}}
            <section class="rounded-2xl border border-line bg-white p-3">
                <div class="flex items-center gap-2.5">
                    <div class="relative min-w-0 flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                            @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                        </span>
                        <input type="search" id="jadwal-cari" aria-label="Cari mata pelajaran, tentor, rombel, atau ruang"
                               placeholder="Cari mata pelajaran, tentor, rombel, atau ruang..."
                               class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                    </div>

                    <button type="button" id="jadwal-filter-toggle" aria-expanded="false" aria-controls="filter-ruang"
                            class="inline-flex h-10 shrink-0 items-center gap-2 rounded-lg border border-line bg-white px-3.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand">
                        @include('layouts.partials.icon', ['name' => 'filter', 'class' => 'h-4 w-4 text-bronze'])
                        <span class="hidden sm:inline">Filter Ruang</span>
                    </button>
                </div>

                <div id="filter-ruang" class="hidden border-t border-line pt-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="shrink-0 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Ruang</span>
                        <div data-ruang-chips class="flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </section>

            {{-- Daftar kelas hari ini --}}
            <section class="overflow-hidden rounded-2xl border border-line bg-white">
                <header class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 px-4 pt-4 sm:px-5">
                    <h2 class="flex min-w-0 items-center gap-2.5 text-[16px] font-bold leading-snug text-ink">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                            @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-4 w-4'])
                        </span>
                        <span data-judul-sesi>{{ $sesi_pil['sore']['judul'] }}</span>
                    </h2>
                    <p data-jumlah-sesi class="shrink-0 text-[12px] font-semibold text-muted">{{ $sesi_pil['sore']['jumlah'] }} Kelas Terjadwal</p>
                </header>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[820px] border-collapse text-left">
                        <thead>
                            <tr class="bg-shell">
                                <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Waktu</th>
                                <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Mata Pelajaran</th>
                                <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Tentor</th>
                                <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Rombel / Kelas</th>
                                <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwal as $baris)
                                <tr data-baris
                                    data-sesi="{{ $baris['sesi'] }}"
                                    data-ruang="{{ $baris['ruang_key'] }}"
                                    data-ruang-label="{{ $baris['ruang'] }}"
                                    data-cari="{{ strtolower($baris['waktu'] . ' ' . $baris['mapel'] . ' ' . $baris['tentor'] . ' ' . $baris['rombel'] . ' ' . $baris['ruang']) }}"
                                    @class([
                                        'border-t border-line align-top odd:bg-white even:bg-shell',
                                        'hidden' => $baris['sesi'] === 'malam',
                                    ])>
                                    <td class="px-5 py-4 text-[12.5px] font-bold tabular-nums text-ink">{{ $baris['waktu'] }}</td>
                                    <td class="px-5 py-4 text-[13px] font-bold leading-snug text-ink">{{ $baris['mapel'] }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-[11.5px] font-bold {{ $avatar_pil[$baris['avatar']] }}" aria-hidden="true">
                                                {{ $baris['inisial'] }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-[13px] font-bold leading-snug text-ink">{{ $baris['tentor'] }}</p>
                                                @if ($baris['checkin'])
                                                    <p class="mt-1 text-[11.5px] font-bold leading-snug text-bronze">Check-in {{ $baris['checkin'] }}</p>
                                                @else
                                                    <p class="mt-1 text-[11.5px] leading-snug text-bark-400">Check-in belum dibuka</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-md border border-line bg-white px-2 py-1 text-[11.5px] font-bold text-ink">{{ $baris['rombel'] }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-md border border-line bg-white px-2 py-1 text-[11.5px] font-semibold text-ink">{{ $baris['ruang'] }}</span>
                                    </td>
                                </tr>
                            @endforeach

                            <tr id="jadwal-kosong" class="hidden border-t border-line">
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <p class="text-[13.5px] font-bold text-ink">Tidak ada kelas yang cocok</p>
                                    <p class="mt-1.5 text-[12.5px] text-muted">Ubah kata kunci, pilih ruang lain, atau kembalikan filter ke Semua.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p data-hitung aria-live="polite" class="text-[12px] leading-snug text-muted">
                        Menampilkan <span class="font-bold text-ink">4</span> dari <span class="font-bold text-ink">8</span> kelas Sesi Sore
                    </p>
                    <button type="button" data-luas
                            class="text-[12.5px] font-semibold text-bronze transition hover:text-espresso focus-visible:underline">
                        Lihat Semua Sesi Sore
                    </button>
                </footer>
            </section>
        </div>

        {{-- Rail kanan: verifikasi izin & ruangan --}}
        <div class="space-y-5">

            <section id="verifikasi-izin" class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-center gap-2.5">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'mail', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <h2 class="min-w-0 text-[15px] font-bold leading-snug text-ink">Verifikasi Izin &amp; Sakit</h2>
                    <span data-jumlah-izin class="ml-auto shrink-0 rounded-md bg-gold px-2 py-1 text-[10.5px] font-bold leading-none text-espresso">
                        {{ count($pengajuan) }} Menunggu
                    </span>
                </header>

                <div data-daftar-izin class="mt-4 space-y-3">
                    @foreach ($pengajuan as $ajuan)
                        @php $izin = $izin_pil[$ajuan['jenis']]; @endphp
                        <article data-izin class="rounded-xl border border-line bg-cream px-4 py-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <p class="min-w-0 text-[13.5px] font-bold leading-snug text-ink">{{ $ajuan['nama'] }}</p>
                                <span class="shrink-0 rounded-md px-2 py-1 text-[10.5px] font-bold leading-none {{ $izin['kelas'] }}">{{ $izin['label'] }}</span>
                            </div>
                            <p class="mt-1.5 text-[11.5px] leading-snug text-muted">
                                {{ $ajuan['kelas'] }} <span class="text-bark-300">•</span> {{ $ajuan['wali'] }}
                            </p>
                            <div class="mt-3 flex justify-end gap-2">
                                <button type="button" data-aksi="tolak"
                                        class="rounded-lg border border-line bg-white px-3.5 py-2 text-[12px] font-semibold text-espresso transition hover:bg-sand">
                                    Tolak
                                </button>
                                <button type="button" data-aksi="setujui"
                                        class="rounded-lg bg-espresso px-3.5 py-2 text-[12px] font-semibold text-white transition hover:bg-bark-800">
                                    Setujui
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div data-izin-kosong class="mt-4 hidden rounded-xl border border-dashed border-line bg-cream px-4 py-7 text-center">
                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'mx-auto h-6 w-6 text-bronze'])
                    <p class="mt-2.5 text-[13px] font-bold text-ink">Semua pengajuan sudah diputuskan</p>
                    <p class="mt-1.5 text-[12px] leading-relaxed text-muted">Tidak ada izin atau sakit yang menunggu verifikasi hari ini.</p>
                </div>

                <p data-status-izin role="status" aria-live="polite" class="mt-3 text-[11.5px] font-semibold leading-snug text-bronze"></p>
            </section>

            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-center gap-2.5">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'building', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <h2 class="min-w-0 text-[15px] font-bold leading-snug text-ink">Ketersediaan Ruangan</h2>
                    <span class="ml-auto shrink-0 rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold leading-none text-bronze">
                        {{ count($ruangan) }} Ruang Aktif
                    </span>
                </header>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    @foreach ($ruangan as $ruang)
                        <div class="rounded-xl bg-cream px-3.5 py-3">
                            <p class="text-[13px] font-bold leading-snug text-ink">{{ $ruang['nama'] }}</p>
                            <p class="mt-1 text-[11.5px] font-bold leading-snug {{ $ruang['aktif'] ? 'text-bronze' : 'text-muted' }}">{{ $ruang['status'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    {{-- Kepadatan jadwal pekan ini --}}
    <section class="rounded-2xl border border-line bg-white p-5">
        <header class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
            <h2 class="flex min-w-0 items-center gap-2.5 text-[15px] font-bold leading-snug text-ink">
                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4'])
                </span>
                Kepadatan Jadwal Pekan Ini (Oktober 2024)
            </h2>
            <p class="shrink-0 text-[12px] font-semibold text-bronze">Rata-rata 13.5 Sesi / Hari</p>
        </header>

        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($pekan as $hari)
                <article @class([
                    'rounded-xl px-3.5 py-3',
                    'border border-ember-300 bg-white' => $hari['hari_ini'],
                    'border border-transparent bg-shell' => ! $hari['hari_ini'],
                ])>
                    <p @class([
                        'text-[11.5px] font-semibold leading-snug',
                        'font-bold text-ember-700' => $hari['hari_ini'],
                        'text-muted' => ! $hari['hari_ini'],
                    ])>{{ $hari['hari'] }}</p>
                    <p class="mt-2 text-[17px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $hari['sesi'] }}</p>
                    <p class="mt-2 text-[11px] font-semibold leading-snug {{ $hari['status_kelas'] }}">{{ $hari['status'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Catatan sistem --}}
    <section class="flex flex-col gap-2 rounded-2xl border border-line bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-muted">
            @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'mt-[1px] h-4 w-4 shrink-0 text-gold'])
            Jadwal sesi, presensi tentor, dan pengajuan izin cabang utama tersimpan dalam satu berkas rekap harian.
        </p>
        <p class="shrink-0 text-[11px] text-muted sm:text-right">
            Terakhir disinkronkan: <span data-sinkron class="font-bold text-ink">Hari ini, 15:02 WIB</span>
        </p>
    </section>
</div>

@push('styles')
<style>
    @media print {
        #admin-sidebar,
        #admin-backdrop,
        .no-print {
            display: none !important;
        }

        #admin-utama {
            height: auto !important;
            overflow: visible !important;
        }

        section {
            break-inside: avoid;
        }
    }
</style>
@endpush

<script>
    (() => {
        const infoSesi = @json($sesi_pil);
        const batasTampil = 4;

        const baris = Array.from(document.querySelectorAll('[data-baris]'));
        const tombolSesi = Array.from(document.querySelectorAll('[data-sesi-btn]'));
        const inputCari = document.getElementById('jadwal-cari');
        const tombolFilter = document.getElementById('jadwal-filter-toggle');
        const panelFilter = document.getElementById('filter-ruang');
        const wadahRuang = document.querySelector('[data-ruang-chips]');
        const judulSesi = document.querySelector('[data-judul-sesi]');
        const jumlahSesi = document.querySelector('[data-jumlah-sesi]');
        const hitung = document.querySelector('[data-hitung]');
        const tombolLuas = document.querySelector('[data-luas]');
        const barisKosong = document.getElementById('jadwal-kosong');

        let sesi = 'sore';
        let ruang = 'semua';
        let luas = false;

        const kelasSesiBtn = (aktif) => aktif
            ? 'flex min-w-0 flex-1 items-center gap-2 rounded-lg px-3 py-2 transition border border-line bg-white shadow-[0_1px_2px_rgba(20,20,20,0.06)]'
            : 'flex min-w-0 flex-1 items-center gap-2 rounded-lg px-3 py-2 transition hover:bg-white/60';

        const aturSesiBtn = (tombol) => {
            const aktif = tombol.dataset.sesi === sesi;

            tombol.setAttribute('aria-pressed', String(aktif));
            tombol.className = kelasSesiBtn(aktif);
            tombol.querySelector('[data-titik]').className = aktif
                ? 'h-1.5 w-1.5 shrink-0 rounded-full bg-gold'
                : 'h-1.5 w-1.5 shrink-0 rounded-full bg-bark-200';
            tombol.querySelector('[data-nama]').className = aktif
                ? 'block text-[12.5px] font-bold text-ink'
                : 'block text-[12.5px] font-semibold text-bark-400';
            tombol.querySelector('[data-rentang]').className = aktif
                ? 'block text-[10.5px] font-medium text-muted'
                : 'block text-[10.5px] text-bark-300';
            tombol.querySelector('[data-angka]').className = aktif
                ? 'shrink-0 rounded-full px-1.5 py-0.5 text-[10px] font-bold leading-none bg-gold text-espresso'
                : 'shrink-0 rounded-full px-1.5 py-0.5 text-[10px] font-bold leading-none border border-line bg-white text-muted';
        };

        const bangunRuang = () => {
            const daftar = [{ key: 'semua', label: 'Semua Ruang' }];

            baris.filter((row) => row.dataset.sesi === sesi).forEach((row) => {
                if (!daftar.some((item) => item.key === row.dataset.ruang)) {
                    daftar.push({ key: row.dataset.ruang, label: row.dataset.ruangLabel.replace(/\s\(.*\)$/, '') });
                }
            });

            wadahRuang.innerHTML = '';

            daftar.forEach((item) => {
                const aktif = item.key === ruang;
                const tombol = document.createElement('button');
                tombol.type = 'button';
                tombol.setAttribute('aria-pressed', String(aktif));
                tombol.className = aktif
                    ? 'rounded-lg bg-espresso px-3 py-1.5 text-[11.5px] font-semibold text-white transition'
                    : 'rounded-lg border border-line bg-white px-3 py-1.5 text-[11.5px] font-semibold text-espresso transition hover:bg-sand';
                tombol.textContent = item.label;
                tombol.addEventListener('click', () => {
                    ruang = item.key;
                    bangunRuang();
                    terapkan();
                });
                wadahRuang.appendChild(tombol);
            });
        };

        const terapkan = () => {
            const kata = inputCari.value.trim().toLowerCase();
            const tersaring = kata !== '' || ruang !== 'semua';
            const total = baris.filter((row) => row.dataset.sesi === sesi).length;
            let urutan = 0;
            let tampak = 0;

            baris.forEach((row) => {
                const cocok = row.dataset.sesi === sesi
                    && (ruang === 'semua' || row.dataset.ruang === ruang)
                    && (!kata || row.dataset.cari.includes(kata));

                if (cocok) {
                    urutan += 1;
                }

                const tampil = cocok && (tersaring || luas || urutan <= batasTampil);
                row.classList.toggle('hidden', !tampil);

                if (tampil) {
                    tampak += 1;
                }
            });

            barisKosong.classList.toggle('hidden', tampak > 0);
            judulSesi.textContent = infoSesi[sesi].judul;
            jumlahSesi.textContent = `${total} Kelas Terjadwal`;
            hitung.innerHTML = `Menampilkan <span class="font-bold text-ink">${tampak}</span>`
                + ` dari <span class="font-bold text-ink">${total}</span> kelas ${infoSesi[sesi].label}`;

            tombolLuas.classList.toggle('hidden', total <= batasTampil || tersaring);
            tombolLuas.textContent = luas
                ? `Tampilkan Ringkas ${infoSesi[sesi].label}`
                : `Lihat Semua ${infoSesi[sesi].label}`;
        };

        tombolSesi.forEach((tombol) => {
            aturSesiBtn(tombol);
            tombol.addEventListener('click', () => {
                sesi = tombol.dataset.sesi;
                ruang = 'semua';
                luas = false;
                tombolSesi.forEach(aturSesiBtn);
                bangunRuang();
                terapkan();
            });
        });

        inputCari.addEventListener('input', terapkan);

        tombolLuas.addEventListener('click', () => {
            luas = !luas;
            terapkan();
        });

        tombolFilter.addEventListener('click', () => {
            const membuka = panelFilter.classList.contains('hidden');

            panelFilter.classList.toggle('hidden', !membuka);
            tombolFilter.setAttribute('aria-expanded', String(membuka));

            if (membuka) {
                bangunRuang();
            } else {
                ruang = 'semua';
                terapkan();
            }
        });

        bangunRuang();
        terapkan();

        // ---- Verifikasi izin & sakit ----
        const daftarIzin = document.querySelector('[data-daftar-izin]');
        const kotakKosong = document.querySelector('[data-izin-kosong]');
        const lencanaIzin = document.querySelector('[data-jumlah-izin]');
        const nilaiIzin = document.querySelector('[data-kunci="izin"]');
        const statusIzin = document.querySelector('[data-status-izin]');

        const perbaruiIzin = () => {
            const sisa = daftarIzin.querySelectorAll('[data-izin]').length;

            lencanaIzin.textContent = `${sisa} Menunggu`;
            lencanaIzin.classList.toggle('hidden', sisa === 0);
            kotakKosong.classList.toggle('hidden', sisa > 0);
            daftarIzin.classList.toggle('hidden', sisa === 0);
            nilaiIzin.textContent = String(sisa);
        };

        daftarIzin.addEventListener('click', (event) => {
            const tombol = event.target.closest('[data-aksi]');
            if (!tombol) {
                return;
            }

            const kartu = tombol.closest('[data-izin]');
            const nama = kartu.querySelector('p').textContent.trim();
            const disetujui = tombol.dataset.aksi === 'setujui';

            kartu.remove();
            perbaruiIzin();

            const sisa = daftarIzin.querySelectorAll('[data-izin]').length;
            statusIzin.textContent = `Pengajuan ${nama} ${disetujui ? 'disetujui' : 'ditolak'}.`
                + ` ${sisa} pengajuan masih menunggu verifikasi.`;
        });

        // ---- Cetak & sinkronisasi ----
        document.getElementById('cetak-rekap').addEventListener('click', () => window.print());

        document.getElementById('segarkan-data').addEventListener('click', () => {
            const kini = new Date();
            const jam = `${String(kini.getHours()).padStart(2, '0')}:${String(kini.getMinutes()).padStart(2, '0')}`;
            document.querySelector('[data-sinkron]').textContent = `Hari ini, ${jam} WIB`;
        });
    })();
</script>
@endsection
