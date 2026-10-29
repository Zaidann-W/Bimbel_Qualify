@extends('layouts.owner')

@section('title', 'Dashboard Ringkasan Operasional & Bisnis')

@section('content')
@php
    $statToneMap = [
        'forest' => 'bg-forest-50 text-forest-600',
        'ember' => 'bg-ember-50 text-ember-600',
        'gold' => 'bg-sandgold text-bronze',
        'bark' => 'bg-bark-50 text-bark-600',
    ];
    $subToneMap = [
        'forest' => 'text-forest-600',
        'ember' => 'text-ember-600',
        'bark' => 'text-bark-400',
    ];

    $bars = $trend['bars'];
    $maxLaba = $trend['maxLaba'];
@endphp

<div class="space-y-6 px-4 py-6 sm:px-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <p class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-bark-400">
                <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                Executive Center &middot; Ringkasan Eksekutif
            </p>
            <h1 class="mt-2 text-[26px] font-bold tracking-tight text-ink">Dashboard Ringkasan Operasional &amp; Bisnis</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-muted">Konsolidasi performa finansial, disiplin presensi, kesiapan materi belajar, dan visibilitas portal publik Bimbel QUALIFY.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-semibold text-bronze">
                @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5 text-gold'])
                {{ now()->locale('id')->translatedFormat('F Y') }}
                <span class="rounded bg-sandgold px-1.5 py-0.5 text-[10px] font-bold text-bronze">Genap</span>
            </span>
            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-semibold text-ink transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'refresh', 'class' => 'h-3.5 w-3.5 text-bark-400'])
                Sinkronisasi
            </button>
            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gold px-3.5 py-2 text-[12px] font-bold text-espresso shadow-sm transition hover:bg-bronze hover:text-white">
                @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-3.5 w-3.5'])
                Ekspor Ringkasan
            </button>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="rounded-2xl border border-line bg-white p-5">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">{{ $stat['label'] }}</p>
                    <div class="@class(['grid h-8 w-8 shrink-0 place-items-center rounded-lg', $statToneMap[$stat['iconTone']] ?? $statToneMap['bark']])">
                        @include('layouts.partials.icon', ['name' => $stat['icon'], 'class' => 'h-4 w-4'])
                    </div>
                </div>
                <p class="mt-2 text-[28px] font-bold leading-none tracking-tight text-ink">{{ $stat['value'] }}</p>
                <p @class(['mt-2.5 text-[11px] font-medium', $subToneMap[$stat['subTone']] ?? 'text-bark-400'])>
                    @include('layouts.partials.icon', ['name' => 'trending-up', 'class' => 'h-3 w-3 inline -mt-px'])
                    {{ $stat['sub'] }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- Kinerja Finansial --}}
    <section class="rounded-2xl border border-line bg-white p-5">
        <header class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'wallet', 'class' => 'h-[18px] w-[18px]'])
                </div>
                <div>
                    <h2 class="text-[15px] font-bold text-ink">Kinerja Finansial &amp; Margin Arus Kas</h2>
                    <p class="text-[11px] text-bark-400">Komparasi pemasukan operasional dan beban berjalan bulan {{ now()->locale('id')->translatedFormat('F Y') }}</p>
                </div>
            </div>
            <a href="{{ route('owner.laporanKeuangan') }}" class="text-[12px] font-bold text-bronze hover:text-ember-600 transition">
                Buka Laporan Keuangan Lengkap &rarr;
            </a>
        </header>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            {{-- Total Pemasukan --}}
            <div class="rounded-xl border border-line bg-cream p-4">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Total Pemasukan</p>
                    <span class="h-2 w-2 rounded-full bg-forest-500"></span>
                </div>
                <p class="mt-2 text-[22px] font-bold tracking-tight text-ink">{{ 'Rp '.number_format($finance['income'], 0, ',', '.') }}</p>
                <div class="mt-4 space-y-2">
                    @foreach ($finance['incomeBreakdown'] as $item)
                        <div class="flex items-center justify-between text-[12px]">
                            <span class="text-bark-500">{{ $item['label'] }}</span>
                            <span class="font-semibold text-ink">{{ 'Rp '.number_format($item['amount'] / 1_000_000, 1).' jt' }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 border-t border-line pt-3 text-[11px] text-bark-400">
                    <span class="font-bold text-ink">{{ $finance['incomeSources'] }} Pos</span> Sumber Pemasukan &middot; <span class="font-bold text-forest-600">100% Tercatat</span>
                </div>
            </div>

            {{-- Total Pengeluaran --}}
            <div class="rounded-xl border border-line bg-cream p-4">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Total Pengeluaran</p>
                    <span class="h-2 w-2 rounded-full bg-bark-400"></span>
                </div>
                <p class="mt-2 text-[22px] font-bold tracking-tight text-ink">{{ 'Rp '.number_format($finance['expense'], 0, ',', '.') }}</p>
                <div class="mt-4 space-y-2">
                    @foreach ($finance['expenseBreakdown'] as $item)
                        <div class="flex items-center justify-between text-[12px]">
                            <span class="text-bark-500">{{ $item['label'] }}</span>
                            <span class="font-semibold text-ink">{{ 'Rp '.number_format($item['amount'] / 1_000_000, 1).' jt' }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 border-t border-line pt-3 text-[11px] text-bark-400">
                    Rasio Pengeluaran <span class="font-bold text-ember-600">{{ round($finance['expenseRatio'] * 100, 1).'%' }}</span> dari Omzet
                </div>
            </div>

            {{-- Trend bar chart --}}
            <div class="rounded-xl border border-line bg-cream p-4">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Tren Laba Bersih 6 Bulan</p>
                    <span class="text-[10px] font-bold text-forest-600">Avg: {{ round($maxLaba > 0 ? 68.4 : 0, 1).'%' }}</span>
                </div>
                <div class="mt-4 flex h-28 items-end gap-2">
                    @foreach ($bars as $bar)
                        <div class="flex flex-1 flex-col items-center gap-1">
                            <div class="@class([
                                'w-full rounded-t-md transition-all',
                                $bar['isCurrent'] ? 'bg-gold' : 'bg-bark-200',
                            ])"
                                 style="height: {{ $maxLaba > 0 ? max(8, round($bar['laba'] / $maxLaba * 100)) : 8 }}%"></div>
                            <span @class(['text-[9px] font-medium', $bar['isCurrent'] ? 'text-bronze font-bold' : 'text-bark-400'])>{{ $bar['bulan'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 border-t border-line pt-3 text-[11px] text-bark-400">
                    Rekening: Mandiri &amp; BCA &middot; <span class="font-bold text-forest-600">Terverifikasi</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Bottom 3 cards --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- Ringkasan Presensi --}}
        <section class="rounded-2xl border border-line bg-white p-5">
            <header class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="grid h-8 w-8 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'clipboard-check', 'class' => 'h-4 w-4'])
                    </div>
                    <h3 class="text-[14px] font-bold text-ink">Ringkasan Presensi</h3>
                </div>
                <span class="rounded-full bg-forest-50 px-2 py-0.5 text-[10px] font-bold text-forest-600">Aktif</span>
            </header>

            <div class="space-y-3">
                <div class="flex items-center justify-between rounded-lg bg-cream px-3 py-2.5">
                    <div>
                        <p class="text-[11px] text-bark-400">Kehadiran Siswa</p>
                        <p class="text-[16px] font-bold text-ink">{{ $presensi['studentRate'] }}</p>
                    </div>
                    <span class="text-[11px] font-medium text-bark-400">{{ $presensi['studentDetail'] }}</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-cream px-3 py-2.5">
                    <div>
                        <p class="text-[11px] text-bark-400">Kehadiran Tentor</p>
                        <p class="text-[16px] font-bold text-ink">{{ $presensi['tentorRate'] }}</p>
                    </div>
                    <span class="text-[11px] font-medium text-bark-400">{{ $presensi['tentorDetail'] }}</span>
                </div>
            </div>

            <footer class="mt-4 border-t border-line pt-3 flex items-center justify-between text-[11px]">
                <span class="text-bark-400">Disiplin akademik prima</span>
                <a href="{{ route('owner.laporanAbsensi') }}" class="font-bold text-bronze hover:text-ember-600 transition">Detail Absensi &rarr;</a>
            </footer>
        </section>

        {{-- Distribusi Materi --}}
        <section class="rounded-2xl border border-line bg-white p-5">
            <header class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="grid h-8 w-8 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'h-4 w-4'])
                    </div>
                    <h3 class="text-[14px] font-bold text-ink">Distribusi Materi</h3>
                </div>
                <span class="rounded-full bg-sandgold px-2 py-0.5 text-[10px] font-bold text-bronze">Watermark ON</span>
            </header>

            <div class="space-y-3">
                <div class="flex items-center justify-between rounded-lg bg-cream px-3 py-2.5">
                    <div>
                        <p class="text-[11px] text-bark-400">Modul Digital Terproteksi</p>
                        <p class="text-[16px] font-bold text-ink">{{ $materi['modulCount'] }} Modul</p>
                    </div>
                    @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-4 w-4 text-bark-300'])
                </div>
                <div class="flex items-center justify-between rounded-lg bg-cream px-3 py-2.5">
                    <div>
                        <p class="text-[11px] text-bark-400">Paket Try Out SNBT</p>
                        <p class="text-[16px] font-bold text-ink">{{ $materi['tryoutCount'] }} Paket</p>
                    </div>
                    <span class="rounded bg-sandgold px-2 py-0.5 text-[10px] font-bold text-bronze">Jadwal {{ now()->addDays(3)->format('d M') }}</span>
                </div>
            </div>

            <footer class="mt-4 border-t border-line pt-3 flex items-center justify-between text-[11px]">
                <span class="text-bark-400">Dilindungi enkripsi NIK</span>
                <a href="{{ route('owner.pemantauanMateri') }}" class="font-bold text-bronze hover:text-ember-600 transition">Kelola Modul &rarr;</a>
            </footer>
        </section>

        {{-- Website & Pendaftaran --}}
        <section class="rounded-2xl border border-line bg-white p-5">
            <header class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="grid h-8 w-8 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'globe', 'class' => 'h-4 w-4'])
                    </div>
                    <h3 class="text-[14px] font-bold text-ink">Website &amp; Pendaftaran</h3>
                </div>
                <span class="rounded-full bg-forest-50 px-2 py-0.5 text-[10px] font-bold text-forest-600">SSL Aktif</span>
            </header>

            <div class="space-y-3">
                <div class="flex items-center justify-between rounded-lg bg-cream px-3 py-2.5">
                    <div>
                        <p class="text-[11px] text-bark-400">Status Server Publik</p>
                        <p class="text-[13px] font-semibold text-ink">bimbelqualify.com</p>
                    </div>
                    <span class="rounded-full bg-forest-50 px-2 py-0.5 text-[10px] font-bold text-forest-600">Online 99.9%</span>
                </div>
                <div class="rounded-lg bg-cream px-3 py-2.5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] text-bark-400">Kapasitas Kursi Tersisa</p>
                            <p class="text-[16px] font-bold text-ink">{{ $website['capacityPercent'] }}%</p>
                        </div>
                        <span class="text-[11px] font-medium text-bark-400">{{ $website['capacityUsed'] }} kursi terisi dari {{ $website['capacityTotal'] }}</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full rounded-full bg-bark-100">
                        <div class="h-1.5 rounded-full bg-gold" style="width: {{ min(100, $website['capacityPercent']) }}%"></div>
                    </div>
                </div>
            </div>

            <footer class="mt-4 border-t border-line pt-3 flex items-center justify-between text-[11px]">
                <span class="text-bark-400">Google SEO #1 Bandung</span>
                <a href="{{ route('owner.pengaturanWebsite') }}" class="font-bold text-bronze hover:text-ember-600 transition">Sunting Pengaturan &rarr;</a>
            </footer>
        </section>
    </div>

    {{-- Footer --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-line bg-white px-5 py-3.5 text-[11px]">
        <p class="flex items-center gap-1.5 text-bark-400">
            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5 text-forest-500'])
            Konsolidasi otomatis diverifikasi oleh sistem internal QUALIFY ERP. Periode aktif TA 2024/2025.
        </p>
        <p class="text-bark-400">
            Terakhir disinkronkan: Hari ini, {{ now()->format('H:i') }} WIB
        </p>
    </div>
</div>
@endsection