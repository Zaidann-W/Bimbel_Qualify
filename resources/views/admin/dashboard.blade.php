@extends('layouts.admin')

@section('title', 'Dashboard Overview')

{{-- Seluruh angka di halaman ini masih mock UI sesuai rancangan docs/dashboard-admin.png;
     sambungkan ke modul pendaftaran, kas, presensi, dan klinik PR begitu tersedia --}}
@section('content')
@php
    $kpis = [
        [
            'label' => 'Total Siswa Aktif',
            'nilai' => '342',
            'ikon' => 'users',
            'aksen' => 'border-t-gold',
            'tile' => 'bg-sand text-ink',
            'lencana' => '+12 Siswa Baru',
            'lencana_kelas' => 'bg-sandgold text-bronze',
            'catatan' => '18 Rombel Kelas Aktif',
        ],
        [
            'label' => 'Menunggu Verifikasi',
            'nilai' => '12 Calon',
            'ikon' => 'clipboard-form',
            'aksen' => 'border-t-gold',
            'tile' => 'bg-sand text-ink',
            'lencana' => 'Butuh Respon',
            'lencana_kelas' => 'bg-palegold text-espresso',
            'catatan' => 'Perlu ditinjau hari ini',
        ],
        [
            'label' => 'Kehadiran Hari Ini',
            'nilai' => '94.8%',
            'ikon' => 'presentation',
            'aksen' => 'border-t-espresso',
            'tile' => 'bg-espresso text-gold',
            'lencana' => 'Sesi Berjalan',
            'lencana_kelas' => 'bg-sandgold text-bronze',
            'catatan' => '14 Sesi (8 Sore, 6 Malam)',
        ],
        [
            'label' => 'Pemasukan Kas Hari Ini',
            'nilai' => 'Rp 4.850.000',
            'ikon' => 'wallet',
            'aksen' => 'border-t-espresso',
            'tile' => 'bg-sand text-ink',
            'lencana' => 'Kasir Buka',
            'lencana_kelas' => 'bg-sandgold text-bronze',
            'catatan' => 'Dari 4 SPP & 2 Registrasi',
        ],
    ];

    $verifikasi = [
        [
            'huruf' => 'A',
            'nama' => 'Aisyah Nurul Hikmah',
            'status' => 'Menunggu Review',
            'status_kelas' => 'bg-sandgold text-bronze',
            'sekolah' => 'SMAN 1 Yogyakarta',
            'kelas' => 'Kelas 12',
            'biaya' => 'Rp 3.500.000',
            'ket_biaya' => 'Lunas via BCA Virtual Account',
            'aksi' => 'Periksa Berkas',
            'aksi_kelas' => 'bg-espresso text-white hover:bg-bark-800',
            'aksi_ikon' => null,
            'redup' => false,
        ],
        [
            'huruf' => 'R',
            'nama' => 'Rayhan Pratama',
            'status' => 'Perlu Perbaikan',
            'status_kelas' => 'bg-ember-100 text-ember-700',
            'sekolah' => 'SMP IT Al-Fityan',
            'kelas' => 'Kelas 9',
            'biaya' => 'Rp 500.000',
            'ket_biaya' => 'DP 20% - Bukti transfer terpotong',
            'aksi' => 'Lihat Bukti',
            'aksi_kelas' => 'border border-line bg-white text-espresso hover:bg-sand',
            'aksi_ikon' => 'eye',
            'redup' => false,
        ],
        [
            'huruf' => 'F',
            'nama' => 'Fakhri Daniswara',
            'status' => 'Diterima & Terbit NIS',
            'status_kelas' => 'bg-sand text-muted',
            'sekolah' => 'SMA De Britto',
            'kelas' => 'Kelas 11',
            'biaya' => 'Rp 4.200.000',
            'ket_biaya' => 'Lunas Mandiri - NIS: #2425-1108',
            'aksi' => 'Selesai',
            'aksi_kelas' => 'bg-sand text-bark-400',
            'aksi_ikon' => 'check-circle',
            'redup' => true,
        ],
    ];

    $sesi = [
        [
            'kode' => 'MTK',
            'nama' => 'Matematika Wajib (Kalkulus Sederhana)',
            'kelas' => 'Kelas 12',
            'tentor' => 'Dimas Wahyu, S.Pd',
            'ruang' => 'Ruang Bima (Lt. 2)',
            'status' => 'Berlangsung (18/20 Hadir)',
            'status_kelas' => 'bg-gold text-espresso',
            'aktif' => true,
        ],
        [
            'kode' => 'FIS',
            'nama' => 'Fisika Terapan (Gelombang Elektromagnetik)',
            'kelas' => 'Kelas 11',
            'tentor' => 'Rina Novianti, M.Si',
            'ruang' => 'Ruang Gajah Mada (Lt. 1)',
            'status' => 'Berlangsung (15/15 Hadir)',
            'status_kelas' => 'bg-gold text-espresso',
            'aktif' => true,
        ],
        [
            'kode' => 'ING',
            'nama' => 'Bahasa Inggris (TOEFL Prep & Reading)',
            'kelas' => 'Kelas 10',
            'tentor' => 'Fajar Anshori, B.Ed',
            'ruang' => 'Ruang Diponegoro (Lt. 2)',
            'status' => 'Siap Mulai (Persiapan Tentor)',
            'status_kelas' => 'bg-sand text-bark-400',
            'aktif' => false,
        ],
    ];
