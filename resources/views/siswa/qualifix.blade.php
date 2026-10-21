@extends('layouts.siswa')

@section('title', 'Qualifix Siswa')

{{-- shortcut: tab filter, search, dan tombol aksi kartu masih mock UI; sambungkan ke backend Q&A --}}
@section('content')
@php
    $stats = [
        ['Total Diajukan', '8', 'Soal', 'file', 'bg-bark-100 text-bark-600', false],
        ['Menunggu Jawaban', '1', 'Pertanyaan', 'clock', 'bg-ember-100 text-ember-600', true],
        ['Sudah Dijawab', '5', 'Tersedia', 'message-circle', 'bg-bark-100 text-bark-600', false],
        ['Selesai / Dipahami', '2', 'Arsip', 'check-circle', 'bg-emerald-50 text-emerald-600', false],
    ];

    $tabs = [
        ['Semua', 8, true],
        ['Menunggu', 1, false],
        ['Dijawab', 5, false],
        ['Selesai', 2, false],
    ];

    // [ikonMapel, mapel, bab, status, judul, deskripsi, awalAvatar, nama, waktu, tombol]
    $soal = [
        ['sigma', 'Matematika Saintek', 'Bab Barisan & Deret', 'Sudah Dijawab',
         'Soal No. 14 Try Out TPS Penalaran Kuantitatif – Barisan Geometri Bertingkat',
         'Kak Hendra, untuk soal tipe nomor 14 mencari suku ke-10 dari deret bersusun ini, saya bingung menentukan beda rasio bertingkatnya apakah menggunakan eliminasi persamaan sekaligus atau rumus cepat deret aritmatika tingkat dua…',
         'HW', 'Kak Hendra Wijaya, M. Sc', '2 jam lalu', 'Lihat Coretan Pembahasan'],
        ['beaker', 'Kimia Saintek', 'Kesetimbangan & Garam', 'Menunggu Jawaban',
         'Soal Stokiometri Laju Buffer Asam Basa pH Campuran',
         'Dalam soal no. 5 percobaan titrasi asam lemah dengan basa kuat, saya bingung menentukan titik ekuivalen sebelum dan sesudah penambahan indikator…',
         'NS', 'Nadia Syafitri', '3 menit lalu', 'Lihat Detail Soal'],
        ['wind', 'Fisika Saintek', 'Bab Listrik Statis', 'Selesai / Dipahami',
         'Analisis Gaya Gerak Listrik Induksi via Hukum Lenz',
         'Kak, untuk rangkaian batang konduktor ini saya sudah paham arah arus induksinya, tapi waktu menghitung besar GFLnya masih bingung memakai kaidah kanan tangan yang mana…',
         'SA', 'Kak Sarah Anastasia, S.Pd', '1 hari lalu', 'Lihat Coretan Pembahasan'],
    ];
@endphp

