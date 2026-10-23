@extends('layouts.manager')

@section('title', 'Profil Saya')

{{-- Halaman profil manajer: identitas eksekutif, ringkasan kinerja cabang, dan panel otoritas terkunci.
     Seluruh data masih mock UI sesuai rancangan docs/profil-manager.png; sambungkan ke modul HR & CMS --}}
@section('content')
@php
    $statistik = [
        [
            'label' => 'Total Siswa Dibina',
            'ikon' => 'graduation-cap',
            'nilai' => '842',
            'satuan' => 'Siswa',
            'catatan' => 'Jenjang SD, SMP, SMA',
        ],
        [
            'label' => 'Staf Pengajar Cabang',
            'ikon' => 'users',
            'nilai' => '36',
            'satuan' => 'Guru',
            'catatan' => '12 Master Teacher',
        ],
        [
            'label' => 'Kepatuhan Kurikulum',
            'ikon' => 'clipboard-check',
            'nilai' => '98.4%',
            'catatan' => 'Pekan ke-11 TA 2024/2025',
        ],
        [
            'label' => 'Tingkat Keamanan Akun',
            'ikon' => 'shield-check',
            'nilai' => 'Maksimal',
            'catatan' => '2FA Autentikasi Aktif',
        ],
    ];

    $kontak = [
        [
            'id' => 'nama-lengkap',
            'label' => 'Nama Lengkap (dengan Gelar)',
            'ikon' => 'user',
            'nilai' => 'Ir. Rahmat Hidayat, M.M.',
            'hint' => 'Gunakan nama resmi sesuai Surat Keputusan (SK) Pengangkatan Direksi Bimbel.',
            'tipe' => 'text',
            'wajib' => true,
        ],
        [
            'id' => 'email-perusahaan',
            'label' => 'Alamat Email Resmi Perusahaan',
            'ikon' => 'mail',
            'nilai' => 'r.hidayat@qualifybimbel.com',
            'hint' => 'Email ini digunakan untuk pengiriman rekapsulasi audit mingguan & alert manajerial.',
            'tipe' => 'email',
            'wajib' => true,
        ],
        [
            'id' => 'telepon-pejabat',
            'label' => 'Nomor Telepon / WhatsApp Pejabat',
            'ikon' => 'phone',
            'nilai' => '+62 811-3456-7890',
            'hint' => null,
            'tipe' => 'tel',
            'wajib' => true,
        ],
        [
            'id' => 'domisili-utama',
            'label' => 'Alamat Domisili Utama',
            'ikon' => 'map-pin',
            'nilai' => 'Surabaya, Jawa Timur',
            'hint' => null,
            'tipe' => 'text',
            'wajib' => false,
        ],
    ];

    $otoritas = [
        [
            'label' => 'Peran Sistem (Role)',
            'nilai' => 'General Manager (Supervisi Operasional & Keuangan Cabang)',
            'detail' => 'Tingkat Manajerial Eksekutif (Tier-1)',
        ],
        [
            'label' => 'Cabang Pengawasan',
            'nilai' => 'Cabang Utama Surabaya Pusat (Holding Jatim)',
            'detail' => 'ID Unit Bisnis: BMB-SUB-01',
        ],
        [
            'label' => 'Tanggal Pengangkatan Otoritas',
            'nilai' => '1 Januari 2021 (4 Tahun 2 Bulan)',
            'detail' => 'SK Direksi: SK-DIR/2021/008-B',
        ],
        [
            'label' => 'Batasan Otoritas Finansial',
            'nilai' => 'Read-Only (Laporan SPP, Kas Cabang, Audit Penggajian)',
            'detail' => 'Hanya dapat mengekspor rekap bulanan; approval anggaran ditangani Super Admin.',
        ],
    ];
