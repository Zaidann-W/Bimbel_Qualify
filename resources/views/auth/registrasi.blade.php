@extends('layouts.main')

@section('title', 'Formulir Pendaftaran Siswa Baru — Bimbel QUALIFY')
@section('meta_description', 'Daftar siswa baru Bimbel QUALIFY secara online dalam 3 menit tanpa membuat akun. Isi data calon siswa, kontak wali, dan pilih sesi belajar.')

@section('content')
    <section class="relative overflow-hidden bg-cream">
        {{-- Hangat khas QUALIFY: dua wash radial seperti pada halaman beranda --}}
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -top-40 left-[6%] h-[560px] w-[820px] rounded-full bg-[radial-gradient(closest-side,rgba(245,196,84,0.30),rgba(245,196,84,0))]"></div>
            <div class="absolute -top-32 right-[4%] h-[460px] w-[640px] rounded-full bg-[radial-gradient(closest-side,rgba(238,216,172,0.55),rgba(238,216,172,0))]"></div>
        </div>

        <div class="relative mx-auto max-w-[1400px] px-6 lg:px-12 pt-10 lg:pt-14 pb-16 lg:pb-24">
            {{-- Ornamen halaman --}}
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-[13px] text-muted">
                <a href="{{ url('/') }}" class="hover:text-ink transition-colors">Beranda</a>
                @include('partials.icon', ['name' => 'chevron-right', 'size' => 14, 'class' => 'shrink-0 text-ink/35'])
                <span class="font-semibold text-ink" aria-current="page">Pendaftaran Siswa Baru</span>
            </nav>

            <div class="mt-6 max-w-[780px]">
                <p class="inline-flex items-center gap-2.5 bg-espresso rounded-full pl-2 pr-4 py-2 text-[11.5px] font-bold uppercase tracking-[0.1em] text-[#f0e2c6]">
                    @include('partials.icon', ['name' => 'lock', 'size' => 15, 'class' => 'shrink-0 text-gold'])
                    Akses Terbuka untuk Calon Siswa &amp; Wali Murid
                </p>

                <h1 class="mt-6 text-[34px] sm:text-[40px] lg:text-[44px] font-extrabold leading-[1.18] tracking-[-0.02em] text-ink">
                    Formulir Pendaftaran Siswa Baru
                </h1>

                <p class="mt-5 text-[16px] lg:text-[17px] leading-[1.75] text-muted max-w-[620px]">
                    Bergabung bersama Bimbel QUALIFY. Isi data di bawah dengan benar. Proses cepat 3 menit
                    tanpa perlu membuat akun terlebih dahulu.
                </p>
            </div>

            {{-- Pita tahun ajaran: satu-satunya momen mencolok di halaman ini --}}
            <div class="mt-8 flex flex-wrap items-center gap-4 rounded-2xl bg-gradient-to-r from-sandgold via-[#fdf5e3] to-sandgold/40 border border-palegold/70 px-5 py-4 sm:px-6">
                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gold text-espresso">
                    @include('partials.icon', ['name' => 'calendar', 'size' => 21, 'stroke' => 1.9])
                </span>
                <p class="text-[15.5px] sm:text-[16.5px] font-bold text-ink">Pendaftaran Tahun Ajaran 2025/2026</p>
                <span class="sm:ml-auto inline-flex items-center gap-2 bg-espresso rounded-full px-3.5 py-2 text-[11px] font-bold uppercase tracking-[0.11em] text-[#f0e2c6]">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold motion-safe:animate-pulse" aria-hidden="true"></span>
                    Gelombang Aktif
                </span>
            </div>

            {{-- Panel sukses, menggantikan formulir setelah terkirim --}}
            <div
                id="pendaftaran-sukses"
                class="hidden mt-6 rounded-[24px] border border-line/60 bg-paper p-8 sm:p-12 text-center shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]"
                role="status"
                aria-live="polite"
            >
                <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gold text-espresso">
                    @include('partials.icon', ['name' => 'check-circle', 'size' => 26, 'stroke' => 1.9])
                </span>
                <h2 class="mt-6 text-[26px] sm:text-[28px] font-extrabold leading-tight text-ink">Pendaftaran Terkirim</h2>
                <p class="mt-4 mx-auto max-w-[560px] text-[15.5px] leading-[1.75] text-muted">
                    Terima kasih, <span id="sukses-nama" class="font-bold text-ink">calon siswa</span>.
                    Data pendaftaran sudah kami terima. Admin akan menghubungi lewat WhatsApp dalam 1×24 jam kerja
                    untuk verifikasi kuota kelas.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a
                        href="{{ url('/') }}"
                        class="inline-flex items-center bg-espresso hover:bg-[#3a2515] text-paper font-bold text-[15px] px-6 py-3.5 rounded-xl transition-colors"
                    >
                        Kembali ke Beranda
                    </a>
                    <button
                        type="button"
                        id="ulangi-pendaftaran"
                        class="inline-flex items-center bg-sand hover:bg-[#e9e6dd] border border-line text-ink font-bold text-[15px] px-6 py-3.5 rounded-xl transition-colors"
                    >
                        Isi Formulir Lagi
                    </button>
                </div>
            </div>

            <form
                id="form-pendaftaran"
                action="{{ route('registrasi') }}"
                method="POST"
                enctype="multipart/form-data"
                novalidate
                class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_360px] xl:gap-8"
            >
                {{-- Honeypot anti-spam: bot-isian akan mengisi field ini, manusia akan melewati --}}
                <input type="text" name="anti_spam_honeypot" class="hidden" autocomplete="off">
                <div class="space-y-6 min-w-0">
                    {{-- Bagian 1 — Data calon siswa --}}
                    <section class="bg-paper border border-line/60 rounded-[24px] p-6 sm:p-8 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                        <header class="flex items-start gap-3.5">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-espresso text-[14px] font-bold text-gold">
                                1
                            </span>
                            <div>
                                <h2 class="text-[20px] font-bold leading-tight text-ink">Data Calon Siswa</h2>
                                <p class="mt-1 text-[13.5px] leading-[1.6] text-muted">
                                    Isi data dasar siswa untuk registrasi rombel belajar.
                                </p>
                            </div>
                        </header>

                        <div class="mt-6 border-t border-line/70"></div>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <div data-field>
                                <label for="nama_lengkap" class="block text-[13.5px] font-bold text-ink">
                                    Nama Lengkap Siswa <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_lengkap"
                                    name="nama_lengkap"
                                    data-label="Nama lengkap siswa"
                                    data-control-surface
                                    data-border-default="border-line"
                                    required
                                    autocomplete="name"
                                    placeholder="Contoh: Muhammad Rayhan Pratama"
                                    class="mt-2 w-full rounded-xl border border-line bg-shell px-4 py-3.5 text-[15px] text-ink placeholder:text-muted/55 transition-colors focus:border-gold focus:bg-paper focus:outline-none focus:ring-4 focus:ring-gold/20"
                                >
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="nama_lengkap" role="alert"></p>
                            </div>

                            <div data-field>
                                <label for="nama_panggilan" class="block text-[13.5px] font-bold text-ink">
                                    Nama Panggilan Siswa <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_panggilan"
                                    name="nama_panggilan"
                                    data-label="Nama panggilan siswa"
                                    data-control-surface
                                    data-border-default="border-line"
                                    required
                                    placeholder="Contoh: Rayhan"
                                    class="mt-2 w-full rounded-xl border border-line bg-shell px-4 py-3.5 text-[15px] text-ink placeholder:text-muted/55 transition-colors focus:border-gold focus:bg-paper focus:outline-none focus:ring-4 focus:ring-gold/20"
                                >
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="nama_panggilan" role="alert"></p>
                            </div>

                            <div data-field>
                                <label for="tanggal_lahir" class="block text-[13.5px] font-bold text-ink">
                                    Tanggal Lahir Siswa <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="tanggal_lahir"
                                    name="tanggal_lahir"
                                    data-label="Tanggal lahir siswa"
                                    data-control-surface
                                    data-border-default="border-line"
                                    required
                                    class="mt-2 w-full rounded-xl border border-line bg-shell px-4 py-3.5 text-[15px] text-ink transition-colors focus:border-gold focus:bg-paper focus:outline-none focus:ring-4 focus:ring-gold/20"
                                >
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="tanggal_lahir" role="alert"></p>
                            </div>

                            <div data-field>
                                <label for="kelas" class="block text-[13.5px] font-bold text-ink">
                                    Kelas T.A (Tahun Ajaran) <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <div class="relative mt-2">
                                    <select
                                        id="kelas"
                                        name="kelas"
                                        data-label="Kelas / jenjang"
                                        data-control-surface
                                        data-border-default="border-line"
                                        required
                                        class="w-full appearance-none rounded-xl border border-line bg-shell px-4 py-3.5 pr-11 text-[15px] text-ink transition-colors focus:border-gold focus:bg-paper focus:outline-none focus:ring-4 focus:ring-gold/20"
                                    >
                                        <option value="" selected disabled>Pilih Jenjang / Kelas T.A 2025/2026</option>
                                        <optgroup label="SD">
                                            <option value="sd-4">SD Kelas 4</option>
                                            <option value="sd-5">SD Kelas 5</option>
                                            <option value="sd-6">SD Kelas 6</option>
                                        </optgroup>
                                        <optgroup label="SMP">
                                            <option value="smp-7">SMP Kelas 7</option>
                                            <option value="smp-8">SMP Kelas 8</option>
                                            <option value="smp-9">SMP Kelas 9</option>
                                        </optgroup>
                                        <optgroup label="SMA">
                                            <option value="sma-10">SMA Kelas 10</option>
                                            <option value="sma-11">SMA Kelas 11</option>
                                            <option value="sma-12">SMA Kelas 12</option>
                                        </optgroup>
                                    </select>
                                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-ink/45" aria-hidden="true">
                                        @include('partials.icon', ['name' => 'chevron-down', 'size' => 18])
                                    </span>
                                </div>
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="kelas" role="alert"></p>
                            </div>

                            <div data-field>
                                <label for="asal_sekolah" class="block text-[13.5px] font-bold text-ink">
                                    Asal Sekolah <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="asal_sekolah"
                                    name="asal_sekolah"
                                    data-label="Asal sekolah"
                                    data-control-surface
                                    data-border-default="border-line"
                                    required
                                    placeholder="Contoh: SMPN 1 Yogyakarta"
                                    class="mt-2 w-full rounded-xl border border-line bg-shell px-4 py-3.5 text-[15px] text-ink placeholder:text-muted/55 transition-colors focus:border-gold focus:bg-paper focus:outline-none focus:ring-4 focus:ring-gold/20"
                                >
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="asal_sekolah" role="alert"></p>
                            </div>

                            <div data-field>
                                <label for="wa_siswa" class="block text-[13.5px] font-bold text-ink">
                                    No. WA Siswa <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <div
                                    class="mt-2 flex items-stretch rounded-xl border border-line bg-shell transition-colors focus-within:border-gold focus-within:bg-paper focus-within:ring-4 focus-within:ring-gold/20"
                                    data-control-surface
                                    data-border-default="border-line"
                                >
                                    <span class="inline-flex items-center border-r border-line/80 px-4 text-[15px] font-semibold text-muted" aria-hidden="true">+62</span>
                                    <input
                                        type="tel"
                                        id="wa_siswa"
                                        name="wa_siswa"
                                        data-label="No. WA siswa"
                                        required
                                        inputmode="tel"
                                        autocomplete="tel"
                                        placeholder="812-xxxx-xxxx"
                                        class="w-full min-w-0 bg-transparent px-4 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none"
                                    >
                                </div>
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="wa_siswa" role="alert"></p>
                            </div>

                            <div class="sm:col-span-2" data-field>
                                <label for="alamat" class="block text-[13.5px] font-bold text-ink">
                                    Alamat Tempat Tinggal <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="alamat"
                                    name="alamat"
                                    data-label="Alamat tempat tinggal"
                                    data-control-surface
                                    data-border-default="border-line"
                                    required
                                    placeholder="Alamat lengkap tempat tinggal siswa"
                                    class="mt-2 w-full rounded-xl border border-line bg-shell px-4 py-3.5 text-[15px] text-ink placeholder:text-muted/55 transition-colors focus:border-gold focus:bg-paper focus:outline-none focus:ring-4 focus:ring-gold/20"
                                >
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="alamat" role="alert"></p>
                            </div>
                        </div>

                        {{-- Unggah rapor --}}
                        <div class="mt-7 rounded-2xl border border-line/70 bg-shell/70 p-5">
                            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                <h3 class="text-[14.5px] font-bold text-ink">
                                    Berkas Rapor 2 Semester Terakhir <span class="text-bronze" aria-hidden="true">*</span>
                                </h3>
                                <p class="text-[12.5px] text-muted">Format PDF, JPG, PNG (Maks. 5MB)</p>
                            </div>

                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                @foreach ([
                                    ['rapor_ganjil', '1. Rapor Semester Sebelumnya (Ganjil)', 'Berkas rapor semester ganjil'],
                                    ['rapor_genap', '2. Rapor Semester Terakhir (Genap)', 'Berkas rapor semester genap'],
                                ] as [$name, $caption, $labelBerkas])
                                    <div data-field>
                                        <p id="{{ $name }}-caption" class="text-[12.5px] font-semibold text-muted">
                                            {{ $caption }}
                                        </p>
                                        <label
                                            for="{{ $name }}"
                                            data-dropzone
                                            data-control-surface
                                            data-border-default="border-line"
                                            aria-describedby="{{ $name }}-caption"
                                            class="mt-2 flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-dashed border-line bg-paper px-4 py-7 text-center transition-colors hover:border-gold hover:bg-sandgold/40"
                                        >
                                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sandgold text-espresso" aria-hidden="true">
                                                @include('partials.icon', ['name' => 'file-up', 'size' => 20, 'stroke' => 1.9])
                                            </span>
                                            <span class="mt-1 max-w-full break-all text-[14px] font-bold text-ink" data-dropzone-title>
                                                Pilih Berkas Rapor
                                            </span>
                                            <span class="text-[12.5px] text-muted" data-dropzone-hint>
                                                Klik atau seret file PDF / Foto di sini
                                            </span>
                                        </label>
                                        <input
                                            type="file"
                                            id="{{ $name }}"
                                            name="{{ $name }}"
                                            data-label="{{ $labelBerkas }}"
                                            required
                                            accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                                            class="sr-only"
                                        >
                                        <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="{{ $name }}" role="alert"></p>
                                    </div>
                                @endforeach
                            </div>

                            <p class="mt-4 flex items-start gap-2.5 text-[12.5px] leading-[1.6] text-muted">
                                @include('partials.icon', ['name' => 'info', 'size' => 15, 'class' => 'shrink-0 mt-px text-bronze'])
                                Pastikan halaman biodata rapor dan tabel nilai semester tampak jelas serta terbaca.
                            </p>
                        </div>
                    </section>

                    {{-- Bagian 2 — Kontak wali & pengaturan grup --}}
                    <section class="bg-paper border border-line/60 rounded-[24px] p-6 sm:p-8 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                        <header class="flex items-start gap-3.5">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-espresso text-[14px] font-bold text-gold">
                                2
                            </span>
                            <div>
                                <h2 class="text-[20px] font-bold leading-tight text-ink">Kontak Orang Tua / Wali &amp; Pengaturan Grup</h2>
                                <p class="mt-1 text-[13.5px] leading-[1.6] text-muted">
                                    Informasi wali siswa, nomor WhatsApp grup bimbingan, dan pilihan sesi belajar.
                                </p>
                            </div>
                        </header>

                        <div class="mt-6 border-t border-line/70"></div>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <div data-field>
                                <label for="nama_wali" class="block text-[13.5px] font-bold text-ink">
                                    Nama Orang Tua / Wali <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_wali"
                                    name="nama_wali"
                                    data-label="Nama orang tua / wali"
                                    data-control-surface
                                    data-border-default="border-line"
                                    required
                                    autocomplete="name"
                                    placeholder="Contoh: Hendra Pratama"
                                    class="mt-2 w-full rounded-xl border border-line bg-shell px-4 py-3.5 text-[15px] text-ink placeholder:text-muted/55 transition-colors focus:border-gold focus:bg-paper focus:outline-none focus:ring-4 focus:ring-gold/20"
                                >
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="nama_wali" role="alert"></p>
                            </div>

                            <div data-field>
                                <label for="wa_wali" class="block text-[13.5px] font-bold text-ink">
                                    No. WA Wali <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <div
                                    class="mt-2 flex items-stretch rounded-xl border border-line bg-shell transition-colors focus-within:border-gold focus-within:bg-paper focus-within:ring-4 focus-within:ring-gold/20"
                                    data-control-surface
                                    data-border-default="border-line"
                                >
                                    <span class="inline-flex items-center border-r border-line/80 px-4 text-[15px] font-semibold text-muted" aria-hidden="true">+62</span>
                                    <input
                                        type="tel"
                                        id="wa_wali"
                                        name="wa_wali"
                                        data-label="No. WA wali"
                                        required
                                        inputmode="tel"
                                        autocomplete="tel"
                                        placeholder="812-xxxx-xxxx"
                                        class="w-full min-w-0 bg-transparent px-4 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none"
                                    >
                                </div>
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="wa_wali" role="alert"></p>
                            </div>

                            <div class="sm:col-span-2" data-field>
                                <label for="wa_grup" class="block text-[13.5px] font-bold text-ink">
                                    No. yang Dimasukkan ke Grup <span class="text-bronze" aria-hidden="true">*</span>
                                </label>
                                <div
                                    class="mt-2 flex items-stretch rounded-xl border border-line bg-shell transition-colors focus-within:border-gold focus-within:bg-paper focus-within:ring-4 focus-within:ring-gold/20"
                                    data-control-surface
                                    data-border-default="border-line"
                                >
                                    <span class="inline-flex items-center border-r border-line/80 px-4 text-[15px] font-semibold text-muted" aria-hidden="true">+62</span>
                                    <input
                                        type="tel"
                                        id="wa_grup"
                                        name="wa_grup"
                                        data-label="No. WhatsApp grup"
                                        required
                                        inputmode="tel"
                                        placeholder="Nomor WA yang dimasukkan ke grup kelas/bimbel (contoh: 812-xxxx-xxxx)"
                                        class="w-full min-w-0 bg-transparent px-4 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none"
                                    >
                                </div>
                                <p class="mt-2 text-[12.5px] leading-[1.6] text-muted">
                                    Nomor WhatsApp yang akan didaftarkan ke grup bimbel / WhatsApp group koordinasi rombel.
                                </p>
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="wa_grup" role="alert"></p>
                            </div>

                            <fieldset class="sm:col-span-2" data-field>
                                <legend class="text-[13.5px] font-bold text-ink">
                                    Pilihan Sesi Belajar <span class="text-bronze" aria-hidden="true">*</span>
                                </legend>
                                <div class="mt-2.5 grid gap-3 rounded-xl sm:grid-cols-2" data-control-surface>
                                    <label class="flex cursor-pointer items-center gap-3.5 rounded-xl border-2 border-line bg-shell px-4 py-4 transition-colors has-[:checked]:border-gold has-[:checked]:bg-sandgold/70">
                                        <input type="radio" name="sesi" value="sore" data-label="sesi belajar" required class="peer sr-only">
                                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-line bg-paper text-bronze" aria-hidden="true">
                                            @include('partials.icon', ['name' => 'sun', 'size' => 19])
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-[15px] font-bold text-ink">Sesi Sore</span>
                                            <span class="block text-[12.5px] font-semibold text-bronze">15.30 - 17.30 WIB</span>
                                        </span>
                                        <span class="relative grid h-5 w-5 shrink-0 place-items-center rounded-full border-2 border-line bg-paper transition-colors before:absolute before:left-1/2 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-x-1/2 before:-translate-y-1/2 before:rounded-full before:bg-gold before:opacity-0 before:transition-opacity peer-checked:border-espresso peer-checked:bg-espresso peer-checked:before:opacity-100 peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true"></span>
                                    </label>

                                    <label class="flex cursor-pointer items-center gap-3.5 rounded-xl border-2 border-line bg-shell px-4 py-4 transition-colors has-[:checked]:border-gold has-[:checked]:bg-sandgold/70">
                                        <input type="radio" name="sesi" value="malam" class="peer sr-only">
                                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-line bg-paper text-bronze" aria-hidden="true">
                                            @include('partials.icon', ['name' => 'moon', 'size' => 19])
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-[15px] font-bold text-ink">Sesi Malam</span>
                                            <span class="block text-[12.5px] font-semibold text-bronze">18.30 - 20.30 WIB</span>
                                        </span>
                                        <span class="relative grid h-5 w-5 shrink-0 place-items-center rounded-full border-2 border-line bg-paper transition-colors before:absolute before:left-1/2 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-x-1/2 before:-translate-y-1/2 before:rounded-full before:bg-gold before:opacity-0 before:transition-opacity peer-checked:border-espresso peer-checked:bg-espresso peer-checked:before:opacity-100 peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true"></span>
                                    </label>
                                </div>
                                <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="sesi" role="alert"></p>
                            </fieldset>
                        </div>

                        <div class="mt-7" data-field>
                            <label class="-m-1 flex cursor-pointer items-start gap-3 rounded-xl p-1" data-control-surface>
                                <input type="checkbox" name="setuju" data-label="persetujuan" required class="peer sr-only">
                                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-md border-2 border-line bg-shell text-transparent transition-colors peer-checked:border-espresso peer-checked:bg-espresso peer-checked:text-gold peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true">
                                    @include('partials.icon', ['name' => 'check', 'size' => 12, 'stroke' => 3.2])
                                </span>
                                <span class="text-[14px] leading-[1.6] text-ink">
                                    Data yang saya masukkan sudah benar dan sesuai.
                                </span>
                            </label>
                            <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="setuju" role="alert"></p>
                        </div>

                        <button
                            type="submit"
                            class="mt-6 flex w-full items-center justify-center gap-2.5 rounded-xl bg-espresso px-6 py-[18px] text-[16px] font-bold text-white transition-colors hover:bg-[#3a2515]"
                        >
                            Kirim Pendaftaran
                            @include('partials.icon', ['name' => 'arrow-right', 'size' => 18])
                        </button>

                        <p class="mt-3.5 flex items-center justify-center gap-2 text-center text-[12.5px] text-muted">
                            @include('partials.icon', ['name' => 'lock', 'size' => 14, 'class' => 'shrink-0 text-bronze'])
                            Diproses oleh Admin via WhatsApp dalam 1×24 jam kerja.
                        </p>
                    </section>
                </div>

                {{-- Alur pendaftaran --}}
                <aside class="lg:sticky lg:top-8">
                    <div class="rounded-[24px] border border-line/60 bg-paper p-6 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                        <h2 class="flex items-center gap-2.5 text-[17.5px] font-bold text-ink">
                            @include('partials.icon', ['name' => 'trending-up', 'size' => 19, 'class' => 'shrink-0 text-bronze'])
                            Alur Pendaftaran
                        </h2>

                        <ol class="mt-5">
                            @foreach ([
                                ['Isi Formulir', 'Lengkapi formulir singkat tanpa perlu akun.'],
                                ['Verifikasi 1×24 Jam', 'Admin memeriksa ketersediaan kuota kelas.'],
                                ['Kelas Dimulai', 'Undangan masuk rombel &amp; materi bimbingan aktif.'],
                            ] as $index => [$judul, $deskripsi])
                                <li class="relative flex gap-3.5 pb-6 last:pb-0">
                                    @unless ($loop->last)
                                        <span class="absolute left-[15px] top-9 bottom-0 w-px bg-line" aria-hidden="true"></span>
                                    @endunless
                                    <span
                                        @class([
                                            'relative z-10 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-[13px] font-bold',
                                            'bg-gold text-espresso' => $loop->first,
                                            'bg-sand text-ink/60 border border-line' => ! $loop->first,
                                        ])
                                    >
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="min-w-0 pt-0.5">
                                        <p class="text-[14.5px] font-bold leading-tight text-ink">{{ $judul }}</p>
                                        <p class="mt-1.5 text-[13px] leading-[1.6] text-muted">{!! $deskripsi !!}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </aside>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('form-pendaftaran');
            var panelSukses = document.getElementById('pendaftaran-sukses');

            if (!form || !panelSukses) {
                return;
            }

            var maksBerkas = 5 * 1024 * 1024;
            var tipeBerkas = ['application/pdf', 'image/jpeg', 'image/png'];
            var gerakHemat = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function permukaan(kontrol) {
                var field = kontrol.closest('[data-field]');

                return field ? field.querySelector('[data-control-surface]') : null;
            }

            function kotakError(kontrol) {
                return form.querySelector('[data-error-for="' + kontrol.name + '"]');
            }

            function berkasSah(kontrol) {
                var berkas = kontrol.files[0];

                if (!berkas || berkas.size > maksBerkas) {
                    return false;
                }

                return tipeBerkas.indexOf(berkas.type) !== -1 || /\.(pdf|jpe?g|png)$/i.test(berkas.name || '');
            }

            function valid(kontrol) {
                if (kontrol.type === 'radio') {
                    return Boolean(form.querySelector('input[name="' + kontrol.name + '"]:checked'));
                }

                if (kontrol.type === 'checkbox') {
                    return kontrol.checked;
                }

                if (kontrol.type === 'file') {
                    return berkasSah(kontrol);
                }

                if (kontrol.type === 'tel') {
                    var digit = kontrol.value.replace(/\D/g, '');

                    return digit.length >= 8 && digit.length <= 15;
                }

                return kontrol.value.trim() !== '';
            }

            function pesanError(kontrol) {
                var label = kontrol.dataset.label || 'Bagian ini';

                if (kontrol.type === 'file') {
                    return kontrol.files.length
                        ? label + ' bukan PDF / JPG / PNG atau ukurannya melebihi 5MB.'
                        : label + ' belum dipilih.';
                }

                if (kontrol.type === 'radio') {
                    return 'Pilih salah satu ' + label + '.';
                }

                if (kontrol.type === 'checkbox') {
                    return 'Centang persetujuan sebelum mengirim pendaftaran.';
                }

                if (kontrol.type === 'tel') {
                    return label + ' harus berisi 8–15 digit angka.';
                }

                return label + ' wajib diisi.';
            }

            function gayaError(surface, adaError) {
                if (!surface) {
                    return;
                }

                surface.classList.toggle('ring-2', adaError);
                surface.classList.toggle('ring-ember-300', adaError);

                if (surface.dataset.borderDefault) {
                    surface.classList.toggle(surface.dataset.borderDefault, ! adaError);
                    surface.classList.toggle('border-ember-400', adaError);
                }
            }

            function tandaiError(kontrol, adaError) {
                var kotak = kotakError(kontrol);

                if (kotak) {
                    kotak.textContent = adaError ? pesanError(kontrol) : '';
                    kotak.classList.toggle('hidden', ! adaError);
                }

                gayaError(permukaan(kontrol), adaError);
                kontrol.setAttribute('aria-invalid', adaError ? 'true' : 'false');
            }

            function gulirKe(elemen) {
                elemen.scrollIntoView({ behavior: gerakHemat ? 'auto' : 'smooth', block: 'center' });
            }

            var wajib = Array.prototype.slice.call(form.querySelectorAll('[required]'));

            wajib.forEach(function (kontrol) {
                var eventName = ['checkbox', 'radio', 'file'].indexOf(kontrol.type) !== -1 ? 'change' : 'input';

                kontrol.addEventListener(eventName, function () {
                    if (kontrol.getAttribute('aria-invalid') === 'true') {
                        tandaiError(kontrol, ! valid(kontrol));
                    }
                });
            });

            // Pratinjau berkas: klik, seret-lepas, validasi tipe & ukuran
            Array.prototype.forEach.call(form.querySelectorAll('input[type="file"]'), function (input) {
                var field = input.closest('[data-field]');
                var dropzone = field.querySelector('[data-dropzone]');
                var judul = field.querySelector('[data-dropzone-title]');
                var petunjuk = field.querySelector('[data-dropzone-hint]');
                var judulAwal = judul.textContent.trim();
                var petunjukAwal = petunjuk.textContent.trim();

                function ukuran(byte) {
                    return byte >= 1024 * 1024
                        ? (byte / (1024 * 1024)).toFixed(1).replace('.0', '') + ' MB'
                        : Math.max(1, Math.round(byte / 1024)) + ' KB';
                }

                function bersihkan() {
                    judul.textContent = judulAwal;
                    petunjuk.textContent = petunjukAwal;
                    dropzone.classList.remove('border-gold', 'bg-sandgold/50');
                }

                function render() {
                    var berkas = input.files[0];

                    if (!berkas) {
                        bersihkan();

                        return;
                    }

                    if (!berkasSah(input)) {
                        bersihkan();
                        tandaiError(input, true);

                        return;
                    }

                    judul.textContent = berkas.name;
                    petunjuk.textContent = ukuran(berkas.size) + ' — klik untuk mengganti berkas';
                    tandaiError(input, false);
                    dropzone.classList.add('border-gold', 'bg-sandgold/50');
                    dropzone.classList.remove('border-line');
                }

                ['dragenter', 'dragover'].forEach(function (jenis) {
                    dropzone.addEventListener(jenis, function (event) {
                        event.preventDefault();
                        dropzone.classList.add('bg-sandgold/70');
                    });
                });

                ['dragleave', 'drop'].forEach(function (jenis) {
                    dropzone.addEventListener(jenis, function (event) {
                        event.preventDefault();
                        dropzone.classList.remove('bg-sandgold/70');
                    });
                });

                dropzone.addEventListener('drop', function (event) {
                    if (event.dataTransfer && event.dataTransfer.files.length) {
                        input.files = event.dataTransfer.files;
                        render();
                    }
                });

                input.addEventListener('change', render);
            });

            form.addEventListener('submit', function (event) {
                var pertamaInvalid = null;

                wajib.forEach(function (kontrol) {
                    var oke = valid(kontrol);

                    tandaiError(kontrol, ! oke);

                    if (! oke && ! pertamaInvalid) {
                        pertamaInvalid = kontrol;
                    }
                });

                if (pertamaInvalid) {
                    gulirKe(permukaan(pertamaInvalid) || pertamaInvalid);
                    pertamaInvalid.focus({ preventScroll: true });

                    event.preventDefault();

                    return;
                }

                // Let the form submit natively; the server-side Form Request
                // will validate again and redirect to konfirmasi on success.
                // No event.preventDefault() here — the browser will post
                // the form to {{ route('registrasi') }} and the controller
                // will redirect to /registrasi/konfirmasi with the registration number.
            });

            document.getElementById('ulangi-pendaftaran').addEventListener('click', function () {
                form.reset();

                wajib.forEach(function (kontrol) {
                    tandaiError(kontrol, false);
                    kontrol.removeAttribute('aria-invalid');
                });

                Array.prototype.forEach.call(form.querySelectorAll('input[type="file"]'), function (input) {
                    input.dispatchEvent(new Event('change'));
                });

                panelSukses.classList.add('hidden');
                form.classList.remove('hidden');
                form.scrollIntoView({ behavior: gerakHemat ? 'auto' : 'smooth', block: 'start' });
            });
        })();
    </script>
@endpush