<div class="space-y-5">

    {{-- Badge row --}}
    <div class="flex flex-wrap items-center gap-2.5">
        <span class="rounded-md bg-espresso px-2.5 py-1 text-[11px] font-bold text-white">Asisten 1-on-1</span>
        <span class="text-[12px] font-medium text-bark-400">Target Lulus UTBK SNBT 2025</span>
    </div>

    {{-- Header + actions --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[26px] font-bold tracking-tight text-bark-800">Qualifix</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Tanyakan kesulitan soal PR, tugas sekolah, atau drilling UTBK ke Master Tutor Qualify. Pembahasan dilengkapi coretan rumus matematika, trik eliminasi kilat, dan feedback asistensi personal.</p>
        </div>
        <div class="flex shrink-0 flex-wrap gap-2">
            <button type="button" data-qualifix-panduan-open
                    class="inline-flex items-center gap-2 rounded-lg border border-bark-200 bg-white px-3.5 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'h-4 w-4'])
                Panduan Tanya Soal
            </button>
            <button type="button" data-qualifix-ajukan-open
                    class="inline-flex items-center gap-2 rounded-lg bg-ember-500 px-4 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-ember-600">
                @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4'])
                Ajukan Pertanyaan PR Baru
            </button>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as [$label, $value, $unit, $icon, $tone, $active])
            <div @class([
                'rounded-2xl border bg-white p-4',
                'border-ember-300 ring-1 ring-ember-100' => $active,
                'border-bark-100' => ! $active,
            ])>
                <div class="flex items-start justify-between">
                    <div>
                        <p @class([
                            'text-[11px] font-semibold uppercase tracking-wide',
                            'text-ember-600' => $active,
                            'text-bark-400' => ! $active,
                        ])>{{ $label }}</p>
                        <div class="mt-1.5 flex items-baseline gap-1.5">
                            <span class="text-[26px] font-bold leading-none tracking-tight text-bark-800">{{ $value }}</span>
                            <span class="text-[11.5px] font-medium text-bark-400">{{ $unit }}</span>
                        </div>
                    </div>
                    <div @class(['grid h-9 w-9 place-items-center rounded-xl', $tone])>
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-[18px] w-[18px]'])
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Daftar pertanyaan --}}
    <section class="overflow-hidden rounded-2xl border border-bark-100 bg-white">

        {{-- Tabs + search --}}
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-5 py-4">
            <div class="flex flex-wrap gap-1.5">
                @foreach ($tabs as [$label, $count, $active])
                    <button type="button" aria-pressed="{{ $active ? 'true' : 'false' }}"
                            @class([
                                'flex items-center gap-1 rounded-lg px-3.5 py-2 text-[12.5px] font-semibold transition',
                                'bg-bark-800 text-white' => $active,
                                'border border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => ! $active,
                            ])>
                        {{ $label }}({{ $count }})
                    </button>
                @endforeach
            </div>
            <div class="relative min-w-[220px] flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                <input type="search" placeholder="Cari judul soal, topik atau bab…"
                       class="w-full rounded-lg border border-bark-200 bg-white py-2.5 pl-9 pr-3 text-[13px] text-bark-800 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
            </div>
        </header>

        {{-- Cards --}}
        <div class="divide-y divide-bark-100">
            @foreach ($soal as [$ikon, $mapel, $bab, $status, $judul, $deskripsi, $awal, $nama, $waktu, $tombol])
                <article class="border-l-4 border-l-ember-400 px-5 py-4 transition hover:bg-bark-50/60">

                    {{-- Meta --}}
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2 text-[11.5px]">
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-ember-50 px-2 py-1 font-bold text-ember-700 ring-1 ring-ember-200">
                                @include('layouts.partials.icon', ['name' => $ikon, 'class' => 'h-3.5 w-3.5'])
                                {{ $mapel }}
                            </span>
                            <span class="text-bark-400">• {{ $bab }}</span>
                        </div>
                        <span @class([
                            'shrink-0 rounded-full px-2.5 py-1 text-[10.5px] font-bold',
                            'bg-ember-100 text-ember-700' => $status === 'Menunggu Jawaban',
                            'bg-bark-100 text-bark-600' => $status === 'Sudah Dijawab',
                            'bg-emerald-50 text-emerald-600' => $status === 'Selesai / Dipahami',
                        ])>{{ $status }}</span>
                    </div>

                    {{-- Judul + deskripsi --}}
                    <h3 class="mt-2.5 text-[15px] font-bold leading-snug text-bark-800">{{ $judul }}</h3>
                    <p class="mt-1.5 line-clamp-2 text-[12.5px] leading-relaxed text-bark-500">{{ $deskripsi }}</p>

                    {{-- Footer --}}
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div @class([
                                'grid h-8 w-8 shrink-0 place-items-center rounded-full text-[11px] font-bold',
                                'bg-ember-100 text-ember-700' => $status === 'Menunggu Jawaban',
                                'bg-bark-800 text-white' => $status !== 'Menunggu Jawaban',
                            ])>{{ $awal }}</div>
                            <div class="leading-tight">
                                <p class="text-[12.5px] font-semibold text-bark-700">{{ $nama }}</p>
                                <p class="text-[11px] text-bark-400">{{ $waktu }}</p>
                            </div>
                        </div>
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-bold text-bark-600 transition hover:bg-bark-50">
                            {{ $tombol }}
                            @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>

<x-qualifix-panduan-modal />
<x-qualifix-ajukan-modal />
@endsection