@endphp

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Header halaman --}}
    <header class="flex flex-wrap items-start justify-between gap-x-6 gap-y-4">
        <div class="max-w-2xl">
            <p class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                <span class="text-[10.5px] font-extrabold uppercase tracking-[0.14em] text-bronze">Otoritas Eksekutif Cabang</span>
                <span class="h-1 w-1 rounded-full bg-gold"></span>
                <span class="text-[11.5px] font-semibold text-muted">ID NIP: MGR-SUB-2021001</span>
            </p>
            <h1 class="mt-2 text-[26px] font-extrabold leading-[1.15] tracking-tight text-ink sm:text-[29px]">
                Profil Manajer &amp; Pengaturan Akun
            </h1>
            <p class="mt-2 text-[13px] leading-relaxed text-muted">
                Informasi kredensial eksekutif, identitas pejabat cabang, dan konfigurasi keamanan portal manajerial.
            </p>
        </div>

        <p class="flex shrink-0 items-center gap-2 rounded-full border border-line bg-white px-3.5 py-2 text-[11.5px] font-semibold text-muted">
            <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
            <span>Sesi Aktif Diperbarui<br><span class="font-medium text-bark-400">Hari ini, 09:41 WIB</span></span>
        </p>
    </header>

    {{-- Kartu identitas eksekutif --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
        <div class="h-1.5 bg-gold"></div>

        <div class="flex flex-col gap-6 p-5 sm:p-6 lg:flex-row lg:items-start">
            {{-- Foto & nama --}}
            <div class="flex min-w-0 flex-1 items-start gap-4 sm:gap-5">
                <div class="relative shrink-0">
                    <span class="grid h-[88px] w-[88px] place-items-center rounded-2xl bg-espresso text-[26px] font-extrabold tracking-tight text-gold">
                        RH
                    </span>
                    <button type="button"
                            class="absolute -bottom-1.5 -right-1.5 grid h-7 w-7 place-items-center rounded-full bg-gold text-espresso ring-2 ring-white transition hover:bg-palegold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-bronze"
                            aria-label="Ganti foto profil">
                        @include('layouts.partials.icon', ['name' => 'camera', 'class' => 'h-3.5 w-3.5'])
                    </button>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-[19px] font-extrabold leading-snug tracking-tight text-ink">Ir. Rahmat Hidayat, M.M.</h2>
                        <span class="rounded-md border border-line bg-cream px-2 py-[3px] text-[10px] font-bold text-muted">Eks. Level 1</span>
                    </div>

                    <p class="mt-1.5 flex items-start gap-1.5 text-[13px] font-semibold leading-snug text-bark-700">
                        @include('layouts.partials.icon', ['name' => 'briefcase', 'class' => 'mt-0.5 h-3.5 w-3.5 shrink-0 text-bronze'])
                        General Manager Operasional &amp; Keuangan
                    </p>

                    <div class="mt-2.5 space-y-1 text-[11.5px] leading-relaxed text-muted">
                        <p class="flex items-start gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'mt-0.5 h-3.5 w-3.5 shrink-0 text-bark-300'])
                            Jl. Pernuma No. 45-47, Embong Kaliasin, Genteng, Surabaya, Jawa Timur 60271
                        </p>
                        <p class="flex items-start gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'building', 'class' => 'mt-0.5 h-3.5 w-3.5 shrink-0 text-bark-300'])
                            Kantor Cabang Utama Surabaya Pusat
                        </p>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-sandgold px-2.5 py-1 text-[10.5px] font-bold text-bronze">
                            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                            Aktif Terverifikasi sejak 1 Jan 2021
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-line bg-cream px-2.5 py-1 text-[10.5px] font-bold text-muted">
                            @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-3.5 w-3.5'])
                            Read-Only Laporan &amp; Supervisi Otoritas
                        </span>
                    </div>
                </div>
            </div>

            {{-- Panel masa jabatan & sertifikasi --}}
            <dl class="grid shrink-0 grid-cols-1 gap-3 sm:grid-cols-2 lg:w-64">
                <div class="rounded-xl border border-line bg-cream px-4 py-3">
                    <dt class="flex items-center gap-1.5 text-[10.5px] font-bold uppercase tracking-[0.1em] text-muted">
                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5 text-bronze'])
                        Lama Menjabat
                    </dt>
                    <dd class="mt-1.5 text-[21px] font-extrabold leading-none tracking-tight tabular-nums text-ink">4 Thn 2 Bln</dd>
                </div>

                <div class="rounded-xl border border-line bg-cream px-4 py-3">
                    <dt class="flex items-center gap-1.5 text-[10.5px] font-bold uppercase tracking-[0.1em] text-muted">
                        @include('layouts.partials.icon', ['name' => 'award', 'class' => 'h-3.5 w-3.5 text-bronze'])
                        Status Sertifikasi Bimbel
                    </dt>
                    <dd class="mt-1.5 flex items-center gap-1.5 text-[13.5px] font-extrabold leading-snug text-ink">
                        @include('layouts.partials.icon', ['name' => 'star', 'class' => 'h-4 w-4 shrink-0 text-gold'])
                        ISO 9001:2021 Auditor
                    </dd>
                </div>
            </dl>
        </div>
    </section>

    {{-- Statistik binaan cabang --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($statistik as $item)
            <article class="rounded-xl border border-line bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">{{ $item['label'] }}</p>
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => $item['ikon'], 'class' => 'h-4 w-4'])
                    </span>
                </div>

                <p class="mt-3.5 flex items-baseline gap-1.5">
                    <span class="text-[25px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $item['nilai'] }}</span>
                    @if (! empty($item['satuan']))
                        <span class="text-[13px] font-bold text-bark-500">{{ $item['satuan'] }}</span>
                    @endif
                </p>

                <p class="mt-2.5 flex items-center gap-1.5 text-[11px] font-medium leading-snug text-bark-700">
                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                    {{ $item['catatan'] }}
                </p>
            </article>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- Kiri: data pribadi & kontak --}}
        <section class="rounded-2xl border border-line bg-white p-5 shadow-[0_1px_3px_rgba(20,20,20,0.05)] lg:col-span-2">
            <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-5 w-5'])
                    </span>
                    <div>
                        <h2 class="text-[17px] font-bold leading-snug text-ink">Data Pribadi &amp; Kontak</h2>
                        <p class="mt-0.5 max-w-md text-[12px] leading-relaxed text-muted">
                            Anda diizinkan memperbarui data korespondensi aktif untuk keperluan verifikasi SMS/WhatsApp OTP dan laporan darurat operasional cabang.
                        </p>
                    </div>
                </div>
                <span class="shrink-0 rounded-full bg-cream px-3 py-1.5 text-[10.5px] font-bold text-muted">Dapat Diubah Mandiri</span>
            </header>

            <form method="POST" action="{{ route('manager.profilSimpan') }}" class="mt-5">
                @csrf

                <div class="grid grid-cols-1 gap-x-5 gap-y-4 sm:grid-cols-2">
                    @foreach ($kontak as $field)
                        <div @class(['sm:col-span-2' => $field['id'] === 'domisili-utama'])>
                            <label for="{{ $field['id'] }}" class="text-[12px] font-bold text-ink">
                                {{ $field['label'] }}@if ($field['wajib'])<span class="text-ember-500"> *</span>@endif
                            </label>
                            <div class="relative mt-1.5">
                                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                                    @include('layouts.partials.icon', ['name' => $field['ikon'], 'class' => 'h-4 w-4'])
                                </span>
                                <input type="{{ $field['tipe'] }}" id="{{ $field['id'] }}" name="{{ $field['id'] }}"
                                       value="{{ $field['nilai'] }}" @if ($field['wajib']) required @endif
                                       class="w-full rounded-lg border border-line bg-white py-2.5 pl-9 pr-3 text-[12.5px] font-semibold text-ink transition hover:bg-sand focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                            </div>
                            @if ($field['hint'])
                                <p class="mt-1.5 text-[10.5px] leading-relaxed text-muted">{{ $field['hint'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                    <p class="flex items-center gap-1.5 text-[11px] font-medium text-muted">
                        @include('layouts.partials.icon', ['name' => 'info', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                        Perubahan tercatat di audit log
                    </p>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-gold px-4 py-3 text-[13px] font-bold text-espresso transition hover:bg-gold/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-bronze focus-visible:ring-offset-2">
                        @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-4 w-4'])
                        Simpan Perubahan Kontak
                    </button>
                </div>
            </form>
        </section>

        {{-- Kanan: otoritas & hak akses (terkunci) --}}
        <section class="flex flex-col rounded-2xl border border-line bg-white p-5 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-espresso text-gold">
                        @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'h-5 w-5'])
                    </span>
                    <h2 class="pt-1.5 text-[17px] font-bold leading-snug text-ink">Otoritas &amp; Hak Akses</h2>
                </div>
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-ember-50 px-3 py-1.5 text-[10.5px] font-bold text-ember-600">
                    @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-3.5 w-3.5'])
                    Terkunci
                </span>
            </header>

            <div class="mt-4 flex items-start gap-2.5 rounded-xl border border-line bg-cream px-3.5 py-3">
                @include('layouts.partials.icon', ['name' => 'info', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-bronze'])
                <p class="text-[11.5px] leading-relaxed text-muted">
                    <span class="font-bold text-ink">Kebijakan Keamanan Pusat:</span>
                    Peran manajer, otoritas cabang, dan status akun diproteksi secara hierarkis. Perubahan kewenangan hanya dapat diproses oleh
                    <span class="font-bold text-bark-700">Super Admin Kantor Pusat (Yayasan Qualify)</span>.
                </p>
            </div>

            <dl class="mt-4 divide-y divide-line border-t border-line">
                @foreach ($otoritas as $item)
                    <div class="flex items-start gap-3 py-3.5">
                        <div class="min-w-0 flex-1">
                            <dt class="text-[10px] font-bold uppercase tracking-[0.12em] text-muted">{{ $item['label'] }}</dt>
                            <dd class="mt-1 text-[12.5px] font-bold leading-snug text-ink">{{ $item['nilai'] }}</dd>
                            <p class="mt-1 text-[11px] leading-relaxed text-muted">{{ $item['detail'] }}</p>
                        </div>
                        <span class="mt-0.5 shrink-0 text-bark-300" aria-label="Data terkunci, tidak dapat diubah">
                            @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-4 w-4'])
                        </span>
                    </div>
                @endforeach
            </dl>

            <p class="mt-auto flex flex-wrap items-center gap-x-2 gap-y-1 border-t border-line pt-4 text-[11.5px] font-medium text-muted">
                Perlu mutasi wewenang?
                <a href="#" class="font-bold text-bronze transition hover:text-espresso focus-visible:underline">
                    Hubungi Biro HR Pusat
                </a>
            </p>
        </section>
    </div>
</div>
@endsection
