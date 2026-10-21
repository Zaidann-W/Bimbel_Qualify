@extends('layouts.manager')

@section('title', 'Dashboard Manajer')

{{-- Seluruh angka di halaman ini masih mock UI sesuai rancangan docs/dashboard-manager.png;
     sambungkan ke modul keuangan, presensi, materi, dan CMS begitu tersedia --}}
@section('content')
@php
    $kpis = [
        [
            'label' => 'Siswa Aktif',
            'ikon' => 'users',
            'nilai' => '348',
            'penanda' => 'trending-up',
            'catatan' => '+12% MoM',
            'panel' => 'baris',
            'baris' => [
                ['label' => '18 Rombel Aktif', 'teks' => 'bold'],
                ['label' => '96.4% Rasio Kehadiran', 'teks' => 'normal'],
            ],
        ],
        [
            'label' => 'Rombel Berjalan',
            'ikon' => 'building',
            'nilai' => '18',
            'satuan' => 'Kelas',
            'penanda' => 'dot',
            'catatan' => 'Kapasitas 91% terisi',
            'panel' => 'kolom',
            'kolom' => [
                ['jenjang' => 'SD', 'jumlah' => '4'],
                ['jenjang' => 'SMP', 'jumlah' => '6'],
                ['jenjang' => 'SMA', 'jumlah' => '8', 'aksen' => true],
            ],
        ],
        [
            'label' => 'Tentor Bertugas',
            'ikon' => 'user-check',
            'nilai' => '24',
            'satuan' => 'Aktif',
            'penanda' => 'check-circle',
            'catatan' => '100% Plotting Terisi',
            'panel' => 'pasangan',
            'pasangan' => ['label' => 'Sesi Kosong:', 'nilai' => '0 Sesi'],
        ],
        [
            'label' => 'Saldo Bersih',
            'ikon' => 'wallet',
            'nilai' => 'Rp 84.65M',
            'penanda' => 'dot',
            'catatan' => 'Per Tahun 2025',
            'panel' => 'uang',
            'uang' => [
                ['label' => 'Masuk:', 'nilai' => 'Rp 142.85M'],
                ['label' => 'Keluar:', 'nilai' => 'Rp 58.20M'],
            ],
        ],
        [
            'label' => 'Materi dan Modul',
            'ikon' => 'book-open',
            'nilai' => '86',
            'satuan' => '/ 92',
            'penanda' => 'dot',
            'catatan' => '93.5% Siap Ajar',
            'panel' => 'uang',
            'uang' => [
                ['label' => 'Draft', 'nilai' => '6 Modul', 'aksen' => true],
                ['label' => 'Review:', 'nilai' => '3 Modul'],
            ],
        ],
        [
            'label' => 'Siswa Baru',
            'ikon' => 'user-plus',
            'nilai' => '42',
            'satuan' => 'Pendaftar',
            'penanda' => 'check-circle',
            'catatan' => '36 Terverifikasi',
            'panel' => 'pasangan',
            'pasangan' => ['label' => 'Verifikasi Berkas:', 'nilai' => '6 Siswa', 'aksen' => true],
        ],
    ];

    $log = [
        [
            'ikon' => 'wallet',
            'judul' => 'Verifikasi SPP Rombel 12 IPA oleh Kasir',
            'waktu' => '10 Mar 2025, 14:20',
            'teks' => 'Siswa: Amanda Putri (Kelas 12 IPA-1)',
            'jumlah' => 'Rp 2.450.000',
            'tag' => ['Validasi Kasir #02'],
        ],
        [
            'ikon' => 'book-open',
            'judul' => 'Modul Fisika Kuantum Sesi 12 diunggah Tentor',
            'waktu' => '10 Mar 2025, 13:10',
            'teks' => 'Oleh: Kak Hendra Wijaya, M.Pd.',
            'tag' => ['Tingkat SMA Super Intensif', 'PDF • 4.2 MB'],
        ],
        [
            'ikon' => 'clipboard-check',
            'judul' => 'Rekap Presensi Sesi Pagi Cabang Utama Selesai',
            'waktu' => '10 Mar 2025, 12:00',
            'teks' => '94 siswa hadir • 3 izin • 1 sakit',
            'tag' => ['Presensi 95.9%'],
        ],
        [
            'ikon' => 'user-plus',
            'judul' => 'Pendaftaran Baru Siswa SMA disetujui Admin',
            'waktu' => '10 Mar 2025, 10:45',
            'teks' => 'Calon Siswa: Dimas Aditya Ramadhan',
            'tag' => ['Paket UTBK TPS + TKA Saintek', 'Rombel SMA-3'],
        ],
    ];

    $fasilitas = [
        ['nama' => 'Laboratorium CBT / Tryout', 'nilai' => '40 Unit Komputer (Siap)'],
        ['nama' => 'Perpustakaan &amp; Modul Fisik', 'nilai' => 'Stok Cukup (140 Eks)', 'aksen' => true],
        ['nama' => 'Koneksi Internet Dedicated', 'nilai' => '200 Mbps (Stabil)', 'aksen' => true],
    ];
