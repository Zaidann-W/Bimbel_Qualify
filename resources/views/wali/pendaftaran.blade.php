@extends('layouts.wali')

@section('title', 'Pendaftaran - Orang tua')

{{-- shortcut: Daftarkan Siswa Baru, Perbaiki & Unggah, Unggah Perbaikan, Lihat Detail, dan Konsultasi WhatsApp masih mock UI; sambungkan ke backend pendaftaran --}}
@section('content')
@php
    // [nama, ikon, meta, valid]
    $berkas = [
        ['Pas Foto 3×4 Latar Belakang Merah', 'image', 'pas_foto_anindya.jpg • 1.4 MB • Diunggah 28 Okt 2024', true],
        ['Scan Rapor Legalisir (Semester 1 – 4)', 'file-text', 'rapor_anindya.pdf • 4.8 MB • Diunggah 28 Okt 2024', true],
        ['Surat Keterangan Siswa Aktif / Kelulusan', 'file-text', 'ski_smp_anindya.pdf • 1.1 MB • Diunggah 28 Okt 2024', true],
    ];
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-1.5 rounded-md border border-gold bg-gold/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-espresso">
                @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3 w-3'])
                Penerimaan Siswa Baru TA 2025/2026
            </span>
            <h1 class="mt-2.5 text-[24px] font-bold tracking-tight text-bark-800">Status Pendaftaran Calon Siswa</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Pantau alur verifikasi berkas persyaratan, hasil tes diagnostik, dan status persetujuan pendaftaran putra-putri Anda secara transparan.</p>
        </div>
        <div class="flex shrink-0 flex-col items-end gap-2">
            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-bold text-bark-600 transition hover:bg-bark-50">
                @include('layouts.partials.icon', ['name' => 'help-circle', 'class' => 'h-3.5 w-3.5'])
                Bantuan Panitia PSB
            </button>
            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-500 px-3.5 py-2.5 text-[12.5px] font-bold text-white transition hover:bg-ember-600">
                @include('layouts.partials.icon', ['name' => 'user-plus', 'class' => 'h-4 w-4'])
                Daftarkan Siswa Baru
            </button>
        </div>
    </div>

    {{-- Status calon siswa --}}
    <section class="rounded-2xl border border-bark-100 bg-white px-5 py-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-bark-800 text-[15px] font-bold text-white">AP</div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-[15.5px] font-bold text-bark-800">Anindya Putri Rahayu</h2>
                        <span class="inline-flex items-center gap-1 rounded-full border border-gold bg-gold/10 px-2 py-0.5 text-[10.5px] font-bold text-espresso">
                            @include('layouts.partials.icon', ['name' => 'refresh', 'class' => 'h-3 w-3'])
                            Perlu Perbaikan Berkas
                        </span>
                        <span class="rounded-md border border-bark-200 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-bark-500">Gelombang 1</span>
                    </div>
                    <div class="mt-1.5 flex flex-wrap items-center gap-x-5 gap-y-1 text-[11.5px] text-bark-500">
                        <span>@include('layouts.partials.icon', ['name' => 'clipboard-form', 'class' => 'mr-1 inline h-3 w-3 align-[-2px]'])No. Registrasi: <span class="font-bold text-bark-700">REG-2025-0091</span></span>
                        <span>Jenjang: <span class="font-bold text-bark-700">SMA Kelas 10</span></span>
                        <span>Peminatan: <span class="font-bold text-bark-700">Intensif UTBK / FK</span></span>
                        <span>Tgl: <span class="font-bold text-bark-700">28 Okt 2024</span></span>
                    </div>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <button type="button" class="rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-bold text-bark-600 transition hover:bg-bark-50">Lihat Detail</button>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-500 px-3.5 py-2 text-[12px] font-bold text-white transition hover:bg-ember-600">
                    @include('layouts.partials.icon', ['name' => 'upload', 'class' => 'h-3.5 w-3.5'])
                    Perbaiki &amp; Unggah
                </button>
            </div>
        </div>
    </section>

    {{-- Audit dokumen --}}
    <section class="rounded-2xl border border-bark-100 bg-white">
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-5 py-4">
            <div>
                <h2 class="text-[15.5px] font-bold text-bark-800">Audit &amp; Verifikasi Dokumen Persyaratan</h2>
                <p class="mt-0.5 text-[11.5px] text-bark-400">Format didukung: PDF, JPG, PNG (Maks. 5MB per file)</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-600">3 Berkas Valid</span>
                <span class="rounded-full bg-gold/20 px-2.5 py-1 text-[11px] font-bold text-espresso">1 Butuh Perbaikan</span>
            </div>
        </header>

        <div class="divide-y divide-bark-100">

            {{-- Berkas bermasalah --}}
            <div class="flex flex-wrap items-center gap-4 border-l-4 border-l-gold bg-gold/10 px-5 py-4">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-5 w-5'])
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-[13.5px] font-bold text-bark-800">Scan Kartu Keluarga &amp; KTP Wali</h3>
                        <span class="rounded-full bg-gold/20 px-2 py-0.5 text-[10px] font-bold text-espresso">Perlu Perbaikan</span>
                    </div>
                    <p class="mt-1 max-w-xl text-[11.5px] leading-relaxed text-bark-500">Catatan verifikator: File buram / bagian NIK tidak terbaca jelas. Mohon unggah ulang dengan pencahayaan baik.</p>
                </div>
                <button type="button" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-gold px-3.5 py-2 text-[12px] font-bold text-espresso transition hover:brightness-95">
                    @include('layouts.partials.icon', ['name' => 'upload', 'class' => 'h-3.5 w-3.5'])
                    Unggah Perbaikan
                </button>
            </div>

            {{-- Berkas valid --}}
            @foreach ($berkas as [$nama, $ikon, $meta, $valid])
                <div class="flex flex-wrap items-center gap-4 px-5 py-3.5 transition hover:bg-bark-50/60">
                    <div @class([
                        'grid h-10 w-10 shrink-0 place-items-center rounded-xl',
                        'bg-sky-50 text-sky-500' => $ikon === 'image',
                        'bg-violet-50 text-violet-500' => $ikon === 'file-text',
                    ])>
                        @include('layouts.partials.icon', ['name' => $ikon, 'class' => 'h-5 w-5'])
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-[13.5px] font-bold text-bark-800">{{ $nama }}</h3>
                        <p class="mt-0.5 text-[11.5px] text-bark-400">{{ $meta }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-600">
                            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                            Valid
                        </span>
                        <button type="button" class="grid h-8 w-8 place-items-center rounded-lg border border-bark-200 bg-white text-bark-400 transition hover:bg-bark-50 hover:text-bark-600" aria-label="Lihat berkas {{ $nama }}">
                            @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4'])
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Bantuan --}}
    <section class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-bark-100 bg-white px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bark-800 text-white">
                @include('layouts.partials.icon', ['name' => 'headset', 'class' => 'h-5 w-5'])
            </div>
            <div class="min-w-0">
                <h3 class="text-[13.5px] font-bold text-bark-800">Butuh Bantuan Panitia PSB?</h3>
                <p class="mt-0.5 text-[11.5px] text-bark-400">Konselor akademik Dra. Sri Wahyuni, M.Psi siap membantu proses verifikasi pendaftaran anakanda.</p>
            </div>
        </div>
        <button type="button" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2.5 text-[12.5px] font-bold text-white transition hover:bg-emerald-600">
            @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-4 w-4'])
            Konsultasi WhatsApp
        </button>
    </section>
</div>
@endsection
