@extends('layouts.admin')

@section('title', 'Absensi & Jadwal')

@section('content')
@php
    $avatar_pil = [
        'espresso' => 'bg-espresso text-white',
        'palegold' => 'bg-palegold text-espresso',
        'sand' => 'bg-sand text-bark-500',
    ];

    $izin_pil = [
        'sakit' => ['label' => 'Sakit', 'kelas' => 'bg-ember-100 text-ember-700'],
        'lomba' => ['label' => 'Izin Lomba', 'kelas' => 'bg-gold text-espresso'],
        'pribadi' => ['label' => 'Izin Pribadi', 'kelas' => 'bg-sandgold text-bronze'],
    ];

    $kunciIzin = function (?string $type) use ($izin_pil) {
        $type = strtolower((string) $type);

        foreach (array_keys($izin_pil) as $kunci) {
            if (str_contains($type, $kunci)) {
                return $kunci;
            }
        }

        return 'pribadi';
    };
@endphp

{{-- Kepala halaman --}}
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <p class="flex flex-wrap items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-700">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                Modul 5.3 Operasional &amp; Akademik
                <span class="text-bark-300" aria-hidden="true">•</span>
                Bimbel Qualify
            </p>
            <h1 class="mt-2.5 text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">
                Manajemen Jadwal &amp; Presensi Bimbel
            </h1>
            <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Pemantauan jadwal sesi kelas, presensi kehadiran tentor dan siswa, serta pencatatan izin / sakit / alpa secara terpadu.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <form method="GET" action="{{ route('admin.absensiJadwal') }}" class="flex items-center gap-2.5">
                <label class="flex items-center gap-2 rounded-lg border border-line bg-white px-3 py-2.5 text-[12.5px] font-semibold text-ink">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4 text-bronze'])
                    <input type="date" name="tanggal" value="{{ $tanggal->toDateString() }}"
                           onchange="this.form.submit()"
                           aria-label="Pilih tanggal jadwal"
                           class="bg-transparent text-[12.5px] font-semibold text-ink focus:outline-none">
                </label>
                @if (! $tanggal->isToday())
                    <a href="{{ route('admin.absensiJadwal') }}"
                       class="rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand">
                        Hari ini
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.manajemenJadwal') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-espresso px-4 py-2.5 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4 text-gold'])
                Kelola Jadwal
            </a>
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
                    ])>{{ $kpi['nilai'] }}</span>
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
                    @if ($kpi['catatan2'])
                        <span class="text-bark-300" aria-hidden="true">•</span>
                        <span>{{ $kpi['catatan2'] }}</span>
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

            {{-- Pemilih sesi --}}
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
                                        'block text-[10.5px] font-medium text-muted' => $loop->first,
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
                        <time datetime="{{ $tanggal->toDateString() }}">{{ $tanggal->translatedFormat('l, d/m/Y') }}</time>
                    </p>
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

            {{-- Daftar kelas pada tanggal aktif --}}
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
                                <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwal as $baris)
                                <tr data-baris
                                    data-sesi="{{ $baris['sesi'] }}"
                                    data-ruang="{{ $baris['ruang_key'] }}"
                                    data-ruang-label="{{ $baris['ruang'] }}"
                                    data-cari="{{ strtolower($baris['waktu'].' '.$baris['mapel'].' '.$baris['tentor'].' '.$baris['rombel'].' '.$baris['ruang']) }}"
                                    @class([
                                        'border-t border-line align-top odd:bg-white even:bg-shell',
                                        'hidden' => $baris['sesi'] === 'malam',
                                        'opacity-60' => $baris['batal'],
                                    ])>
                                    <td class="px-5 py-4">
                                        <span class="text-[12.5px] font-bold tabular-nums text-ink">{{ $baris['waktu'] }}</span>
                                        <span @class([
                                            'mt-1.5 inline-flex rounded-md px-2 py-0.5 text-[10.5px] font-bold leading-tight',
                                            'bg-sandgold text-bronze' => $baris['status'] === 'terjadwal',
                                            'bg-palegold text-espresso' => $baris['status'] === 'berlangsung',
                                            'bg-forest-50 text-forest-600' => $baris['status'] === 'selesai',
                                            'bg-ember-100 text-ember-600' => $baris['status'] === 'dibatalkan',
                                        ])>{{ ucfirst($baris['status']) }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-[13px] font-bold leading-snug text-ink">{{ $baris['mapel'] }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-[11.5px] font-bold {{ $avatar_pil[array_keys($avatar_pil)[$loop->index % count($avatar_pil)]] }}" aria-hidden="true">
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
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($baris['batal'])
                                                <span class="rounded-lg bg-ember-100 px-2.5 py-1.5 text-[11px] font-bold leading-tight text-ember-600">Dibatalkan</span>
                                            @else
                                                <a href="{{ route('admin.manajemenJadwal.sesi.edit', $baris['id']) }}"
                                                   aria-label="Ubah sesi {{ $baris['mapel'] }} di {{ $baris['rombel'] }}"
                                                   class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                                    @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <tr id="jadwal-kosong" class="hidden border-t border-line">
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <div data-kosong-tanpa-sesi class="hidden">
                                        @if ($sesiBerikutnya)
                                            <p class="text-[13.5px] font-bold text-ink">
                                                Belum ada sesi terjadwal pada {{ $tanggal->locale('id')->translatedFormat('l, d/m/Y') }}
                                            </p>
                                            <p class="mt-1.5 text-[12.5px] text-muted">
                                                Sesi berikutnya: {{ $sesiBerikutnya->tanggal->locale('id')->translatedFormat('l, d/m/Y') }}
                                                pukul {{ substr($sesiBerikutnya->start_time, 0, 5) }} ·
                                                {{ $sesiBerikutnya->subject?->name ?? 'Mata pelajaran' }} ·
                                                {{ $sesiBerikutnya->classGroup?->code ?? '-' }}
                                            </p>
                                            <a href="{{ route('admin.absensiJadwal', ['tanggal' => $sesiBerikutnya->tanggal->toDateString()]) }}"
                                               class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-espresso px-3.5 py-2 text-[12px] font-semibold text-white transition hover:bg-bark-800">
                                                Lihat Sesi {{ $sesiBerikutnya->tanggal->locale('id')->translatedFormat('d/m/Y') }}
                                            </a>
                                        @else
                                            <p class="text-[13.5px] font-bold text-ink">Belum ada sesi terjadwal</p>
                                            <p class="mt-1.5 text-[12.5px] text-muted">
                                                Buat aturan jadwal mingguan lalu generate sesi lewat
                                                <a href="{{ route('admin.manajemenJadwal') }}" class="font-semibold text-bronze underline underline-offset-2">Kelola Jadwal</a>.
                                            </p>
                                        @endif
                                    </div>
                                    <div data-kosong-filter>
                                        <p class="text-[13.5px] font-bold text-ink">Tidak ada kelas yang cocok</p>
                                        <p class="mt-1.5 text-[12.5px] text-muted">Ubah kata kunci, pilih ruang lain, atau kembalikan filter ke Semua.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p data-hitung aria-live="polite" class="text-[12px] leading-snug text-muted">
                        Menampilkan <span class="font-bold text-ink">0</span> dari <span class="font-bold text-ink">0</span> kelas Sesi Sore
                    </p>
                    <button type="button" data-luas
                            class="text-[12.5px] font-semibold text-bronze transition hover:text-espresso focus-visible:underline">
                        Lihat Semua Sesi Sore
                    </button>
                </footer>
            </section>

            {{-- Kepadatan jadwal sepekan --}}
            <section class="overflow-hidden rounded-2xl border border-line bg-white">
                <header class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 px-4 pt-4 sm:px-5">
                    <h2 class="flex min-w-0 items-center gap-2.5 text-[15px] font-bold leading-snug text-ink">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                            @include('layouts.partials.icon', ['name' => 'table', 'class' => 'h-4 w-4'])
                        </span>
                        Kepadatan Jadwal Pekan Ini
                    </h2>
                    <p class="shrink-0 text-[12px] font-semibold text-muted">{{ $tanggal->translatedFormat('d M Y') }}</p>
                </header>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[640px] border-collapse text-left">
                        <thead>
                            <tr class="bg-shell">
                                <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Hari</th>
                                <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Sesi</th>
                                <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pekan as $hari)
                                <tr @class(['border-t border-line odd:bg-white even:bg-shell', 'bg-sandgold/40' => $hari['hari_ini']])>
                                    <td class="px-5 py-3.5 text-[12.5px] font-bold text-ink">
                                        {{ $hari['hari'] }}
                                        @if ($hari['hari_ini'])
                                            <span class="ml-1.5 rounded-md bg-gold px-1.5 py-0.5 text-[10px] font-bold leading-none text-espresso">Hari ini</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-[12.5px] font-semibold text-bark-500">{{ $hari['sesi'] }} Sesi</td>
                                    <td class="px-5 py-3.5 text-[12.5px] font-semibold">
                                        @if ($hari['rate'] === null)
                                            <span class="text-muted">Belum ada presensi</span>
                                        @else
                                            <span class="font-bold {{ $hari['rate'] >= 90 ? 'text-forest-600' : 'text-bronze' }}">{{ $hari['rate'] }}% Hadir</span>
                                            <span class="text-bark-400"> ({{ $hari['hadir'] }}/{{ $hari['total'] }})</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Rail kanan: sesi berikutnya, verifikasi izin & ruangan --}}
        <div class="space-y-5">

            @if ($sesiBerikutnya)
                <section class="rounded-2xl border border-line bg-white p-5">
                    <header class="flex flex-wrap items-center gap-2.5">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                            @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4.5 w-4.5'])
                        </span>
                        <h2 class="min-w-0 text-[15px] font-bold leading-snug text-ink">Sesi Berikutnya</h2>
                    </header>

                    <article class="mt-4 rounded-xl border border-line bg-cream px-4 py-3.5">
                        <p class="text-[12.5px] font-bold text-bronze">
                            {{ $sesiBerikutnya->tanggal->locale('id')->translatedFormat('l, d/m/Y') }}
                        </p>
                        <p class="mt-1.5 text-[13.5px] font-bold leading-snug text-ink">
                            {{ $sesiBerikutnya->subject?->name ?? 'Mata pelajaran' }}
                        </p>
                        <p class="mt-1 text-[11.5px] leading-snug text-muted">
                            {{ substr($sesiBerikutnya->start_time, 0, 5) }} - {{ substr($sesiBerikutnya->end_time, 0, 5) }}
                            · {{ $sesiBerikutnya->classGroup?->code ?? '-' }}
                            · {{ $sesiBerikutnya->room?->name ?? 'Tanpa ruang' }}
                        </p>
                        <p class="mt-1 text-[11.5px] leading-snug text-bark-400">
                            Tentor: {{ $sesiBerikutnya->staff?->full_name_with_title ?? '-' }}
                        </p>
                        <a href="{{ route('admin.absensiJadwal', ['tanggal' => $sesiBerikutnya->tanggal->toDateString()]) }}"
                           class="mt-3 inline-flex items-center gap-1.5 text-[12px] font-bold text-bronze underline decoration-bronze/40 underline-offset-2 transition hover:text-gold">
                            Buka tanggal tersebut
                        </a>
                    </article>
                </section>
            @endif

            <section id="verifikasi-izin" class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-center gap-2.5">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'mail', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <h2 class="min-w-0 text-[15px] font-bold leading-snug text-ink">Verifikasi Izin &amp; Sakit</h2>
                    <span class="ml-auto shrink-0 rounded-md bg-gold px-2 py-1 text-[10.5px] font-bold leading-none text-espresso">
                        {{ $pengajuan->count() }} Menunggu
                    </span>
                </header>

                <div class="mt-4 space-y-3">
                    @foreach ($pengajuan as $ajuan)
                        @php
                            $kunci = $kunciIzin($ajuan->type);
                            $izin = $izin_pil[$kunci];
                            $enrollment = $ajuan->student?->enrollments->firstWhere('status', 'aktif');
                            $wali = $ajuan->student?->guardians->first();
                        @endphp
                        <article class="rounded-xl border border-line bg-cream px-4 py-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <p class="min-w-0 text-[13.5px] font-bold leading-snug text-ink">{{ $ajuan->student?->full_name ?? 'Siswa' }}</p>
                                <span class="shrink-0 rounded-md px-2 py-1 text-[10.5px] font-bold leading-none {{ $izin['kelas'] }}">{{ $izin['label'] }}</span>
                            </div>
                            <p class="mt-1.5 text-[11.5px] leading-snug text-muted">
                                {{ $enrollment?->classGroup?->code ?? 'Tanpa kelas' }}
                                <span class="text-bark-300">•</span>
                                Wali: {{ $wali?->name ?? '-' }}
                            </p>
                            @if ($ajuan->reason)
                                <p class="mt-1.5 text-[11.5px] leading-snug text-bark-400">{{ $ajuan->reason }}</p>
                            @endif
                            <div class="mt-3 flex justify-end gap-2">
                                <form method="POST" action="{{ route('admin.izin.update', $ajuan) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="ditolak">
                                    <button type="submit"
                                            class="rounded-lg border border-line bg-white px-3.5 py-2 text-[12px] font-semibold text-espresso transition hover:bg-sand">
                                        Tolak
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.izin.update', $ajuan) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="disetujui">
                                    <button type="submit"
                                            class="rounded-lg bg-espresso px-3.5 py-2 text-[12px] font-semibold text-white transition hover:bg-bark-800">
                                        Setujui
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div @class([
                    'mt-4 rounded-xl border border-dashed border-line bg-cream px-4 py-7 text-center',
                    'hidden' => $pengajuan->isNotEmpty(),
                ])>
                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'mx-auto h-6 w-6 text-bronze'])
                    <p class="mt-2.5 text-[13px] font-bold text-ink">Semua pengajuan sudah diputuskan</p>
                    <p class="mt-1.5 text-[12px] leading-relaxed text-muted">Tidak ada izin atau sakit yang menunggu verifikasi.</p>
                </div>
            </section>

            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-center gap-2.5">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'building', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <h2 class="min-w-0 text-[15px] font-bold leading-snug text-ink">Ketersediaan Ruangan</h2>
                    <span class="ml-auto shrink-0 rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold leading-none text-bronze">
                        {{ $ruangan->count() }} Ruang Aktif
                    </span>
                </header>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    @forelse ($ruangan as $ruang)
                        <div class="rounded-xl bg-cream px-3.5 py-3">
                            <p class="text-[13px] font-bold leading-snug text-ink">{{ $ruang['nama'] }}</p>
                            <p class="mt-1 text-[11.5px] font-bold leading-snug {{ $ruang['aktif'] ? 'text-bronze' : 'text-muted' }}">{{ $ruang['status'] }}</p>
                        </div>
                    @empty
                        <p class="col-span-2 rounded-xl border border-dashed border-line bg-cream px-4 py-6 text-center text-[12.5px] text-muted">
                            Belum ada data ruang.
                        </p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

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
            document.querySelector('[data-kosong-tanpa-sesi]')?.classList.toggle('hidden', baris.length !== 0);
            document.querySelector('[data-kosong-filter]')?.classList.toggle('hidden', baris.length === 0);
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
    })();
</script>
@endsection
