@extends('layouts.owner')

@section('title', 'Dashboard')

{{-- Seluruh angka di halaman ini masih mock UI sesuai rancangan docs/dashboard-owner.png;
     sambungkan ke modul keuangan, presensi, materi, dan CMS begitu tersedia --}}
@section('content')
@php
    $kpis = [
        [
            'label' => 'Laba Bersih Bulan Ini',
            'nilai' => 'Rp 184.5M',
            'ikon' => 'file-text',
            'aksen' => 'border-t-gold',
            'penanda' => 'trending-up',
            'catatan' => 'Margin Sehat 68.8%',
        ],
        [
            'label' => 'Tingkat Kehadiran Siswa',
            'nilai' => '96.4%',
            'ikon' => 'clipboard-check',
            'aksen' => 'border-t-gold',
            'penanda' => 'dot',
            'catatan' => '1.339 dari 1.392 sesi terjadwal',
        ],
        [
            'label' => 'Modul Belajar & Soal',
            'nilai' => '78 Modul',
            'ikon' => 'book-open',
            'aksen' => 'border-t-espresso',
            'penanda' => 'check-circle',
            'catatan' => '100% Siap Didistribusikan',
        ],
        [
            'label' => 'Pendaftar Gelombang II',
            'nilai' => '42 Siswa',
            'ikon' => 'user-plus',
            'aksen' => 'border-t-gold',
            'penanda' => 'trending-up',
            'catatan' => '+18.5% peningkatan MoM',
        ],
    ];

    $penerimaan = [
        ['nama' => 'SPP Reguler & Intensif', 'nilai' => 'Rp 218.4 Jt'],
        ['nama' => 'Registrasi Siswa Baru', 'nilai' => 'Rp 31.6 Jt'],
        ['nama' => 'Modul & Try Out', 'nilai' => 'Rp 18.0 Jt'],
    ];

    $pengeluaran = [
        ['nama' => 'Honorarium 24 Tentor', 'nilai' => 'Rp 49.2 Jt'],
        ['nama' => 'Operasional Gedung & WiFi', 'nilai' => 'Rp 18.3 Jt'],
        ['nama' => 'Percetakan & Fasilitas', 'nilai' => 'Rp 16.0 Jt'],
    ];

    $trenLaba = [
        ['bulan' => 'Des', 'tinggi' => 62, 'kelas' => 'bg-bark-100', 'teks' => 'text-bark-300'],
        ['bulan' => 'Jan', 'tinggi' => 86, 'kelas' => 'bg-bark-100', 'teks' => 'text-bark-300'],
        ['bulan' => 'Feb', 'tinggi' => 68, 'kelas' => 'bg-bark-100', 'teks' => 'text-bark-300'],
        ['bulan' => 'Mar', 'tinggi' => 64, 'kelas' => 'bg-bark-100', 'teks' => 'text-bark-300'],
        ['bulan' => 'Apr', 'tinggi' => 80, 'kelas' => 'bg-palegold', 'teks' => 'text-bronze'],
        ['bulan' => 'Mei', 'tinggi' => 96, 'kelas' => 'bg-gold', 'teks' => 'text-ink'],
    ];

    $ringkasan = [
        [
            'ikon' => 'clipboard-check',
            'judul' => 'Ringkasan Presensi',
            'lencana' => 'Aktif',
            'baris' => [
                ['label' => 'Kehadiran Siswa', 'nilai' => '96.4%', 'tag' => '1.339 Sesi'],
                ['label' => 'Kehadiran Tentor', 'nilai' => '99.1%', 'tag' => '24 Tentor'],
            ],
            'catatan' => 'Disiplin akademik prima',
            'aksi' => 'Detail Absensi',
            'href' => '#',
            'warna_baris' => 'bg-cream',
        ],
        [
            'ikon' => 'book-open',
            'judul' => 'Distribusi Materi',
            'lencana' => 'Watermark ON',
            'baris' => [
                ['label' => 'Modul Digital Terproteksi', 'nilai' => '78 Modul', 'ikon_kanan' => 'lock'],
                ['label' => 'Paket Try Out SNBT', 'nilai' => '28 Paket', 'tag' => 'Jadwal 28 Mei'],
            ],
            'catatan' => 'Dilindungi enkripsi NIK',
            'aksi' => 'Kelola Modul',
            'href' => '#',
            'warna_baris' => 'bg-cream',
        ],
        [
            'ikon' => 'monitor',
            'judul' => 'Website & Pendaftaran',
            'lencana' => 'SSL Aktif',
            'baris' => [
                ['label' => 'Status Server Publik', 'sub' => 'bimbelqualify.com', 'nilai_kanan' => 'Online 99.9%'],
                ['label' => 'Kapasitas Kursi Terisi', 'sub' => 'Sisa 18 kursi reguler', 'nilai_kanan' => '88.5%', 'angka_besar' => true],
            ],
            'catatan' => 'Google SEO #1 Bandung',
            'aksi' => 'Sunting Profil',
            'href' => '#',
            'warna_baris' => 'bg-sandgold',
        ],
    ];
