@extends('layouts.manager')

@section('title', 'Keuangan Bulanan')

{{-- Seluruh angka di halaman ini masih mock UI sesuai rancangan docs/keuangan-manager.png dan
     docs/keuangan1-manager.png; sambungkan ke modul keuangan (buku kas, SPP, anggaran) begitu tersedia --}}
@section('content')
@php
    $rupiah = fn (int $nilai): string => 'Rp ' . number_format($nilai, 0, ',', '.');

    $kartuMetrik = [
        [
            'label' => 'Total Pemasukan',
            'ikon' => 'wallet',
            'nilai' => '142.850.000',
            'penanda' => ['teks' => '+14.2%', 'ikon' => 'trending-up', 'nada' => 'gold'],
            'catatan' => 'Feb 2025: Rp 125.100.000',
            'bar' => ['persen' => 72, 'nada' => 'gold'],
            'blob' => true,
        ],
        [
            'label' => 'Total Pengeluaran',
            'ikon' => 'briefcase',
            'nilai' => '58.200.000',
            'penanda' => ['teks' => '+4.1%', 'ikon' => 'trending-up', 'nada' => 'netral'],
            'catatan' => 'Feb 2025: Rp 55.900.000',
            'bar' => ['persen' => 40, 'nada' => 'espresso'],
        ],
        [
            'label' => 'Net Profit Bersih',
            'ikon' => 'landmark',
            'nilai' => '84.650.000',
            'penanda' => ['teks' => '+22.3%', 'ikon' => 'trending-up', 'nada' => 'gold'],
            'catatan' => 'Surplus peningkatan efisiensi operasional',
            'gelap' => true,
        ],
        [
            'label' => 'Piutang Belum Lunas',
            'ikon' => 'credit-card',
            'nilai' => '14.500.000',
            'penanda' => ['teks' => '12 Siswa', 'nada' => 'ember'],
            'catatan' => 'Tenggat pembayaran termin: 15 Maret 2025',
            'piutang' => true,
        ],
    ];

    // Skala batang: Rp 52,0 juta (Minggu II) = 100% tinggi plot
    $arusKas = [
        ['minggu' => 'Minggu I (1-7 Mar)', 'masuk' => 'Rp 48.0M / Rp 18.0M', 'tinggi_masuk' => 92.3, 'tinggi_keluar' => 34.6],
        ['minggu' => 'Minggu II (8-14 Mar)', 'masuk' => 'Rp 52.0M / Rp 14.0M', 'tinggi_masuk' => 100, 'tinggi_keluar' => 26.9],
        ['minggu' => 'Minggu III (15-21 Mar)', 'masuk' => 'Rp 26.0M / Rp 16.0M', 'tinggi_masuk' => 50, 'tinggi_keluar' => 30.8],
        ['minggu' => 'Minggu IV (Proyeksi)', 'masuk' => 'Rp 16.8M / Rp 10.2M', 'tinggi_masuk' => 32.3, 'tinggi_keluar' => 19.6, 'proyeksi' => true],
    ];

    $sumberPemasukan = [
        ['nama' => 'SPP Bulanan Siswa', 'persen' => 72, 'nilai' => 'Rp 102.8M'],
        ['nama' => 'Registrasi Siswa Baru', 'persen' => 18, 'nilai' => 'Rp 25.7M'],
        ['nama' => 'Tryout & Buku Modul', 'persen' => 10, 'nilai' => 'Rp 14.3M'],
    ];

    $alokasiBeban = [
        ['nama' => 'Honor Mengajar Tentor', 'persen' => 54, 'nilai' => 'Rp 31.4M'],
        ['nama' => 'Sewa & Utilitas Gedung', 'persen' => 22, 'nilai' => 'Rp 12.8M'],
        ['nama' => 'Percetakan & Software Ops', 'persen' => 24, 'nilai' => 'Rp 13.9M'],
    ];

    $rekapKategori = [
        [
            'nama' => 'SPP &amp; Biaya Les Siswa', 'rincian' => 'Paket Reguler SD, SMP, SMA',
            'ikon' => 'graduation-cap', 'jenis' => 'Pemasukan',
            'realisasi' => 102850000, 'target' => 100000000, 'selisih' => '+2.85%',
            'status' => ['teks' => 'Surplus Sehat', 'ikon' => 'check-circle', 'nada' => 'gold'],
        ],
        [
            'nama' => 'Biaya Pendaftaran Baru', 'rincian' => 'Gelombang SNBT &amp; Intensif',
            'ikon' => 'user-plus', 'jenis' => 'Pemasukan',
            'realisasi' => 25700000, 'target' => 20000000, 'selisih' => '+28.5%',
            'status' => ['teks' => 'Melampaui Target', 'ikon' => 'trending-up', 'nada' => 'gold'],
        ],
        [
            'nama' => 'Penjualan Modul &amp; Tryout', 'rincian' => 'Platform CBT &amp; Cetak',
            'ikon' => 'book-open', 'jenis' => 'Pemasukan',
            'realisasi' => 14300000, 'target' => 12000000, 'selisih' => '+19.1%',
            'status' => ['teks' => 'Surplus', 'ikon' => 'check-circle', 'nada' => 'gold'],
        ],
        [
            'nama' => 'Honorarium Tentor', 'rincian' => '28 Guru Tetap &amp; Lepas',
            'ikon' => 'briefcase', 'jenis' => 'Pengeluaran',
            'realisasi' => 31430000, 'target' => 33000000, 'selisih' => '-4.75%',
            'status' => ['teks' => 'Sesuai Budget', 'ikon' => 'check-circle', 'nada' => 'netral'],
        ],
        [
            'nama' => 'Listrik, Internet &amp; Fasilitas', 'rincian' => 'Gedung Pusat &amp; AC Ruangan',
            'ikon' => 'building', 'jenis' => 'Pengeluaran',
            'realisasi' => 12800000, 'target' => 13000000, 'selisih' => '-1.5%',
            'status' => ['teks' => 'Sesuai Budget', 'ikon' => 'check-circle', 'nada' => 'netral'],
        ],
        [
            'nama' => 'Percetakan Modul &amp; Bank Soal', 'rincian' => 'Pengadaan 350 Eksemplar',
            'ikon' => 'printer', 'jenis' => 'Pengeluaran',
            'realisasi' => 8150000, 'target' => 7500000, 'selisih' => '+8.6%',
            'status' => ['teks' => 'Dalam Batas Wajar', 'ikon' => 'info', 'nada' => 'palegold'],
        ],
        [
            'nama' => 'Biaya Pemeliharaan &amp; Software', 'rincian' => 'Server CBT &amp; Lisensi Database',
            'ikon' => 'sliders', 'jenis' => 'Pengeluaran',
            'realisasi' => 5820000, 'target' => 6000000, 'selisih' => '-3.0%',
            'status' => ['teks' => 'Efisien', 'ikon' => 'slash-circle', 'nada' => 'netral'],
        ],
    ];
@endphp

{{-- Kepala halaman: judul + aksi ekspor --}}
<section class="rounded-b-2xl bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-xl">
            <h1 class="text-[26px] font-bold leading-[1.2] tracking-tight text-ink sm:text-[30px]">
                Laporan dan Pemantauan<br>Keuangan Bulanan
            </h1>
            <p class="mt-2.5 text-[13px] leading-relaxed text-muted">
                Analisis Arus Kas Masuk, Beban Operasional, dan Status Pelunasan SPP Siswa terpusat tanpa otorisasi modifikasi pembukuan.
            </p>
        </div>

        <div class="flex shrink-0 flex-col gap-2.5 lg:items-end">
            <button type="button"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-line bg-white px-4 py-2.5 text-[12.5px] font-bold text-espresso transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40 lg:w-auto">
                @include('layouts.partials.icon', ['name' => 'table', 'class' => 'h-4 w-4'])
                Unduh Laporan (.XLSX)
            </button>
            <button type="button"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[12.5px] font-bold text-espresso transition hover:bg-gold/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-bronze focus-visible:ring-offset-2 lg:w-auto">
                @include('layouts.partials.icon', ['name' => 'file-text', 'class' => 'h-4 w-4'])
                Ekspor Laporan Resmi (.PDF)
            </button>
        </div>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Empat kartu metrik pokok --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kartuMetrik as $metrik)
            @if (! empty($metrik['gelap']))
                <article class="relative overflow-hidden rounded-xl bg-espresso p-4">
                    <div class="flex items-start justify-between gap-3">
                        <p class="flex items-center gap-2 text-[12.5px] font-bold leading-snug text-white">
                            @include('layouts.partials.icon', ['name' => $metrik['ikon'], 'class' => 'text-gold'])
                            {{ $metrik['label'] }}
                        </p>
                        <span class="inline-flex shrink-0 items-center gap-1 rounded-md bg-gold px-1.5 py-0.5 text-[10px] font-bold text-espresso">
                            @include('layouts.partials.icon', ['name' => $metrik['penanda']['ikon'], 'class' => 'h-3 w-3'])
                            {{ $metrik['penanda']['teks'] }}
                        </span>
                    </div>

                    <p class="mt-4 text-[20px] font-bold leading-none text-white/80">Rp</p>
                    <p class="mt-1 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-gold">{{ $metrik['nilai'] }}</p>

                    <p class="mt-3 text-[11.5px] leading-relaxed text-bark-200">{{ $metrik['catatan'] }}</p>

                    <p class="mt-4 flex items-center gap-2 rounded-lg bg-white/5 px-3 py-2.5 text-[11.5px] font-semibold text-white">
                        @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4 w-4 text-gold'])
                        Margin Operasional: <span class="font-bold text-gold">59.2%</span>
                    </p>
                </article>
            @elseif (! empty($metrik['piutang']))
                <article class="flex flex-col rounded-xl border border-line bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
                    <div class="flex items-start justify-between gap-3">
                        <p class="flex items-center gap-2 text-[12.5px] font-bold leading-snug text-ink">
                            @include('layouts.partials.icon', ['name' => $metrik['ikon'], 'class' => 'text-ember-600'])
                            {{ $metrik['label'] }}
                        </p>
                        <span class="inline-flex shrink-0 items-center rounded-md bg-ember-100 px-1.5 py-0.5 text-center text-[10px] font-bold leading-tight text-ember-600">
                            {{ $metrik['penanda']['teks'] }}
                        </span>
                    </div>

                    <p class="mt-4 text-[20px] font-bold leading-none text-ember-600/80">Rp</p>
                    <p class="mt-1 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ember-600">{{ $metrik['nilai'] }}</p>

                    <p class="mt-3 text-[11.5px] leading-relaxed text-muted">{{ $metrik['catatan'] }}</p>

                    <dl class="mt-auto flex items-baseline justify-between gap-3 pt-4 text-[11.5px]">
                        <dt class="text-muted">Tingkat Penagihan:</dt>
                        <dd class="font-bold tabular-nums text-bronze">87.6% <span class="text-ink">Sukses</span></dd>
                    </dl>
                </article>
            @else
                <article class="relative flex flex-col overflow-hidden rounded-xl border border-line bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
                    <div class="flex items-start justify-between gap-3">
                        <p class="flex items-center gap-2 text-[12.5px] font-bold leading-snug text-ink">
                            @include('layouts.partials.icon', ['name' => $metrik['ikon'], 'class' => 'text-bronze'])
                            {{ $metrik['label'] }}
                        </p>
                        <span @class([
                            'inline-flex shrink-0 items-center gap-1 rounded-md px-1.5 py-0.5 text-[10px] font-bold',
                            'bg-gold text-espresso' => $metrik['penanda']['nada'] === 'gold',
                            'bg-sand text-muted' => $metrik['penanda']['nada'] === 'netral',
                        ])>
                            @include('layouts.partials.icon', ['name' => $metrik['penanda']['ikon'], 'class' => 'h-3 w-3'])
                            {{ $metrik['penanda']['teks'] }}
                        </span>
                    </div>

                    <p class="mt-4 text-[20px] font-bold leading-none text-ink/70">Rp</p>
                    <p class="mt-1 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $metrik['nilai'] }}</p>

                    <p class="mt-3 text-[11.5px] leading-relaxed text-muted">{{ $metrik['catatan'] }}</p>

                    <div class="relative mt-auto pt-4">
                        @if (! empty($metrik['blob']))
                            <span class="pointer-events-none absolute -bottom-2 right-5 h-12 w-12 rounded-full bg-palegold/60" aria-hidden="true"></span>
                        @endif
                        <div class="relative h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                            <div @class([
                                'h-full rounded-full',
                                'bg-gold' => $metrik['bar']['nada'] === 'gold',
                                'bg-bark-700' => $metrik['bar']['nada'] === 'espresso',
                            ]) style="width: {{ $metrik['bar']['persen'] }}%"></div>
                        </div>
                    </div>
                </article>
            @endif
        @endforeach
    </div>

    {{-- Arus kas mingguan + distribusi anggaran --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- Grafik batang arus kas --}}
        <section class="rounded-2xl border border-line bg-white p-5 shadow-[0_1px_3px_rgba(20,20,20,0.05)] lg:col-span-2">
            <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">
                <div>
                    <h2 class="text-[17px] font-bold leading-snug text-ink">Arus Kas Mingguan (Cashflow)</h2>
                    <p class="mt-0.5 max-w-md text-[12px] leading-relaxed text-muted">
                        Perbandingan ritme kas masuk dan pengeluaran bulan Maret 2025
                    </p>
                </div>
                <ul class="flex shrink-0 items-center gap-4 text-[11.5px] font-semibold text-ink">
                    <li class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-gold"></span>Pemasukan</li>
                    <li class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-espresso"></span>Pengeluaran</li>
                </ul>
            </header>

            <figure class="mt-5">
                <div role="img"
                     aria-label="Diagram batang arus kas mingguan Maret 2025. Minggu I pemasukan Rp 48 juta, pengeluaran Rp 18 juta. Minggu II pemasukan Rp 52 juta, pengeluaran Rp 14 juta. Minggu III pemasukan Rp 26 juta, pengeluaran Rp 16 juta. Minggu IV proyeksi pemasukan Rp 16,8 juta, pengeluaran Rp 10,2 juta."
                     class="relative h-56">
                    {{-- Garis bantu --}}
                    <span class="absolute inset-x-0 top-0 border-t border-dashed border-line" aria-hidden="true"></span>
                    <span class="absolute inset-x-0 top-1/4 border-t border-dashed border-line" aria-hidden="true"></span>
                    <span class="absolute inset-x-0 top-2/4 border-t border-dashed border-line" aria-hidden="true"></span>
                    <span class="absolute inset-x-0 top-3/4 border-t border-dashed border-line" aria-hidden="true"></span>
                    <span class="absolute inset-x-0 bottom-0 border-t border-line" aria-hidden="true"></span>

                    <div class="relative flex h-full items-end justify-around gap-6 px-2 sm:gap-10">
                        @foreach ($arusKas as $grup)
                            <div class="flex h-full flex-1 items-end justify-center gap-1.5 sm:gap-2.5">
                                <div @class([
                                    'w-full max-w-[38px] rounded-t-[4px]',
                                    'bg-gold' => empty($grup['proyeksi']),
                                    'bg-palegold' => ! empty($grup['proyeksi']),
                                ]) style="height: {{ $grup['tinggi_masuk'] }}%"></div>
                                <div @class([
                                    'w-full max-w-[38px] rounded-t-[4px]',
                                    'bg-espresso' => empty($grup['proyeksi']),
                                    'bg-bark-300' => ! empty($grup['proyeksi']),
                                ]) style="height: {{ $grup['tinggi_keluar'] }}%"></div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <figcaption class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 border-t border-line pt-4 sm:grid-cols-4">
                    @foreach ($arusKas as $grup)
                        <div>
                            <p class="text-[12px] font-bold leading-snug text-ink">{{ $grup['minggu'] }}</p>
                            <p class="mt-0.5 text-[11px] tabular-nums leading-snug text-muted">{{ $grup['masuk'] }}</p>
                        </div>
                    @endforeach
                </figcaption>
            </figure>
        </section>

        {{-- Distribusi anggaran --}}
        <section class="rounded-2xl border border-line bg-white p-5 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <header class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-[17px] font-bold leading-snug text-ink">Distribusi Anggaran</h2>
                    <p class="mt-0.5 text-[12px] leading-relaxed text-muted">
                        Struktur rasio pendapatan dan beban operasional bulan ini
                    </p>
                </div>
                @include('layouts.partials.icon', ['name' => 'pie-chart', 'class' => 'mt-0.5 h-5 w-5 shrink-0 text-bark-300'])
            </header>

            <div class="mt-4 space-y-4">
                <div class="rounded-xl bg-shell p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Sumber Pemasukan</p>
                    <ul class="mt-3 space-y-3.5">
                        @foreach ($sumberPemasukan as $pos)
                            <li>
                                <div class="flex items-baseline justify-between gap-3 text-[12px]">
                                    <span class="font-semibold leading-snug text-ink">{{ $pos['nama'] }}</span>
                                    <span class="shrink-0 whitespace-nowrap tabular-nums">
                                        <span class="font-bold text-ink">{{ $pos['persen'] }}%</span>
                                        <span class="text-muted">({{ $pos['nilai'] }})</span>
                                    </span>
                                </div>
                                <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                                    <div class="h-full rounded-full bg-gold" style="width: {{ $pos['persen'] }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-xl bg-shell p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Alokasi Beban Operasional</p>
                    <ul class="mt-3 space-y-3.5">
                        @foreach ($alokasiBeban as $pos)
                            <li>
                                <div class="flex items-baseline justify-between gap-3 text-[12px]">
                                    <span class="font-semibold leading-snug text-ink">{{ $pos['nama'] }}</span>
                                    <span class="shrink-0 whitespace-nowrap tabular-nums">
                                        <span class="font-bold text-ink">{{ $pos['persen'] }}%</span>
                                        <span class="text-muted">({{ $pos['nilai'] }})</span>
                                    </span>
                                </div>
                                <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                                    <div class="h-full rounded-full bg-bark-700" style="width: {{ $pos['persen'] }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    </div>

    {{-- Rekapitulasi kategori keuangan --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
        <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3 px-5 py-4">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                <h2 class="text-[17px] font-bold leading-snug text-ink">Rekapitulasi Kategori Keuangan</h2>
                <span class="rounded-md bg-sand px-2 py-1 text-[10.5px] font-bold text-muted">7 Pos Anggaran</span>
                <p class="w-full text-[12px] leading-relaxed text-muted">
                    Evaluasi target vs realisasi arus kas divisi cabang aktif
                </p>
            </div>
            <button type="button"
                    class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12px] font-bold text-espresso transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40">
                @include('layouts.partials.icon', ['name' => 'file-text', 'class' => 'h-4 w-4'])
                Buka Audit Log Transaksi
            </button>
        </header>

        <div class="overflow-x-auto border-t border-line">
            <table class="w-full min-w-[920px] text-left">
                <thead>
                    <tr class="border-b border-line">
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Kategori Keuangan</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Jenis</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Realisasi<br>Bulan Ini</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Target /<br>Anggaran</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Selisih<br>(%)</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Status Audit</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Aksi<br>Analisis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($rekapKategori as $kategori)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <span @class([
                                        'grid h-9 w-9 shrink-0 place-items-center rounded-lg',
                                        'bg-gold text-espresso' => $kategori['jenis'] === 'Pemasukan',
                                        'bg-sand text-espresso' => $kategori['jenis'] === 'Pengeluaran',
                                    ])>
                                        @include('layouts.partials.icon', ['name' => $kategori['ikon'], 'class' => 'h-4.5 w-4.5'])
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-bold leading-snug text-ink">{!! $kategori['nama'] !!}</p>
                                        <p class="mt-0.5 text-[11px] leading-relaxed text-muted">{!! $kategori['rincian'] !!}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-block rounded-md px-2 py-1 text-[10.5px] font-bold',
                                    'bg-sandgold text-bronze' => $kategori['jenis'] === 'Pemasukan',
                                    'bg-sand text-muted' => $kategori['jenis'] === 'Pengeluaran',
                                ])>{{ $kategori['jenis'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="block text-[10px] text-bark-300">Rp</span>
                                <span class="text-[13px] font-bold tabular-nums text-ink">{{ number_format($kategori['realisasi'], 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="block text-[10px] text-bark-300">Rp</span>
                                <span class="text-[13px] font-medium tabular-nums text-bark-300">{{ number_format($kategori['target'], 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'text-[12.5px] font-bold tabular-nums',
                                    'text-bronze' => str_starts_with($kategori['selisih'], '+'),
                                    'text-bark-500' => ! str_starts_with($kategori['selisih'], '+'),
                                ])>{{ $kategori['selisih'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-[10.5px] font-bold leading-tight',
                                    'bg-gold text-espresso' => $kategori['status']['nada'] === 'gold',
                                    'bg-sandgold text-bronze' => $kategori['status']['nada'] === 'palegold',
                                    'bg-sand text-muted' => $kategori['status']['nada'] === 'netral',
                                ])>
                                    @include('layouts.partials.icon', ['name' => $kategori['status']['ikon'], 'class' => 'h-3.5 w-3.5'])
                                    {{ $kategori['status']['teks'] }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <button type="button"
                                        class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg border border-line bg-white px-2.5 py-1.5 text-[11px] font-bold text-espresso transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40">
                                    @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-3.5 w-3.5'])
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <footer class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-t border-line bg-shell px-5 py-3.5">
            <p class="flex items-center gap-2 text-[11.5px] leading-relaxed text-muted">
                @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'h-4 w-4 shrink-0 text-gold'])
                Data diverifikasi secara periodik oleh Bendahara Cabang &amp; Kantor Pusat
            </p>
            <p class="flex flex-wrap items-baseline gap-x-4 gap-y-1 text-[11.5px] text-muted">
                <span>Total Realisasi: <span class="font-bold tabular-nums text-ink">{{ $rupiah(201050000) }}</span></span>
                <span>Selisih Kumulatif: <span class="font-bold tabular-nums text-bronze">+18.4%</span></span>
            </p>
        </footer>
    </section>
</div>
@endsection