@endphp

{{-- Kepala halaman --}}
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <p class="flex items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.16em] text-bronze">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                Bimbel Qualify
            </p>
            <h1 class="mt-2.5 text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">
                Dashboard Overview &amp; Pusat Kendali
            </h1>
            <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Ringkasan metrik harian, verifikasi pendaftaran tertunda, jadwal sesi belajar hari ini, dan ikhtisar kas operasional cabang utama.
            </p>
        </div>

        <button type="button"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-espresso px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-bark-800 lg:mt-1">
            @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4 text-gold'])
            Unduh Rekap Harian (PDF)
        </button>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Empat angka pokok hari ini --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kpis as $kpi)
            <article @class([
                'rounded-xl border border-line border-t-[3px] bg-white p-4',
                $kpi['aksen'],
            ])>
                <div class="flex items-start justify-between gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ $kpi['tile'] }}">
                        @include('layouts.partials.icon', ['name' => $kpi['ikon'], 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <span class="rounded-full px-2.5 py-1 text-[10.5px] font-bold leading-none {{ $kpi['lencana_kelas'] }}">{{ $kpi['lencana'] }}</span>
                </div>
                <p class="mt-3.5 text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">{{ $kpi['label'] }}</p>
                <p class="mt-1.5 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $kpi['nilai'] }}</p>
                <p class="mt-3.5 flex items-center gap-1.5 text-[11.5px] font-medium leading-snug text-bark-700">
                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                    {{ $kpi['catatan'] }}
                </p>
            </article>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        {{-- Kolom utama --}}
        <div class="space-y-5 xl:col-span-2">

            {{-- Verifikasi pendaftaran --}}
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3">
                    <div class="min-w-0 max-w-lg">
                        <h2 class="flex items-start gap-2.5 text-[18px] font-bold leading-snug text-ink">
                            <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-gold"></span>
                            <span>Aksi Cepat: Pendaftaran Menunggu Verifikasi</span>
                        </h2>
                        <p class="mt-2 pl-[18px] text-[12.5px] leading-relaxed text-muted">
                            Konfirmasi pelunasan administrasi dan penerbitan Nomor Induk Siswa (NIS) baru.
                        </p>
                    </div>
                    <span class="shrink-0 rounded-full bg-gold px-3.5 py-2 text-[11.5px] font-bold leading-none text-espresso">
                        3 Prioritas Utama
                    </span>
                </header>

                <div class="mt-5 space-y-3">
                    @foreach ($verifikasi as $baris)
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-3 rounded-xl border border-line bg-cream px-4 py-3.5">
                            <span @class([
                                'grid h-10 w-10 shrink-0 place-items-center rounded-full text-[14px] font-bold',
                                'bg-palegold text-espresso' => ! $baris['redup'],
                                'bg-white text-bark-300' => $baris['redup'],
                            ]) aria-hidden="true">{{ $baris['huruf'] }}</span>

                            <div class="min-w-0 flex-1 basis-56">
                                <p class="flex flex-wrap items-center gap-2">
                                    <span @class(['text-[13.5px] font-bold text-ink', 'text-muted' => $baris['redup']])>{{ $baris['nama'] }}</span>
                                    <span class="rounded-md px-2 py-1 text-[10px] font-bold leading-none {{ $baris['status_kelas'] }}">{{ $baris['status'] }}</span>
                                </p>
                                <p class="mt-1.5 text-[11.5px] leading-snug text-muted">
                                    {{ $baris['sekolah'] }} <span class="text-bark-300">•</span> {{ $baris['kelas'] }}
                                </p>
                                <p class="mt-1 text-[11.5px] leading-snug text-muted">
                                    Biaya: <span class="font-bold text-ink">{{ $baris['biaya'] }}</span>
                                    <span class="text-bark-400">({{ $baris['ket_biaya'] }})</span>
                                </p>
                            </div>

                            <button type="button"
                                    class="ml-auto inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3.5 py-2.5 text-[12.5px] font-semibold transition {{ $baris['aksi_kelas'] }}">
                                @if ($baris['aksi_ikon'])
                                    @include('layouts.partials.icon', ['name' => $baris['aksi_ikon'], 'class' => 'h-4 w-4'])
                                @endif
                                {{ $baris['aksi'] }}
                            </button>
                        </div>
                    @endforeach
                </div>

                <footer class="mt-4 flex justify-end">
                    <a href="#" class="text-[12.5px] font-semibold text-bronze transition hover:text-espresso focus-visible:underline">
                        Lihat Semua 12 Pendaftaran <span aria-hidden="true">→</span>
                    </a>
                </footer>
            </section>

            {{-- Jadwal sesi berjalan --}}
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-4">
                    <div class="min-w-0 max-w-md">
                        <div class="flex items-start gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                                @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4.5 w-4.5'])
                            </span>
                            <div class="min-w-0">
                                <h2 class="text-[17px] font-bold leading-snug text-ink">Jadwal &amp; Presensi Sesi Berjalan</h2>
                                <p class="mt-1 text-[12.5px] leading-relaxed text-muted">
                                    Pantau kehadiran tentor, ketersediaan ruang belajar, dan presensi realtime siswa.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 rounded-xl bg-sand p-1" role="group" aria-label="Pilih sesi belajar">
                        <button type="button" aria-pressed="true"
                                class="rounded-lg border border-line bg-white px-3 py-2 text-center text-[11.5px] font-bold leading-tight text-ink shadow-[0_1px_2px_rgba(20,20,20,0.06)]">
                            Sesi Sore<br><span class="font-semibold text-muted">(15:30 - 17:30)</span>
                        </button>
                        <button type="button" aria-pressed="false"
                                class="rounded-lg px-3 py-2 text-center text-[11.5px] font-semibold leading-tight text-bark-400 transition hover:text-espresso">
                            Sesi Malam<br><span class="font-medium">(18:30 - 20:30)</span>
                        </button>
                    </div>
                </header>

                <div class="mt-5 space-y-3">
                    @foreach ($sesi as $sesiRow)
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-3 rounded-xl border border-line bg-cream px-4 py-3.5">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg border border-line bg-white text-[12px] font-extrabold tracking-wide text-ink"
                                  aria-hidden="true">{{ $sesiRow['kode'] }}</span>

                            <div class="min-w-0 flex-1 basis-56">
                                <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $sesiRow['nama'] }}</p>
                                <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11.5px] leading-snug text-muted">
                                    <span class="inline-flex items-center gap-1.5">
                                        @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                                        {{ $sesiRow['tentor'] }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                                        {{ $sesiRow['ruang'] }}
                                    </span>
                                </p>
                            </div>

                            <div class="ml-auto flex shrink-0 flex-wrap items-center gap-2">
                                <span class="rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold leading-none text-bronze">{{ $sesiRow['kelas'] }}</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-[10.5px] font-bold leading-none {{ $sesiRow['status_kelas'] }}">
                                    @if ($sesiRow['aktif'])
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-espresso"></span>
                                    @endif
                                    {{ $sesiRow['status'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <footer class="mt-4 flex justify-center">
                    <a href="#" class="text-[12.5px] font-semibold text-bronze transition hover:text-espresso focus-visible:underline">
                        Buka Manajemen Absensi &amp; Jadwal Lengkap <span aria-hidden="true">→</span>
                    </a>
                </footer>
            </section>
        </div>

        {{-- Kolom kanan --}}
        <div class="space-y-5">

            {{-- Arus kas --}}
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex items-start gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-ink">
                        @include('layouts.partials.icon', ['name' => 'receipt', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-[15px] font-bold leading-snug text-ink">Ringkasan Arus Kas</h2>
                        <p class="mt-0.5 text-[11.5px] leading-snug text-muted">Perhitungan Real-time Hari Ini</p>
                    </div>
                    <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-gold"></span>
                </header>

                <div class="mt-4 space-y-2.5">
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-cream px-3.5 py-3">
                        <span class="flex min-w-0 items-center gap-2 text-[12.5px] text-muted">
                            @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4 shrink-0 text-bronze'])
                            <span class="truncate">Total Masuk</span>
                        </span>
                        <span class="shrink-0 text-[14px] font-extrabold tabular-nums text-ink">Rp 4.850.000</span>
                    </div>

                    <div class="flex items-center justify-between gap-3 rounded-xl bg-cream px-3.5 py-3">
                        <span class="flex min-w-0 items-center gap-2 text-[12.5px] text-muted">
                            @include('layouts.partials.icon', ['name' => 'upload', 'class' => 'h-4 w-4 shrink-0 text-ember-600'])
                            <span class="leading-snug">Total Keluar (Operasional)</span>
                        </span>
                        <span class="shrink-0 text-[14px] font-extrabold tabular-nums text-ink">Rp 385.000</span>
                    </div>

                    <div class="flex items-center justify-between gap-3 rounded-xl bg-espresso px-4 py-3.5">
                        <div class="min-w-0">
                            <p class="text-[9.5px] font-bold uppercase tracking-[0.14em] text-palegold">Saldo Bersih Hari Ini</p>
                            <p class="mt-1.5 text-[24px] font-extrabold leading-none tracking-tight tabular-nums text-white">Rp 4.465.000</p>
                        </div>
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gold text-espresso">
                            @include('layouts.partials.icon', ['name' => 'wallet', 'class' => 'h-4.5 w-4.5'])
                        </span>
                    </div>
                </div>

                <a href="#"
                   class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-lg border border-line bg-white py-2.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand focus-visible:underline">
                    Lihat Jurnal Transaksi <span aria-hidden="true">›</span>
                </a>
            </section>

            {{-- Klinik PR --}}
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex items-start gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'help-circle', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-[15px] font-bold leading-snug text-ink">Klinik PR Siswa Hari Ini</h2>
                        <p class="mt-0.5 text-[11.5px] leading-snug text-muted">Bimbingan Tatap Muka 1-on-1</p>
                    </div>
                    <span class="shrink-0 rounded-md bg-gold px-2 py-1 text-[10.5px] font-bold leading-none text-espresso">5 Antre</span>
                </header>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-line bg-cream px-3.5 py-3">
                        <p class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">Tiket Masuk</p>
                        <p class="mt-1.5 text-[19px] font-extrabold leading-none tracking-tight text-ink">18 Tiket</p>
                    </div>
                    <div class="rounded-xl border border-line bg-cream px-3.5 py-3">
                        <p class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">Butuh Tentor</p>
                        <p class="mt-1.5 text-[19px] font-extrabold leading-none tracking-tight text-ink">5 Siswa</p>
                    </div>
                </div>

                <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-sand" role="img" aria-label="Kapasitas klinik PR terpakai 60 persen">
                    <span class="block h-full w-[60%] rounded-full bg-gold"></span>
                </div>

                <footer class="mt-4 flex justify-end">
                    <a href="#" class="text-[12.5px] font-semibold text-bronze transition hover:text-espresso focus-visible:underline">
                        Kelola Tiket Klinik PR <span aria-hidden="true">→</span>
                    </a>
                </footer>
            </section>

            {{-- Agenda --}}
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex items-center gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <h2 class="text-[15px] font-bold leading-snug text-ink">Agenda &amp; Sistem Bimbel</h2>
                </header>

                <div class="mt-4 rounded-xl bg-cream p-4">
                    <div class="flex items-start gap-2.5">
                        <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full border border-gold/50 bg-sandgold text-bronze">
                            @include('layouts.partials.icon', ['name' => 'bookmark', 'class' => 'h-3.5 w-3.5'])
                        </span>
                        <div class="min-w-0">
                            <p class="text-[13px] font-bold leading-snug text-ink">Semester Ganjil T.A. 2024/2025</p>
                            <p class="mt-1.5 text-[11.5px] leading-relaxed text-muted">
                                Status Aktif Berjalan. Masa pendaftaran gelombang kedua berakhir 30 Oktober.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    {{-- Catatan sistem --}}
    <section class="flex flex-col gap-2 rounded-2xl border border-line bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-muted">
            @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'mt-[1px] h-4 w-4 shrink-0 text-gold'])
            Data cabang utama disinkronkan dari modul Pendaftaran, Keuangan, dan Presensi QUALIFY.
        </p>
        <p class="shrink-0 text-[11px] text-muted sm:text-right">Terakhir disinkronkan: <span class="font-bold text-ink">Hari ini, 08:15 WIB</span></p>
    </section>
</div>
@endsection
