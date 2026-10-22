@extends('layouts.wali')

@section('title', 'Profil Saya - Orang tua')

{{-- shortcut: Simpan Perubahan, Ubah Sandi, toggle notifikasi, dan Hubungi Helpdesk masih mock UI; sambungkan ke backend akun wali --}}
@section('content')
@php
    // [inisial, nama, sub, chip teks, chip gaya, chip ikon]
    $anak = [
        ['AP', 'Aisyah Putri Azzahra', 'Kelas 12 IPA • SPP Lunas', 'Siswa Aktif', 'emerald', 'check-circle'],
        ['AR', 'Anindya Putri Rahayu', 'Kelas 10 SMA • Calon Siswa', 'Cek Berkas', 'gold', 'chevron-right'],
    ];
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-gold-600">
                @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3 w-3'])
                Portal Orang Tua • Akun Resmi Wali
            </span>
            <h1 class="mt-2 text-[24px] font-bold tracking-tight text-bark-800">Profil Saya (Wali Murid)</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Kelola data kontak orang tua dan pantau status keterhubungan akun putra-putri Anda dalam satu tampilan terpadu.</p>
        </div>
        <p class="shrink-0 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[11.5px] text-bark-400">
            ID Wali: <span class="font-bold text-bark-700">WAL-2024-0419</span>
        </p>
    </div>

    {{-- Identitas --}}
    <section class="rounded-2xl border border-bark-100 bg-white px-5 py-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3.5">
                <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-espresso text-[17px] font-bold text-white">HG</div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-[16.5px] font-bold text-bark-800">Ir. Hendra Gunawan</h2>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10.5px] font-bold text-emerald-600">
                            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3 w-3'])
                            Akun Wali Terverifikasi
                        </span>
                    </div>
                    <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11.5px] text-bark-500">
                        <span class="inline-flex items-center gap-1.5">@include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-3.5 w-3.5'])Cabang Menteng</span>
                        <span class="inline-flex items-center gap-1.5">@include('layouts.partials.icon', ['name' => 'phone', 'class' => 'h-3.5 w-3.5'])+62 812-4455-8891</span>
                        <span class="inline-flex items-center gap-1.5">@include('layouts.partials.icon', ['name' => 'mail', 'class' => 'h-3.5 w-3.5'])hendra.gunawan@email.com</span>
                    </div>
                </div>
            </div>
            <span class="inline-flex shrink-0 items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[11.5px] font-bold text-emerald-600">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Status: Aktif Berlangganan
            </span>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">

        {{-- Kiri: data diri --}}
        <section class="self-start rounded-2xl border border-bark-100 bg-white px-5 py-4">
            <header class="flex items-start justify-between gap-3">
                <div class="flex min-w-0 items-start gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gold/20 text-espresso">
                        @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-[14.5px] font-bold text-bark-800">Data Diri &amp; Kontak Utama</h2>
                        <p class="mt-0.5 max-w-xs text-[11.5px] leading-relaxed text-bark-400">Nomor aktif untuk penempatan presensi dan laporan belajar</p>
                    </div>
                </div>
                <p class="shrink-0 text-[10.5px] text-bark-400">Update 12 Feb 2025</p>
            </header>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="block">
                    <span class="text-[11px] font-bold text-bark-500">Nama Lengkap Wali</span>
                    <input type="text" value="Ir. Hendra Gunawan" readonly class="mt-1 w-full rounded-lg border border-bark-200 bg-bark-50/60 px-3 py-2 text-[13px] text-bark-700 focus:outline-none">
                </label>
                <label class="block">
                    <span class="text-[11px] font-bold text-bark-500">Hubungan Keluarga</span>
                    <input type="text" value="Ayah Kandung" readonly class="mt-1 w-full rounded-lg border border-bark-200 bg-bark-50/60 px-3 py-2 text-[13px] text-bark-700 focus:outline-none">
                </label>
                <label class="block">
                    <span class="flex items-center gap-1.5 text-[11px] font-bold text-bark-500">Nomor WhatsApp <span class="rounded bg-gold/25 px-1.5 py-[1px] text-[9.5px] font-bold text-espresso">Utama</span></span>
                    <span class="relative mt-1 block">
                        @include('layouts.partials.icon', ['name' => 'phone', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-bark-300'])
                        <input type="text" value="+62 812-4455-8891" readonly class="w-full rounded-lg border border-bark-200 bg-bark-50/60 py-2 pl-9 pr-3 text-[13px] text-bark-700 focus:outline-none">
                    </span>
                </label>
                <label class="block">
                    <span class="text-[11px] font-bold text-bark-500">Alamat Email</span>
                    <span class="relative mt-1 block">
                        @include('layouts.partials.icon', ['name' => 'mail', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-bark-300'])
                        <input type="text" value="hendra.gunawan@email.com" readonly class="w-full rounded-lg border border-bark-200 bg-bark-50/60 py-2 pl-9 pr-3 text-[13px] text-bark-700 focus:outline-none">
                    </span>
                </label>
                <label class="block sm:col-span-2">
                    <span class="text-[11px] font-bold text-bark-500">Alamat Tempat Tinggal</span>
                    <span class="relative mt-1 block">
                        @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-bark-300'])
                        <input type="text" value="Jl. Gandaria Tengah III No. 14, Kebayoran Baru, Jakarta Selatan" readonly class="w-full rounded-lg border border-bark-200 bg-bark-50/60 py-2 pl-9 pr-3 text-[13px] text-bark-700 focus:outline-none">
                    </span>
                </label>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-espresso px-4 py-2.5 text-[12.5px] font-bold text-white transition hover:bg-bark-900">
                    @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-4 w-4'])
                    Simpan Perubahan
                </button>
            </div>
        </section>

        {{-- Kanan: anak, sandi, helpdesk --}}
        <div class="space-y-5">

            <section class="rounded-2xl border border-bark-100 bg-white px-4 py-4">
                <header class="flex items-center justify-between gap-2">
                    <h2 class="text-[13.5px] font-bold text-bark-800">Putra-Putri Terhubung</h2>
                    <span class="rounded-full bg-bark-100 px-2.5 py-1 text-[10.5px] font-bold text-bark-500">2 Siswa</span>
                </header>

                <div class="mt-3 space-y-2">
                    @foreach ($anak as [$inisial, $nama, $sub, $chip, $gaya, $ikon])
                        <div class="flex items-center gap-2.5 rounded-xl border border-bark-100 bg-white px-3 py-2.5 transition hover:bg-bark-50">
                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-bark-800 text-[11.5px] font-bold text-white">{{ $inisial }}</div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[12.5px] font-bold text-bark-800">{{ $nama }}</p>
                                <p class="mt-0.5 truncate text-[10.5px] text-bark-400">{{ $sub }}</p>
                            </div>
                            <span @class([
                                'inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-1 text-[10px] font-bold',
                                'bg-emerald-50 text-emerald-600' => $gaya === 'emerald',
                                'bg-gold/25 text-espresso' => $gaya === 'gold',
                            ])>
                                @include('layouts.partials.icon', ['name' => $ikon, 'class' => 'h-3 w-3'])
                                {{ $chip }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="flex items-center justify-between gap-3 rounded-2xl border border-bark-100 bg-white px-4 py-4">
                <div class="flex min-w-0 items-start gap-2.5">
                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-bark-100 text-bark-500">
                        @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-4 w-4'])
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-[12.5px] font-bold text-bark-800">Kata Sandi Akun</h3>
                        <p class="mt-0.5 text-[11px] text-bark-400">•••••••••••••••••• <span class="font-semibold text-bark-500">• Terakhir diubah 30 hari lalu</span></p>
                    </div>
                </div>
                <button type="button" class="shrink-0 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[11.5px] font-bold text-bark-600 transition hover:bg-bark-50">Ubah Sandi</button>
            </section>

            <p class="flex flex-wrap items-center justify-between gap-2 text-[11.5px] text-bark-400">
                Ada kendala data profil?
                <a href="#" class="inline-flex items-center gap-1.5 font-bold text-emerald-600 hover:text-emerald-700">
                    @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-3.5 w-3.5'])
                    Hubungi Helpdesk
                </a>
            </p>
        </div>
    </div>

    {{-- Notifikasi --}}
    <section class="flex items-center justify-between gap-4 rounded-2xl border border-bark-100 bg-white px-5 py-4">
        <div class="flex min-w-0 items-center gap-3">
            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gold/25 text-espresso">
                @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-4 w-4'])
            </div>
            <div class="min-w-0">
                <h3 class="text-[13px] font-bold text-bark-800">Notifikasi Otomatis WhatsApp</h3>
                <p class="mt-0.5 text-[11.5px] text-bark-400">Presensi tap-in/out harian dan ringkasan nilai modul belajar</p>
            </div>
        </div>
        <button type="button" role="switch" aria-checked="true" aria-label="Notifikasi otomatis WhatsApp" class="relative h-6 w-11 shrink-0 rounded-full bg-gold transition">
            <span class="absolute right-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow"></span>
        </button>
    </section>
</div>
@endsection