@endphp

{{-- Kepala halaman --}}
<section class="rounded-b-2xl bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-xl">
            <p class="flex items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.14em] text-ink">
                <span class="h-2 w-2 shrink-0 rounded-full bg-gold"></span>
                Executive Center <span class="text-bark-300">•</span> Ringkasan Eksekutif
            </p>
            <h1 class="mt-2.5 text-[30px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[34px]">
                Dashboard Ringkasan Operasional &amp; Bisnis
            </h1>
            <p class="mt-3 max-w-lg text-[13.5px] leading-relaxed text-muted">
                Konsolidasi performa finansial, disiplin presensi, kesiapan materi belajar, dan visibilitas portal publik Bimbel QUALIFY.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 lg:shrink-0">
            <div class="inline-flex items-center gap-2 rounded-lg border border-line bg-white px-3 py-2.5">
                @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4 text-bronze'])
                <span class="text-[13px] font-semibold text-ink">Mei 2025</span>
                <span class="rounded-md bg-sandgold px-1.5 py-0.5 text-[10.5px] font-bold text-bronze">Genap</span>
            </div>

            <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-line bg-white px-3 py-2.5 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'refresh', 'class' => 'h-4 w-4 text-bronze'])
                Sinkronisasi
            </button>

            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-gold px-3.5 py-2.5 text-[13px] font-bold text-espresso transition hover:bg-gold/80">
                @include('layouts.partials.icon', ['name' => 'printer', 'class' => 'h-4 w-4'])
                Ekspor Ringkasan
            </button>
        </div>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Empat angka pokok periode berjalan --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kpis as $kpi)
            <article @class([
                'rounded-xl border border-line border-t-[3px] bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]',
                $kpi['aksen'],
            ])>
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">{{ $kpi['label'] }}</p>
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => $kpi['ikon'], 'class' => 'h-4 w-4'])
                    </span>
                </div>
                <p class="mt-3.5 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $kpi['nilai'] }}</p>
                <p class="mt-3.5 flex items-center gap-1.5 text-[11px] font-medium leading-snug text-bark-700">
                    @if ($kpi['penanda'] === 'dot')
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                    @else
                        @include('layouts.partials.icon', ['name' => $kpi['penanda'], 'class' => 'h-3.5 w-3.5 text-gold'])
                    @endif
                    {{ $kpi['catatan'] }}
                </p>
            </article>
        @endforeach
    </div>

    {{-- Neraca keuangan bulan berjalan --}}
    <section class="rounded-2xl border border-line bg-white p-5 shadow-[0_1px_3px_rgba(20,20,20,0.05)] sm:p-6">
        <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3">
            <div class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'wallet', 'class' => 'h-5 w-5'])
                </span>
                <div>
                    <h2 class="text-[17px] font-bold leading-snug text-ink">Kinerja Finansial &amp; Margin Arus Kas</h2>
                    <p class="mt-0.5 text-[12.5px] leading-relaxed text-muted">Komparisi pemasukan operasional dan beban berjalan bulan Mei 2025</p>
                </div>
            </div>
            <a href="{{ route('owner.laporanKeuangan') }}"
               class="inline-flex shrink-0 items-center gap-1 text-[12.5px] font-semibold text-bronze transition hover:text-espresso focus-visible:underline">
                Buka Laporan Finansial Lengkap <span aria-hidden="true">→</span>
            </a>
        </header>

        <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-3">

            {{-- Pemasukan --}}
            <div class="flex flex-col rounded-xl border border-line bg-cream p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Total Penerimaan</p>
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-gold"></span>
                </div>
                <p class="mt-2.5 text-[26px] font-extrabold leading-none tracking-tight tabular-nums text-ink">Rp 268.000.000</p>

                <dl class="mt-4 space-y-2.5">
                    @foreach ($penerimaan as $pos)
                        <div class="flex items-baseline justify-between gap-3 text-[12.5px]">
                            <dt class="min-w-0 truncate text-muted">{{ $pos['nama'] }}</dt>
                            <dd class="shrink-0 font-bold tabular-nums text-ink">{{ $pos['nilai'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                <footer class="mt-auto flex items-center justify-between gap-3 border-t border-line pt-3.5">
                    <p class="text-[11.5px] font-bold text-bronze">3 Pos Sumber Pemasukan</p>
                    <p class="text-[12.5px] font-bold text-ink">100% Tercatat</p>
                </footer>
            </div>

            {{-- Pengeluaran --}}
            <div class="flex flex-col rounded-xl border border-line bg-cream p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Total Pengeluaran</p>
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-espresso"></span>
                </div>
                <p class="mt-2.5 text-[26px] font-extrabold leading-none tracking-tight tabular-nums text-ink">Rp 83.500.000</p>

                <dl class="mt-4 space-y-2.5">
                    @foreach ($pengeluaran as $pos)
                        <div class="flex items-baseline justify-between gap-3 text-[12.5px]">
                            <dt class="min-w-0 truncate text-muted">{{ $pos['nama'] }}</dt>
                            <dd class="shrink-0 font-bold tabular-nums text-ink">{{ $pos['nilai'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                <footer class="mt-auto flex items-center justify-between gap-3 border-t border-line pt-3.5">
                    <p class="text-[11.5px] text-muted">Rasio Pengeluaran</p>
                    <p class="text-[12.5px] font-bold text-ink">31.2% dari Omzet</p>
                </footer>
            </div>

            {{-- Tren laba bersih --}}
            <div class="flex flex-col rounded-xl border border-line bg-cream p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Tren Laba Bersih 6 Bulan</p>
                    <span class="rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold tabular-nums text-bronze">Avg: 66.4%</span>
                </div>

                <div class="mt-4 flex h-[104px] items-end gap-2" role="img"
                     aria-label="Grafik batang tren laba bersih Desember 2024 sampai Mei 2025, rata-rata 66.4 persen">
                    @foreach ($trenLaba as $batang)
                        <div class="flex h-full flex-1 flex-col justify-end">
                            <div class="w-full rounded-t-md {{ $batang['kelas'] }}" style="height: {{ $batang['tinggi'] }}%"></div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-2 flex gap-2" aria-hidden="true">
                    @foreach ($trenLaba as $batang)
                        <span class="flex-1 text-center text-[10px] font-bold {{ $batang['teks'] }}">{{ $batang['bulan'] }}</span>
                    @endforeach
                </div>

                <footer class="mt-auto flex items-center justify-between gap-3 border-t border-line pt-3.5">
                    <p class="text-[11.5px] text-muted">Rekening: Mandiri &amp; BCA</p>
                    <p class="text-[12.5px] font-bold text-bronze">Terverifikasi</p>
                </footer>
            </div>
        </div>
    </section>

    {{-- Tiga pilar operasional --}}
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($ringkasan as $bagian)
            <section class="flex flex-col rounded-2xl border border-line bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)] sm:p-5">
                <header class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-2.5">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                            @include('layouts.partials.icon', ['name' => $bagian['ikon'], 'class' => 'h-4 w-4'])
                        </span>
                        <h2 class="text-[14.5px] font-bold leading-snug text-ink">{{ $bagian['judul'] }}</h2>
                    </div>
                    <span class="shrink-0 rounded-md bg-sandgold px-2 py-1 text-[10px] font-bold text-bronze">{{ $bagian['lencana'] }}</span>
                </header>

                <div class="mt-4 space-y-3">
                    @foreach ($bagian['baris'] as $baris)
                        <div class="flex items-center justify-between gap-3 rounded-xl px-3.5 py-3 {{ $bagian['warna_baris'] }}">
                            <div class="min-w-0">
                                <p class="truncate text-[11.5px] font-medium text-muted">{{ $baris['label'] }}</p>
                                @if (! empty($baris['sub']))
                                    <p class="truncate text-[11.5px] text-muted">{{ $baris['sub'] }}</p>
                                @endif
                                @if (! empty($baris['nilai']))
                                    <p class="mt-0.5 text-[20px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $baris['nilai'] }}</p>
                                @endif
                            </div>

                            @if (! empty($baris['tag']))
                                <span class="shrink-0 rounded-md bg-white px-2 py-1 text-[10.5px] font-bold text-bronze">{{ $baris['tag'] }}</span>
                            @elseif (! empty($baris['nilai_kanan']) && ! empty($baris['angka_besar']))
                                <span class="shrink-0 text-[24px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $baris['nilai_kanan'] }}</span>
                            @elseif (! empty($baris['nilai_kanan']))
                                <span class="shrink-0 rounded-md bg-white px-2 py-1 text-[11px] font-bold text-ember-600">{{ $baris['nilai_kanan'] }}</span>
                            @elseif (! empty($baris['ikon_kanan']))
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md text-bronze">
                                    @include('layouts.partials.icon', ['name' => $baris['ikon_kanan'], 'class' => 'h-4 w-4'])
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <footer class="mt-auto flex items-center justify-between gap-3 border-t border-line pt-3.5">
                    <p class="text-[11px] leading-snug text-muted">{{ $bagian['catatan'] }}</p>
                    <a href="{{ $bagian['href'] }}"
                       class="shrink-0 text-[11.5px] font-semibold text-bronze transition hover:text-espresso focus-visible:underline">{{ $bagian['aksi'] }} <span aria-hidden="true">→</span></a>
                </footer>
            </section>
        @endforeach
    </div>

    {{-- Catatan audit --}}
    <section class="flex flex-col gap-2 rounded-2xl border border-line bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-muted">
            @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'mt-[1px] h-4 w-4 shrink-0 text-gold'])
            Konsolidasi otomatis diverifikasi oleh sistem internal QUALIFY ERP. Periode aktif TA 2024/2025.
        </p>
        <p class="shrink-0 text-[11px] text-muted sm:text-right">Terakhir disinkronkan: <span class="font-bold text-ink">Hari ini, 08:15 WIB</span></p>
    </section>
</div>
@endsection
