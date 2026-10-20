@extends('layouts.tentor')

@section('title', 'Materi Pembelajaran')

{{-- shortcut: filter jenjang/kategori, pencarian, dropdown mapel, dan tombol buka materi masih mock UI; sambungkan ke repository materi kalau modelnya sudah ada --}}
@section('content')
@php
    $stats = [
        ['Modul Tersedia', '38', 'Modul Lengkap', 'Sesuai Kurikulum Nasional Terintegrasi', 'book-open', 'bg-ember-50 text-ember-600'],
        ['Paket Latihan', '14', 'Paket Soal SNBT', 'Dilengkapi Kunci & Pembahasan', 'check-circle', 'bg-emerald-50 text-emerald-600'],
        ['Slide Presentasi', '26', 'Berkas Bahan Ajar', 'Siap Tayang di Smartboard / Layar', 'presentation', 'bg-bark-50 text-bark-500'],
    ];

    $jenjang = ['Semua Kelas', '10 SMA', '11 SMA', '12 SMA / SNBT', 'SMP / Persiapan'];
    $kategori = ['Semua (78)', 'Mapel Matematika Diampu', 'Bank Soal UTBK', 'Slide Presentasi Ajar'];

    // [chipKiri, chipKanan, chipKananTone, judul, deskripsi, rilis, tag, tagTone, cta, ctaTone]
    $materi = [
        ['12 SMA Intensif • Matematika', 'Modul PDF (Baca Online)', 'emerald',
         'Kalkulus Diferensial & Aplikasi Integral Luas/Volume',
         'Teorema dasar kalkulus, substitusi trigonometri, integral parsial cepat, serta strategi visual batas area.',
         'Rilis 5 Mar 2025', 'Kurikulum Riset', 'emerald', 'Buka Materi', 'bark'],

        ['12 SMA & Alumni • Lembaga Ajar', 'Modul PDF (Baca Online)', 'emerald',
         'Barisan, Deret Geometri Tak Hingga & Notasi Sigma',
         'Komposisi tipe soal pembiasaan kuantitatif analitis, pola rekursif, dan problem solving berjenjang.',
         'Rilis 2 Mar 2025', '30 Soal HOTS', 'ember', 'Buka Materi', 'bark'],

        ['SuperCamp SNBT • TPS', 'Modul PDF (Baca Online)', 'emerald',
         'Penalaran Kuantitatif: Trik Eliminasi Cepat & Logika Angka',
         'Strategi pengerjaan di bawah 60 detik per soal, estimasi pecahan desimal, dan penyaringan opsi jawaban.',
         'Rilis 28 Feb 2025', 'Target: 45/60 Soal', 'ember', 'Buka Materi', 'bark'],

        ['Fisika Saintek • Kelas Modul', 'Modul PDF (Baca Online)', 'emerald',
         'Fisika Modern, Radiasi Benda Hitam & Relativitas Khusus',
         'Dualisme gelombang-partikel, kontradiksi Lorentz, efek fotolistrik, dan relevansinya dengan teknologi masa kini.',
         'Rilis 24 Feb 2025', 'Garansi Kedalaman', 'ember', 'Buka Materi', 'bark'],

        ['Bank Soal • Paket 01–05', 'Modul PDF (Baca Online)', 'emerald',
         'Kompilasi Tryout Akbar SNBT: Matematika & Fisika',
         'Kumpulan naskah asli tryout regional bimbel terpadu, lengkap dengan tabel sebaran skor dan analisis butir.',
         'Rilis 20 Feb 2025', 'Bisa Edit Terbuka', 'ember', 'Buka Materi', 'bark'],

        ['Bahan Tayang • Smart Screen', 'Slide Presentasi', 'ember',
         'Slide Interaktif: Matriks & Transformasi Geometri',
         'Animasi vektor interaktif 40fps untuk simulasi perputaran dinding rotasi, distorsi matriks, dan komposisi transformasi.',
         'Rilis 18 Feb 2025', 'Touchscreen Ready', 'ember', 'Buka Slide', 'ember'],
    ];
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Bank Materi &amp; Bahan Ajar</h1>
            <p class="mt-1 text-[13px] text-bark-400">Repositori modul kurikulum riset dan bank tayang ajak kelas terverifikasi. Terstandarisasi untuk Semester Genap 2024/2025.</p>
        </div>
        <div class="flex max-w-xs items-start gap-2.5 rounded-xl border border-ember-200 bg-ember-50 px-4 py-3">
            @include('layouts.partials.icon', ['name' => 'info', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
            <div>
                <p class="text-[12.5px] font-bold text-bark-700">Mode Baca Langsung</p>
                <p class="mt-0.5 text-[11.5px] leading-relaxed text-bark-500">Bahan hanya dapat dibaca &amp; ditayangkan di kelas</p>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($stats as [$label, $value, $unit, $sub, $icon, $tone])
            <div class="relative overflow-hidden rounded-2xl border border-bark-100 bg-white p-5">
                <div class="absolute inset-x-0 top-0 h-1 bg-ember-400"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="text-[28px] font-bold leading-none tracking-tight text-bark-800">{{ $value }}</span>
                            <span class="text-[12px] font-medium text-bark-400">{{ $unit }}</span>
                        </div>
                    </div>
                    <div @class(['grid h-9 w-9 place-items-center rounded-xl', $tone])>
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-[18px] w-[18px]'])
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-bark-400">{{ $sub }}</p>
            </div>
        @endforeach
    </div>

    {{-- Pencarian + filter --}}
    <section class="rounded-2xl border border-bark-100 bg-white p-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-[220px] flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                <input type="search" placeholder="Cari judul modul, bab, atau materi…"
                       class="w-full rounded-lg border border-bark-200 bg-white py-2.5 pl-9 pr-3 text-[13px] text-bark-800 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
            </div>
            <select class="rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[12.5px] font-medium text-bark-600 focus:border-ember-300 focus:outline-none">
                <option>Matematika Saintek</option>
                <option>Fisika Saintek</option>
                <option>Biologi Saintek</option>
            </select>
            <select class="rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[12.5px] font-medium text-bark-600 focus:border-ember-300 focus:outline-none">
                <option>Kelas 12 SMA &amp; Alumni / UTBK</option>
                <option>Kelas 10 SMA</option>
                <option>Kelas 11 SMA</option>
                <option>SMP / Persiapan</option>
            </select>
        </div>

        <div class="mt-4 space-y-2.5">
            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-1 text-[10px] font-bold uppercase tracking-wide text-bark-400">Jenjang Kelas</span>
                @foreach ($jenjang as $i => $j)
                    @php $active = $j === 'Semua Kelas'; @endphp
                    <button type="button"
                            @class([
                                'rounded-lg px-3 py-1.5 text-[12px] font-semibold transition',
                                'bg-bark-800 text-white' => $active,
                                'border border-ember-300 bg-ember-50 text-ember-700' => $j === '12 SMA / SNBT',
                                'border border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => ! $active && $j !== '12 SMA / SNBT',
                            ])>{{ $j }}</button>
                @endforeach
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-1 text-[10px] font-bold uppercase tracking-wide text-bark-400">Kategori</span>
                @foreach ($kategori as $k)
                    @php $active = str_starts_with($k, 'Semua'); @endphp
                    <button type="button"
                            @class([
                                'rounded-lg px-3 py-1.5 text-[12px] font-semibold transition',
                                'bg-bark-800 text-white' => $active,
                                'border border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => ! $active,
                            ])>{{ $k }}</button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Grid materi --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($materi as [$chipKiri, $chipKanan, $chipTone, $judul, $deskripsi, $rilis, $tag, $tagTone, $cta, $ctaTone])
            <article class="flex flex-col rounded-2xl border border-bark-100 bg-white p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="rounded-md bg-bark-100 px-2 py-1 text-[10px] font-bold text-bark-500">{{ $chipKiri }}</span>
                    <span @class([
                        'rounded-md px-2 py-1 text-[10px] font-bold',
                        'bg-emerald-50 text-emerald-600' => $chipTone === 'emerald',
                        'bg-ember-100 text-ember-700' => $chipTone === 'ember',
                    ])>{{ $chipKanan }}</span>
                </div>

                <h3 class="mt-3 text-[14.5px] font-bold leading-snug text-bark-800">{{ $judul }}</h3>
                <p class="mt-1.5 line-clamp-2 text-[12px] leading-relaxed text-bark-400">{{ $deskripsi }}</p>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                    <p class="inline-flex items-center gap-1.5 text-[11px] text-bark-400">
                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5'])
                        {{ $rilis }}
                    </p>
                    <span @class([
                        'rounded-md px-2 py-0.5 text-[10px] font-bold',
                        'bg-emerald-50 text-emerald-600' => $tagTone === 'emerald',
                        'bg-ember-100 text-ember-700' => $tagTone === 'ember',
                    ])>{{ $tag }}</span>
                </div>

                <button type="button"
                        @class([
                            'mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-[12px] font-bold transition',
                            'bg-bark-800 text-white hover:bg-bark-900' => $ctaTone === 'bark',
                            'bg-ember-500 text-white hover:bg-ember-600' => $ctaTone === 'ember',
                        ])>
                    @include('layouts.partials.icon', ['name' => $ctaTone === 'ember' ? 'presentation' : 'book-open', 'class' => 'h-3.5 w-3.5'])
                    {{ $cta }}
                </button>
            </article>
        @endforeach
    </div>

    {{-- Usulan kurikulum --}}
    <section class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-bark-100 bg-white px-5 py-4">
        <div class="flex items-start gap-3">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-ember-50 text-ember-500">
                @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-5 w-5'])
            </div>
            <div>
                <h2 class="text-[14px] font-bold text-bark-800">Perlu usulan penambahan suplemen materi?</h2>
                <p class="mt-0.5 text-[12px] text-bark-400">Sebagai tentor terverifikasi, Anda dapat mengajukan usulan naskah soal tambahan atau koreksi redaksional modul literasi.</p>
            </div>
        </div>
        <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-bark-200 bg-white px-4 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
            @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4'])
            Ajukan ke Tim Kurikulum
        </button>
    </section>
</div>
@endsection