@endphp

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Banner mode read-only --}}
    <section class="flex flex-wrap items-start gap-x-4 gap-y-3 rounded-xl border border-line bg-white px-4 py-3.5 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-espresso text-gold">
            @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-4.5 w-4.5'])
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-[13px] font-bold leading-snug text-ink">Mode Tinjauan Eksekutif (Strict Read-Only)</p>
            <p class="mt-0.5 max-w-2xl text-[11.5px] leading-relaxed text-muted">
                Data disinkronkan secara real-time dari modul Super Admin, Keuangan, dan Tentor. Tindakan manipulasi data transaksi atau absensi dikunci.
            </p>
        </div>
        <p class="flex shrink-0 items-center gap-1.5 text-[11px] font-semibold text-muted sm:pl-4">
            <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
            Sinkronisasi Otomatis: 10 Mar 2025 • 14:48 WIB
        </p>
    </section>

    {{-- Hero sambutan manajerial --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-gradient-to-bl from-sandgold/80 via-cream to-white px-5 py-6 shadow-[0_1px_3px_rgba(20,20,20,0.05)] sm:px-7">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-xl">
                <p class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                    <span class="rounded-md bg-gold px-2 py-1 text-[9.5px] font-extrabold uppercase leading-none tracking-[0.12em] text-espresso">Overview Manajerial</span>
                    <span class="text-[12px] font-semibold leading-tight text-ink">Cabang Utama<br>Surabaya Pusat</span>
                </p>
                <h1 class="mt-4 text-[27px] font-extrabold leading-[1.15] tracking-tight text-ink sm:text-[31px]">
                    Selamat Datang,<br>
                    <span class="underline decoration-gold decoration-[3px] underline-offset-4">Ir. Rahmat Hidayat, M.M.</span>
                </h1>
                <p class="mt-3.5 max-w-md text-[13px] leading-relaxed text-muted">
                    Ikhtisar Eksekutif Kinerja &amp; Operasional Bimbel Qualify Cabang Utama. Seluruh metrik mencerminkan komparasi kurikulum, efisiensi rasio tentor-siswa, dan stabilitas kas bulanan.
                </p>

                <nav class="mt-4 flex flex-col gap-2 text-[12.5px] font-semibold" aria-label="Jalur cepat manajer">
                    <a href="#" class="w-fit text-bronze transition hover:text-espresso focus-visible:underline">Keuangan Bulanan <span aria-hidden="true">→</span></a>
                    <a href="#" class="w-fit text-bronze transition hover:text-espresso focus-visible:underline">Pemantauan Materi <span aria-hidden="true">→</span></a>
                    <a href="#" class="w-fit text-bronze transition hover:text-espresso focus-visible:underline">Ringkasan Operasional <span aria-hidden="true">→</span></a>
                </nav>
            </div>

            <div class="flex shrink-0 flex-col gap-3 lg:w-64 lg:items-end">
                <div class="w-full">
                    <label for="periode-laporan" class="block text-[11px] font-semibold text-muted">Periode Laporan</label>
                    <select id="periode-laporan" name="periode"
                            class="mt-1.5 w-full rounded-lg border border-line bg-white px-3 py-2.5 text-[12.5px] font-semibold text-ink transition hover:bg-sand focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                        <option>Bulan Ini: Maret 2025</option>
                        <option>Februari 2025</option>
                        <option>Januari 2025</option>
                        <option>Tahun Berjalan 2025</option>
                    </select>
                </div>

                <button type="button"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gold px-4 py-3 text-[13px] font-bold text-espresso transition hover:bg-gold/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-bronze focus-visible:ring-offset-2">
                    @include('layouts.partials.icon', ['name' => 'file-text', 'class' => 'h-4.5 w-4.5'])
                    Unduh Executive Summary (PDF)
                </button>
            </div>
        </div>
    </section>

    {{-- Enam kartu metrik pokok --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($kpis as $kpi)
            <article class="flex flex-col rounded-xl border border-line bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">{{ $kpi['label'] }}</p>
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => $kpi['ikon'], 'class' => 'h-4 w-4'])
                    </span>
                </div>

                <p class="mt-3.5 flex items-baseline gap-1.5">
                    <span class="text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $kpi['nilai'] }}</span>
                    @if (! empty($kpi['satuan']))
                        <span class="text-[14px] font-bold text-bark-500">{{ $kpi['satuan'] }}</span>
                    @endif
                </p>

                <p class="mt-2.5 flex items-center gap-1.5 text-[11px] font-medium leading-snug text-bark-700">
                    @if ($kpi['penanda'] === 'dot')
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                    @else
                        @include('layouts.partials.icon', ['name' => $kpi['penanda'], 'class' => 'h-3.5 w-3.5 text-gold'])
                    @endif
                    {{ $kpi['catatan'] }}
                </p>

                @if ($kpi['panel'] === 'baris')
                    <div class="mt-auto space-y-1 rounded-lg bg-cream px-3 py-2.5 pt-3">
                        @foreach ($kpi['baris'] as $baris)
                            <p @class([
                                'text-[11.5px] leading-snug',
                                'font-bold text-ink' => ($baris['teks'] ?? '') === 'bold',
                                'text-muted' => ($baris['teks'] ?? '') !== 'bold',
                            ])>{{ $baris['label'] }}</p>
                        @endforeach
                    </div>
                @elseif ($kpi['panel'] === 'kolom')
                    <div class="mt-auto grid grid-cols-3 gap-2 rounded-lg bg-cream px-3 py-2.5 pt-3 text-center">
                        @foreach ($kpi['kolom'] as $kolom)
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wide text-muted">{{ $kolom['jenjang'] }}</p>
                                <p @class([
                                    'mt-0.5 text-[15px] font-extrabold tabular-nums',
                                    'text-bronze' => ! empty($kolom['aksen']),
                                    'text-ink' => empty($kolom['aksen']),
                                ])>{{ $kolom['jumlah'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @elseif ($kpi['panel'] === 'pasangan')
                    <div class="mt-auto flex items-center justify-between gap-3 rounded-lg bg-cream px-3 py-2.5 pt-3 text-[11.5px]">
                        <span class="text-muted">{{ $kpi['pasangan']['label'] }}</span>
                        <span @class([
                            'font-bold tabular-nums',
                            'text-bronze' => ! empty($kpi['pasangan']['aksen']),
                            'text-ink' => empty($kpi['pasangan']['aksen']),
                        ])>{{ $kpi['pasangan']['nilai'] }}</span>
                    </div>
                @else
                    <dl class="mt-auto space-y-1.5 rounded-lg bg-cream px-3 py-2.5 pt-3">
                        @foreach ($kpi['uang'] as $pos)
                            <div class="flex items-baseline justify-between gap-3 text-[11.5px]">
                                <dt class="text-muted">{{ $pos['label'] }}</dt>
                                <dd @class([
                                    'font-bold tabular-nums',
                                    'text-bronze' => ! empty($pos['aksen']),
                                    'text-ink' => empty($pos['aksen']),
                                ])>{{ $pos['nilai'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </article>
        @endforeach
    </div>

    {{-- Log aktivitas & kesiapan fasilitas --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- Log aktivitas --}}
        <section class="rounded-2xl border border-line bg-white p-5 shadow-[0_1px_3px_rgba(20,20,20,0.05)] lg:col-span-2">
            <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-5 w-5'])
                    </span>
                    <div>
                        <h2 class="text-[17px] font-bold leading-snug text-ink">Log Aktivitas Operasional Terkini</h2>
                        <p class="mt-0.5 max-w-lg text-[12px] leading-relaxed text-muted">
                            Pemantauan real-time transaksi, unggahan materi, dan presensi cabang (Mode Audit).
                        </p>
                    </div>
                </div>
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-sandgold px-2.5 py-1.5 text-[10.5px] font-bold text-bronze">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                    Live Feed
                </span>
            </header>

            <ol class="mt-5 space-y-3">
                @foreach ($log as $entri)
                    <li class="flex items-start gap-3 rounded-xl border border-line bg-cream px-3.5 py-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                            @include('layouts.partials.icon', ['name' => $entri['ikon'], 'class' => 'h-4.5 w-4.5'])
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-0.5">
                                <p class="text-[12.5px] font-bold leading-snug text-ink">{{ $entri['judul'] }}</p>
                                <p class="shrink-0 text-[10.5px] tabular-nums text-muted">{{ $entri['waktu'] }}</p>
                            </div>

                            <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1.5">
                                @if (! empty($entri['jumlah']))
                                    <span class="text-[12.5px] font-extrabold tabular-nums text-ink">{{ $entri['jumlah'] }}</span>
                                    <span class="text-[11.5px] text-muted">{{ $entri['teks'] }}</span>
                                @else
                                    <span class="text-[11.5px] text-muted">{{ $entri['teks'] }}</span>
                                @endif
                                @foreach ($entri['tag'] as $tag)
                                    <span class="rounded-md bg-sandgold px-1.5 py-0.5 text-[10px] font-bold text-bronze">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>

                        <span class="mt-0.5 shrink-0 text-gold" aria-label="Status tervalidasi">
                            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4.5 w-4.5'])
                        </span>
                    </li>
                @endforeach
            </ol>

            <footer class="mt-5 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-t border-line pt-4">
                <p class="max-w-md text-[11.5px] leading-relaxed text-muted">
                    Menampilkan 4 entri audit terbaru hari ini. Seluruh log tersimpan pada replikasi server backup.
                </p>
                <a href="#" class="shrink-0 text-[12.5px] font-semibold text-bronze transition hover:text-espresso focus-visible:underline">
                    Buka Semua Log Operasional <span aria-hidden="true">→</span>
                </a>
            </footer>
        </section>

        {{-- Kesiapan fasilitas --}}
        <section class="flex flex-col rounded-2xl border border-line bg-white p-5 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <header class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'presentation', 'class' => 'h-5 w-5'])
                    </span>
                    <h2 class="text-[17px] font-bold leading-snug text-ink">Kesiapan Fasilitas Cabang</h2>
                </div>
                <span class="shrink-0 rounded-md bg-sandgold px-2 py-1 text-center text-[10px] font-bold leading-tight text-bronze">
                    100%<br>Siap
                </span>
            </header>

            {{-- Ilustrasi ruang belajar (SVG inline, tanpa aset eksternal) --}}
            <figure class="mt-4 overflow-hidden rounded-xl">
                <svg viewBox="0 0 320 136" role="img" aria-label="Ilustrasi Ruang Belajar Digital S-01 dengan meja siswa, layar digital, dan pencahayaan hangat" class="block h-auto w-full">
                    <defs>
                        <linearGradient id="ruang-dinding" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0" stop-color="#3f2c18"/>
                            <stop offset="1" stop-color="#2a1a0e"/>
                        </linearGradient>
                        <linearGradient id="ruang-lantai" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0" stop-color="#2e2010"/>
                            <stop offset="1" stop-color="#1f1509"/>
                        </linearGradient>
                        <radialGradient id="ruang-cahaya" cx="0.5" cy="0" r="1">
                            <stop offset="0" stop-color="#f5b72e" stop-opacity="0.35"/>
                            <stop offset="1" stop-color="#f5b72e" stop-opacity="0"/>
                        </radialGradient>
                    </defs>
                    <rect width="320" height="92" fill="url(#ruang-dinding)"/>
                    <rect y="92" width="320" height="44" fill="url(#ruang-lantai)"/>
                    <rect width="320" height="60" fill="url(#ruang-cahaya)"/>
                    {{-- Layar digital --}}
                    <rect x="112" y="18" width="96" height="46" rx="3" fill="#141414"/>
                    <rect x="116" y="22" width="88" height="38" rx="2" fill="#2e2010"/>
                    <path d="M126 49l14-15 11 11 9-8 18 12" fill="none" stroke="#f5b72e" stroke-opacity="0.75" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="152" y="64" width="16" height="4" rx="1" fill="#543920"/>
                    {{-- Lampu plafon --}}
                    <rect x="40" y="8" width="52" height="4" rx="2" fill="#f5b72e" opacity="0.85"/>
                    <rect x="228" y="8" width="52" height="4" rx="2" fill="#f5b72e" opacity="0.85"/>
                    {{-- Bangku & meja siswa --}}
                    <g fill="#543920">
                        <rect x="34" y="76" width="52" height="5" rx="1.5"/>
                        <rect x="134" y="76" width="52" height="5" rx="1.5"/>
                        <rect x="234" y="76" width="52" height="5" rx="1.5"/>
                    </g>
                    <g fill="#8a6440">
                        <rect x="30" y="98" width="60" height="6" rx="1.5"/>
                        <rect x="130" y="98" width="60" height="6" rx="1.5"/>
                        <rect x="230" y="98" width="60" height="6" rx="1.5"/>
                    </g>
                    <g fill="#3f2c18">
                        <rect x="36" y="104" width="4" height="10" rx="1"/>
                        <rect x="80" y="104" width="4" height="10" rx="1"/>
                        <rect x="136" y="104" width="4" height="10" rx="1"/>
                        <rect x="180" y="104" width="4" height="10" rx="1"/>
                        <rect x="236" y="104" width="4" height="10" rx="1"/>
                        <rect x="280" y="104" width="4" height="10" rx="1"/>
                    </g>
                </svg>
                <figcaption class="mt-2.5">
                    <p class="text-[13px] font-bold leading-snug text-ink">Ruang Belajar Digital S-01</p>
                    <p class="mt-0.5 text-[11px] text-muted">Kapasitas 20 Siswa • 4K Smart TV</p>
                    <p class="mt-1.5 flex items-center gap-1.5 text-[11px] font-semibold text-bronze">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                        Siap Pakai
                    </p>
                </figcaption>
            </figure>

            <dl class="mt-4 divide-y divide-line border-t border-line">
                @foreach ($fasilitas as $fasilitas)
                    <div class="flex items-baseline justify-between gap-3 py-2.5">
                        <dt class="min-w-0 text-[11.5px] leading-snug text-muted">{!! $fasilitas['nama'] !!}</dt>
                        <dd @class([
                            'shrink-0 text-right text-[11.5px] font-bold leading-snug',
                            'text-bronze' => ! empty($fasilitas['aksen']),
                            'text-ink' => empty($fasilitas['aksen']),
                        ])>{{ $fasilitas['nilai'] }}</dd>
                    </div>
                @endforeach
            </dl>

            <button type="button"
                    class="mt-4 w-full rounded-lg border border-line bg-white px-3 py-2.5 text-[12px] font-bold text-espresso transition hover:bg-sand focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold/40">
                Tinjau Matriks Pemakaian Ruangan
            </button>
        </section>
    </div>
</div>
@endsection
