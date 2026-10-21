@extends('layouts.wali')

@section('title', 'Informasi Anak - Orang Tua')

{{-- shortcut: tombol beralih profil anak dan Hubungi/Konsultasi Wali Kelas masih mock UI; sambungkan ke backend portal wali --}}
@section('content')
@php
    $rombel = [
        ['Nama Kelas (Rombel)', 'Rombel 12 IPA Super Intensif', 'Maksimal 12 siswa per sesi'],
        ['Kurikulum Bimbingan', 'Kurikulum Merdeka + UTBK', 'Fokus Pemantapan Skolastik &amp; LSN'],
        ['Periode Belajar', 'Januari – Mei 2025', 'Persiapan SNBT 2025'],
    ];

    // [hari, mapel, tentor, jam, sesi, emas]
    $jadwal = [
        ['Sen', 'Matematika Saintek', 'Kak Hendra Wijaya', '15.30 – 17.30', 'Sesi Bimbel (90 Menit)', false],
        ['Rab', 'Fisika Intensif', 'Kak Sarah Anastasia', '16.00 – 17.30', 'Sesi Bimbel (90 Menit)', false],
        ['Kam', 'Kimia Saintek', 'Dr. Hartono', '15.30 – 17.30', 'Sesi Bimbel (90 Menit)', false],
        ['Sab', 'Simulasi CBT UTBK', 'Latihan Ujian Mandiri', '08.30 – 11.30', 'Simulasi Nasional', true],
    ];
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[24px] font-bold tracking-tight text-bark-800">Informasi Akademik &amp; Profil Anak</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Pantau jadwal belajar, profil bimbingan, dan evaluasi capaian belajar anak secara ringkas dan mudah dipahami.</p>
        </div>
        <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-bark-200 bg-white px-3 py-1.5 text-[11.5px] font-bold text-bark-600">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
            Semester Genap 2024/2025 <span class="text-bark-400">(Aktif)</span>
        </span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">

        {{-- Kiri --}}
        <div class="space-y-6">

            {{-- Profil anak --}}
            <section class="relative rounded-2xl border border-ember-300 bg-white px-5 py-5 ring-1 ring-ember-100">
                <span class="absolute -top-3 right-4 inline-flex items-center gap-1.5 rounded-full bg-gold px-2.5 py-1 text-[9.5px] font-bold uppercase tracking-wide text-espresso">
                    @include('layouts.partials.icon', ['name' => 'star', 'class' => 'h-3 w-3'])
                    Profil Sedang Ditampilkan
                </span>

                <div class="flex items-start gap-4">
                    <div class="relative shrink-0">
                        <div class="grid h-14 w-14 place-items-center rounded-xl bg-ember-100 text-[18px] font-bold text-ember-700">AP</div>
                        <span class="absolute -bottom-1.5 -right-1.5 grid h-5 w-5 place-items-center rounded-full bg-emerald-500 text-white ring-2 ring-white">
                            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3 w-3'])
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 text-[11.5px]">
                            <span class="rounded-md bg-gold px-2 py-[3px] text-[10px] font-bold text-espresso">Siswa Aktif</span>
                            <span class="text-bark-400">NISN: <span class="font-semibold text-bark-600">0098234121</span></span>
                            <span class="text-bark-400">ID: <span class="font-semibold text-bark-600">QLF-24101</span></span>
                        </div>
                        <h2 class="mt-1.5 text-[17px] font-bold text-bark-800">Aisyah Putri Azzahra</h2>
                        <p class="mt-0.5 text-[12.5px] font-semibold text-ember-600">Target UTBK – SNBT: Kedokteran Umum</p>
                        <div class="mt-2.5 flex flex-wrap items-center gap-x-5 gap-y-1 text-[12px] text-bark-500">
                            <p><span class="font-semibold text-bark-700">Asal Sekolah:</span> SMAN 8 Jakarta</p>
                            <p><span class="font-semibold text-bark-700">Kelas:</span> XII IPA</p>
                            <p><span class="font-semibold text-bark-700">Ruang:</span> Eureka (Lt. 2)</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Program & rombel --}}
            <section class="rounded-2xl border border-bark-100 bg-white px-5 py-4">
                <header class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gold/30 text-espresso">
                            @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'h-[18px] w-[18px]'])
                        </div>
                        <div>
                            <h2 class="text-[15px] font-bold text-bark-800">Program &amp; Rombongan Belajar</h2>
                            <p class="text-[11.5px] text-bark-400">Informasi kelas dan target belajar semester ini</p>
                        </div>
                    </div>
                    <span class="rounded-md bg-bark-100 px-2.5 py-1 text-[10.5px] font-bold text-bark-500">Tahun Ajaran 2024/2025</span>
                </header>

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($rombel as [$label, $nilai, $sub])
                        <div class="rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
                            <p class="text-[9.5px] font-bold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                            <p class="mt-1.5 text-[14.5px] font-bold leading-snug text-bark-800">{{ $nilai }}</p>
                            <p class="mt-1 text-[11.5px] text-bark-500">{!! $sub !!}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Wali kelas --}}
            <section class="rounded-2xl border border-bark-100 bg-white px-5 py-4">
                <header class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ember-50 text-ember-600">
                            @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-[18px] w-[18px]'])
                        </div>
                        <div>
                            <h2 class="text-[15px] font-bold text-bark-800">Wali Kelas Akademik</h2>
                            <p class="text-[11.5px] text-bark-400">Pendamping belajar dan konsultan evaluasi berkala</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10.5px] font-bold text-emerald-600">
                        @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                        Tentor Aktif
                    </span>
                </header>

                <div class="mt-4 flex items-center gap-3 rounded-xl border border-bark-100 bg-white px-4 py-3">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-bark-800 text-[13px] font-bold text-white">HW</div>
                    <div class="min-w-0">
                        <p class="text-[14.5px] font-bold text-bark-800">Kak Hendra Wijaya, M. Sc</p>
                        <p class="mt-0.5 text-[12px] font-semibold text-ember-600">Master Trainer Penalaran Matematika &amp; Fisika</p>
                        <p class="mt-0.5 text-[11.5px] text-bark-400">Alumnus S2 Matematika ITB • NIP. 740812</p>
                    </div>
                </div>

                <div class="mt-3 rounded-xl border border-gold/50 bg-gold/10 px-4 py-3">
                    <p class="inline-flex items-center gap-1.5 text-[9.5px] font-bold uppercase tracking-[0.12em] text-ember-600">
                        @include('layouts.partials.icon', ['name' => 'megaphone', 'class' => 'h-3 w-3'])
                        Pesan Motivasi untuk Orang Tua
                    </p>
                    <p class="mt-1.5 text-[12.5px] italic leading-relaxed text-bark-600">"Aisyah menunjukkan semangat belajar yang sangat baik dan disiplin tinggi dalam sesi bimbingan. Pemahaman konsep dasarnya solid. Dukungan suasana belajar yang nyaman di rumah akan sangat membantu kestabilan belajarnya menjelang ujian."</p>
                </div>

                <button type="button" class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg bg-gold px-4 py-3 text-[13px] font-bold text-espresso transition hover:brightness-95">
                    @include('layouts.partials.icon', ['name' => 'phone', 'class' => 'h-4 w-4'])
                    Hubungi / Konsultasi dengan Wali Kelas
                </button>
            </section>
        </div>

        {{-- Kanan --}}
        <div class="space-y-6">

            {{-- Pengalih profil --}}
            <section class="self-start rounded-2xl border border-bark-100 bg-white px-4 py-4">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[13px] font-bold text-bark-800">Calon Siswa</p>
                    <span class="rounded-md bg-gold/30 px-2 py-[3px] text-[10px] font-bold text-espresso">+1 Anak Lain</span>
                </div>
                <div class="mt-3 flex items-center gap-3 rounded-xl border border-bark-100 bg-bark-50/60 px-3 py-2.5">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-bark-200 text-[11.5px] font-bold text-bark-600">AR</div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <p class="truncate text-[13px] font-bold text-bark-800">Anindya Putri Rahayu</p>
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-rose-500"></span>
                        </div>
                        <p class="truncate text-[11px] text-bark-400">Penerimaan Internal (STMB Tegal)</p>
                    </div>
                </div>
                <button type="button" class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                    Beralih ke Profil Anindya
                    @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                </button>
            </section>

            {{-- Jadwal mingguan --}}
            <section class="self-start rounded-2xl border border-bark-100 bg-white px-4 py-4">
                <header class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ember-50 text-ember-600">
                            @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-[18px] w-[18px]'])
                        </div>
                        <div>
                            <h2 class="text-[14.5px] font-bold text-bark-800">Jadwal Belajar Mingguan</h2>
                            <p class="text-[11px] text-bark-400">4 sesi terjadwal per minggu</p>
                        </div>
                    </div>
                    <span class="rounded-md bg-gold/30 px-2 py-1 text-[10px] font-bold text-espresso">Kelas Eureka</span>
                </header>

                <ul class="mt-3 divide-y divide-bark-100">
                    @foreach ($jadwal as [$hari, $mapel, $tentor, $jam, $sesi, $emas])
                        <li @class(['flex items-center gap-3 px-1 py-3', 'rounded-xl bg-gold/10 px-2' => $emas])>
                            <div @class([
                                'grid h-11 w-11 shrink-0 place-items-center rounded-lg text-center leading-tight',
                                'bg-gold text-espresso' => $emas,
                                'bg-bark-800 text-white' => ! $emas,
                            ])>
                                <span class="text-[8px] font-bold uppercase opacity-70">Hari</span>
                                <span class="text-[12px] font-bold uppercase">{{ $hari }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13px] font-bold text-bark-800">{{ $mapel }}</p>
                                <p class="mt-0.5 text-[11px] text-bark-400">{{ $tentor }}</p>
                            </div>
                            <div class="shrink-0 text-right leading-tight">
                                <p class="text-[12.5px] font-bold text-bark-800">{{ $jam }}</p>
                                <p class="mt-0.5 text-[10.5px] text-bark-400">{{ $sesi }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection
