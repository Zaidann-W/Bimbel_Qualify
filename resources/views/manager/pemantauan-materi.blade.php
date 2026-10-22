@extends('layouts.manager')

@section('title', 'Pemantauan Materi')

{{-- Seluruh angka di halaman ini masih mock UI sesuai rancangan docs/pemantauanmateri-manager.png;
     sambungkan ke modul materi (silabus, modul ajar, bank soal) begitu tersedia --}}
@section('content')
@php
    $kartuKesiapan = [
        [
            'label' => 'Total Materi Terdaftar',
            'ikon' => 'book-open',
            'nilai' => '92',
            'satuan' => '/ 100 Modul',
            'bar' => ['persen' => 82, 'teks' => 'Target Genap 2024/2025', 'angka' => '82%'],
        ],
        [
            'label' => 'Dipublikasikan (Siap Ajar)',
            'ikon' => 'check-circle',
            'nilai' => '86',
            'satuan' => 'Modul Siap',
            'penanda' => ['teks' => 'Siap Ajar', 'nada' => 'gold'],
            'catatan' => ['ikon' => 'check-circle', 'teks' => '93,5% kesiapan rombel aktif', 'nada' => 'gold'],
        ],
        [
            'label' => 'Dalam Review / Draft',
            'ikon' => 'pen-line',
            'nilai' => '6',
            'satuan' => 'Perlu Verifikasi',
            'nada' => 'ember',
            'catatan' => ['ikon' => 'clock', 'teks' => '2 Fisika UTBK SMA, 2 Kimia 11 SMA, 2 SMP', 'nada' => 'ember'],
        ],
        [
            'label' => 'Akses Siswa & Unduhan',
            'ikon' => 'download',
            'nilai' => '4.820',
            'satuan' => 'Kali',
            'penanda' => ['teks' => '+18,4%', 'ikon' => 'trending-up', 'nada' => 'gold'],
            'catatan' => ['teks' => 'dibanding Februari 2025'],
        ],
    ];

    $jenjangFilter = ['Semua Jenjang', 'SMA Kelas 10', 'SMA Kelas 11', 'SMA Kelas 12 / SNBT', 'SMP Terpadu', 'SD Prestasi'];
    $mapelFilter = ['Semua Mata Pelajaran', 'Matematika Saintek', 'Fisika Saintek', 'Kimia Saintek', 'Bahasa Indonesia', 'Biologi Saintek'];
    $statusFilter = ['Semua Status', 'Dipublikasikan', 'Dalam Review', 'Draf'];
    $tentorFilter = ['Semua Tentor Penyusun', 'Rahmat Hidayat', 'Dewi Anggraini', 'Bayu Prakoso', 'Sinta Maharani'];

    $pintasan = [
        ['Semua Modul', 92, true],
        ['Prioritas UTBK SNBT', 34, false],
        ['SMA Reguler', 28, false],
        ['SMP Terpadu', 18, false],
        ['SD Prestasi', 12, false],
    ];

    // [kode, judul, mapel, jenjang, tanggal, jam, status, statusNada, unduhan]
    $modul = [
        ['MOD-SMA-014', 'Kalkulus &amp; Aplikasi Integral Luas Volume', 'Matematika Saintek', '12 SMA',
         '8 Mar 2025', '14:20 WIB', 'Dipublikasikan', 'terbit', 348],
        ['MOD-UTBK-032', 'Penalaran Kuantitatif: Trik Eliminasi Cepat &amp; Logika Angka', 'Matematika Saintek', 'UTBK SNBT',
         '8 Mar 2025', '09:40 WIB', 'Dalam Review', 'review', 512],
        ['MOD-SMA-021', 'Fisika Modern, Radiasi Benda Hitam &amp; Relativitas Khusus', 'Fisika Saintek', '12 SMA',
         '7 Mar 2025', '16:05 WIB', 'Draf', 'draf', 96],
        ['MOD-SMP-009', 'Penalaran Verbal: Sinonim, Antonim &amp; Konteks Kalimat', 'Bahasa Indonesia', 'SMP Terpadu',
         '6 Mar 2025', '10:30 WIB', 'Dipublikasikan', 'terbit', 274],
        ['MOD-SD-004', 'Pecahan Sederhana &amp; Operasi Bilangan Campuran', 'Matematika SD', 'SD Prestasi',
         '5 Mar 2025', '13:15 WIB', 'Dalam Review', 'review', 58],
        ['MOD-SMA-027', 'Kimia Organik: Gugus Fungsi, Isomer &amp; Reaksi Penggantian', 'Kimia Saintek', '11 SMA',
         '4 Mar 2025', '11:50 WIB', 'Draf', 'draf', 41],
    ];
@endphp

{{-- Kepala halaman: status audit + judul + aksi ekspor --}}
<section class="relative overflow-hidden rounded-b-2xl bg-white px-4 pb-6 pt-5 sm:px-6">
    {{-- Kilau emas latar, meniru rancangan mockup --}}
    <span class="pointer-events-none absolute -right-12 -top-16 h-52 w-52 rounded-full bg-palegold/40 blur-2xl" aria-hidden="true"></span>
    <span class="pointer-events-none absolute right-24 top-4 h-24 w-24 rounded-full bg-sandgold/50 blur-xl" aria-hidden="true"></span>

    <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                <span class="inline-flex items-center gap-2 rounded-full border border-line bg-shell px-3 py-1.5 text-[11.5px] font-semibold text-muted">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                    Status Audit: Read-Only Supervisi
                </span>
                <a href="#"
                   class="inline-flex items-center gap-1 text-[11.5px] font-semibold text-bronze underline-offset-2 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40">
                    @include('layouts.partials.icon', ['name' => 'info', 'class' => 'h-3.5 w-3.5'])
                    Ketentuan Akses
                </a>
            </div>

            <p class="mt-2.5 flex items-center gap-1.5 text-[12px] text-muted">
                @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-4 w-4 text-bark-300'])
                Terakhir Diperbarui: Hari ini, 10 Maret 2025 · 15:00 WIB
            </p>

            <h1 class="mt-3 text-[26px] font-bold leading-[1.2] tracking-tight text-ink sm:text-[30px]">
                Pemantauan &amp; Kesiapan Materi<br>Pembelajaran
            </h1>
            <p class="mt-2.5 text-[13px] leading-relaxed text-muted">
                Audit ketersediaan silabus, modul ajar digital, bank soal HOTS, dan status publikasi materi per rombel sebelum sesi dimulai.
            </p>
        </div>

        <div class="flex shrink-0 flex-col gap-2.5 lg:items-end">
            <button type="button"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-line bg-white px-4 py-2.5 text-[12.5px] font-bold text-espresso transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40 lg:w-auto">
                @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4'])
                Unduh Rekap Silabus (.PDF)
            </button>
            <button type="button"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[12.5px] font-bold text-espresso transition hover:bg-gold/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-bronze focus-visible:ring-offset-2 lg:w-auto">
                @include('layouts.partials.icon', ['name' => 'table', 'class' => 'h-4 w-4'])
                Ekspor Matriks Kurikulum (.XLSX)
            </button>
        </div>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Empat kartu metrik kesiapan --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kartuKesiapan as $metrik)
            <article @class([
                'flex flex-col rounded-xl border border-line bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]',
                'ring-1 ring-ember-200' => ($metrik['nada'] ?? '') === 'ember',
            ])>
                <div class="flex items-start justify-between gap-3">
                    <p class="flex items-center gap-2 text-[12.5px] font-bold leading-snug text-ink">
                        @include('layouts.partials.icon', [
                            'name' => $metrik['ikon'],
                            'class' => ($metrik['nada'] ?? '') === 'ember' ? 'text-ember-600' : 'text-bronze',
                        ])
                        {{ $metrik['label'] }}
                    </p>
                    @if (! empty($metrik['penanda']))
                        <span @class([
                            'inline-flex shrink-0 items-center gap-1 rounded-md px-1.5 py-0.5 text-[10px] font-bold',
                            'bg-gold text-espresso' => $metrik['penanda']['nada'] === 'gold',
                        ])>
                            @include('layouts.partials.icon', ['name' => $metrik['penanda']['ikon'] ?? 'check-circle', 'class' => 'h-3 w-3'])
                            {{ $metrik['penanda']['teks'] }}
                        </span>
                    @endif
                </div>

                <p class="mt-4 flex items-baseline gap-1.5">
                    <span @class([
                        'text-[27px] font-extrabold leading-none tracking-tight tabular-nums',
                        'text-ember-600' => ($metrik['nada'] ?? '') === 'ember',
                        'text-ink' => ($metrik['nada'] ?? '') !== 'ember',
                    ])>{{ $metrik['nilai'] }}</span>
                    <span class="text-[12px] font-semibold text-muted">{{ $metrik['satuan'] }}</span>
                </p>

                @if (! empty($metrik['bar']))
                    <div class="mt-auto pt-4">
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                            <div class="h-full rounded-full bg-gold" style="width: {{ $metrik['bar']['persen'] }}%"></div>
                        </div>
                        <p class="mt-2 flex items-baseline justify-between gap-2 text-[11px] text-muted">
                            <span>{{ $metrik['bar']['teks'] }}</span>
                            <span class="font-bold tabular-nums text-ink">{{ $metrik['bar']['angka'] }}</span>
                        </p>
                    </div>
                @elseif (! empty($metrik['catatan']))
                    <p class="mt-auto flex items-start gap-1.5 pt-4 text-[11.5px] leading-relaxed">
                        @include('layouts.partials.icon', [
                            'name' => $metrik['catatan']['ikon'] ?? 'trending-up',
                            'class' => 'mt-0.5 h-3.5 w-3.5 shrink-0 ' . (($metrik['catatan']['nada'] ?? '') === 'gold' ? 'text-bronze' : (($metrik['catatan']['nada'] ?? '') === 'ember' ? 'text-ember-500' : 'text-bark-300')),
                        ])
                        <span @class([
                            'font-semibold',
                            'text-bronze' => ($metrik['catatan']['nada'] ?? '') === 'gold',
                            'text-ember-600' => ($metrik['catatan']['nada'] ?? '') === 'ember',
                            'text-muted' => empty($metrik['catatan']['nada']),
                        ])>{{ $metrik['catatan']['teks'] }}</span>
                    </p>
                @endif
            </article>
        @endforeach
    </div>

    {{-- Pencarian + filter --}}
    <section class="rounded-2xl border border-line bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-[220px] flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                    @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                </span>
                <input type="search" aria-label="Cari judul modul atau kode materi"
                       placeholder="Cari judul modul, kode materi, atau bab..."
                       class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition hover:bg-sand focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
            </div>

            @foreach ([$jenjangFilter, $mapelFilter, $statusFilter, $tentorFilter] as $filter)
                <select aria-label="{{ $filter[0] }}"
                        class="rounded-lg border border-line bg-white px-3 py-2.5 text-[12.5px] font-semibold text-muted transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                    @foreach ($filter as $opsi)
                        <option>{{ $opsi }}</option>
                    @endforeach
                </select>
            @endforeach

            <button type="button" aria-label="Muat ulang data materi"
                    class="grid h-[42px] w-[42px] shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-500 transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40">
                @include('layouts.partials.icon', ['name' => 'refresh', 'class' => 'h-4 w-4'])
            </button>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <span class="mr-1 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Pintasan Cepat:</span>
            @foreach ($pintasan as [$nama, $jumlah, $aktif])
                <button type="button"
                        @class([
                            'rounded-lg px-3 py-1.5 text-[12px] font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40',
                            'bg-espresso text-white' => $aktif,
                            'border border-line bg-white text-muted hover:bg-sand hover:text-ink' => ! $aktif,
                        ])>
                    {{ $nama }} ({{ $jumlah }})
                </button>
            @endforeach
        </div>
    </section>

    {{-- Matriks status modul ajar --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
        <header class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 px-5 py-4">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <h2 class="flex items-center gap-2 text-[17px] font-bold leading-snug text-ink">
                    @include('layouts.partials.icon', ['name' => 'layout-grid', 'class' => 'text-bronze'])
                    Matriks Status Modul Ajar
                </h2>
                <span class="text-[12px] leading-relaxed text-muted">Menampilkan 6 dari 92 Modul</span>
            </div>
            <p class="flex items-center gap-2 text-[11.5px] font-semibold text-muted">
                Status Akses:
                <span class="inline-flex items-center gap-1.5 rounded-md bg-sand px-2 py-1 text-[11px] font-bold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-3.5 w-3.5'])
                    Terkunci untuk Modifikasi
                </span>
            </p>
        </header>

        <div class="overflow-x-auto border-t border-line">
            <table class="w-full min-w-[920px] text-left">
                <thead>
                    <tr class="border-b border-line">
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Judul Modul</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Mata<br>Pelajaran &amp;<br>Jenjang</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Pembaruan</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Status Kesiapan</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Aktivitas Siswa</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Aksi Supervisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($modul as [$kode, $judul, $mapel, $jenjang, $tanggal, $jam, $status, $nada, $unduhan])
                        <tr class="transition hover:bg-shell/70">
                            <td class="px-5 py-4 align-top">
                                <span class="inline-block rounded-md bg-sand px-2 py-1 text-[10px] font-bold tracking-wide text-muted">{{ $kode }}</span>
                                <p class="mt-2 max-w-[200px] text-[13px] font-bold leading-snug text-ink">{!! $judul !!}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <p class="text-[12.5px] font-semibold leading-snug text-ink">{{ $mapel }}</p>
                                <span class="mt-1.5 inline-block rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold text-bronze">{{ $jenjang }}</span>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <p class="text-[12.5px] font-semibold text-ink">{{ $tanggal }}</p>
                                <p class="mt-0.5 text-[11px] text-muted">{{ $jam }}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <span @class([
                                    'inline-flex items-center gap-1.5 text-[12.5px] font-bold',
                                    'text-emerald-600' => $nada === 'terbit',
                                    'text-bronze' => $nada === 'review',
                                    'text-ember-600' => $nada === 'draf',
                                ])>
                                    <span @class([
                                        'h-1.5 w-1.5 rounded-full',
                                        'bg-emerald-500' => $nada === 'terbit',
                                        'bg-gold' => $nada === 'review',
                                        'bg-ember-500' => $nada === 'draf',
                                    ])></span>
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <p class="text-[13px] font-bold tabular-nums text-ink">{{ number_format($unduhan, 0, ',', '.') }}</p>
                                <p class="mt-0.5 text-[11px] text-muted">Unduhan Aktif</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <button type="button"
                                        class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg border border-line bg-white px-2.5 py-1.5 text-[11px] font-bold text-espresso transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40">
                                    @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-3.5 w-3.5'])
                                    Pratinjau Materi
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <footer class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3 border-t border-line bg-shell px-5 py-3.5">
            <p class="text-[11.5px] leading-relaxed text-muted">Menampilkan baris 1 sampai 6 dari total 92 materi modul cabang</p>
            <nav class="flex flex-wrap items-center gap-1.5" aria-label="Navigasi halaman matriks modul">
                <button type="button"
                        class="rounded-lg border border-line bg-white px-3 py-1.5 text-[11.5px] font-bold text-muted transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40">
                    &lt; Sebelumnya
                </button>
                @foreach ([1, 2, 3, '...'] as $halaman)
                    @if ($halaman === '...')
                        <span class="px-1.5 text-[11.5px] font-semibold text-bark-300">…</span>
                    @else
                        <button type="button"
                                @class([
                                    'grid h-8 w-8 place-items-center rounded-lg text-[11.5px] font-bold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40',
                                    'bg-espresso text-white' => $halaman === 1,
                                    'border border-line bg-white text-muted hover:bg-sand' => $halaman !== 1,
                                ])>
                            {{ $halaman }}
                        </button>
                    @endif
                @endforeach
                <button type="button" aria-label="Halaman 16"
                        class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-[11.5px] font-bold text-muted transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40">
                    16
                </button>
                <button type="button"
                        class="rounded-lg border border-line bg-white px-3 py-1.5 text-[11.5px] font-bold text-espresso transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40">
                    Selanjutnya &gt;
                </button>
            </nav>
        </footer>
    </section>
</div>
@endsection
