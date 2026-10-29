@extends('layouts.tentor')

@section('title', 'Dashboard Overview — Tentor')

@section('content')
@php
    $user = auth()->user();
@endphp

    {{-- Top header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Selamat datang, {{ $user->name }}! 👋</h1>
            <p class="text-[13px] text-bark-400">Pantau jadwal kelas hari ini, input kehadiran siswa, dan jawab pertanyaan Klinik PR.</p>
        </div>
        <div class="flex items-center gap-3 rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
            <div class="flex flex-col items-center gap-1">
                <span class="text-[11px] font-bold uppercase tracking-wide text-bark-400">Selasa</span>
                <span class="text-[13px] font-bold text-bark-800">11 Maret 2025</span>
            </div>
            <div class="w-px h-5 bg-bark-200 mx-1"></div>
            <span class="text-[13px] font-bold text-bark-800">14:45 WIB</span>
            <span class="text-[11px] font-medium text-bark-500">• Sesi Sore: Semua Mulai</span>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 mb-6">
        {{-- Kelas Hari Ini --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-5">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Kelas Hari Ini</p>
                    <p class="mt-1 text-[30px] font-bold leading-none tracking-tight text-bark-800">2 Sesi</p>
                    <p class="mt-1 text-[11px] text-bark-400">Sore 15:30 &amp; Malam 18:30</p>
                </div>
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500">
                    @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'h-5 w-5'])
                </div>
            </div>
        </div>

        {{-- Status Absensi --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-5 relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-ember-400"></div>
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Status Absensi</p>
                    <p class="mt-1 text-[30px] font-bold leading-none tracking-tight text-ember-700">1 Perlu Disisi</p>
                    <p class="mt-1 text-[11px] text-bark-400">Sesi Sore belum diinput</p>
                </div>
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-ember-50 text-ember-600">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-5 w-5'])
                </div>
            </div>
        </div>

        {{-- Klinik PR Siswa --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-5">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Klinik PR Siswa</p>
                    <p class="mt-1 text-[30px] font-bold leading-none tracking-tight text-bark-800">1 Menunggu</p>
                    <p class="mt-1 text-[11px] text-bark-400">Dari: Aisyah Putri</p>
                </div>
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500">
                    @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-5 w-5'])
                </div>
            </div>
        </div>

        {{-- Jam Mengajar Pekan Ini --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-5">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Jam Mengajar Pekan Ini</p>
                    <p class="mt-1 text-[30px] font-bold leading-none tracking-tight text-bark-800">1 Jam</p>
                    <p class="mt-1 text-[11px] text-bark-400">Target: 16 Jam / Pekan</p>
                </div>
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500">
                    @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-5 w-5'])
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Mengajar Hari Ini --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-ember-50 px-2 py-[2px]">
                        <span class="h-2 w-2 rounded-full bg-ember-500"></span>
                        <span class="text-[10px] font-bold text-ember-700">Jadwal Mengajar Hari Ini</span>
                    </span>
                </div>
                <span class="text-[11px] text-bark-400">2 Sesi Terjadwal</span>
            </div>

            @php
                $sesiHariIni = [
                    [
                        'waktu' => '15:30 - 17:00 WIB',
                        'mapel' => 'Matematika Saintek: Kalkulus &amp; Integral Lanjutan',
                        'desc' => 'Pembahasan Tipe Soal Selektif Masuk PTN &amp; Latihan Soal Mandiri',
                        'kelas' => '12 IPA Intensif',
                        'ruang' => 'Ruang Eureka (Lt. 2)',
                        'siswa' => '14 Siswa Terdaftar',
                        'badge' => 'Akan Dimulai 45 Menit Lagi',
                        'badgeTone' => 'amber',
                        'actions' => [
                            ['label' => 'Input Absen Kelas', 'icon' => 'clipboard-form', 'tone' => 'ember'],
                            ['label' => 'Buka Modul Ajar', 'icon' => 'book-open', 'tone' => 'bark'],
                        ],
                    ],
                    [
                        'waktu' => '18:30 - 20:00 WIB',
                        'mapel' => 'TPS Penalaran Kuantitatif &amp; Aljabar Numerik',
                        'desc' => 'Trik Cepat Soal Deret Hingga dan Pola Bilangan Bertingkat',
                        'kelas' => 'Kelas Intensif UTKB',
                        'ruang' => 'Ruang Newton (Lt. 1)',
                        'siswa' => '18 Siswa Terdaftar',
                        'badge' => null,
                        'actions' => [
                            ['label' => 'Presensi Belum Dibuka (Buka 18:15 WIB)', 'icon' => 'clock', 'tone' => 'bark'],
                            ['label' => 'Lihat Roster &gt;', 'icon' => 'chevron-right', 'tone' => 'bark'],
                        ],
                    ],
                ];
            @endphp

            @foreach ($sesiHariIni as $sesi)
                <div class="rounded-2xl border border-bark-100 bg-white p-5">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <p class="text-[11px] font-bold text-bark-600">{{ $sesi['waktu'] }}</p>
                            <h3 class="text-[15px] font-bold text-bark-800 leading-snug">{{ $sesi['mapel'] }}</h3>
                            <p class="mt-1 text-[11px] text-bark-400">{{ $sesi['desc'] }}</p>
                        </div>
                        @if ($sesi['badge'])
                            <span @class([
                                'inline-flex items-center gap-1 rounded-full px-2.5 py-[2px] text-[9.5px] font-bold',
                                'bg-amber-50 text-amber-700' => $sesi['badgeTone'] === 'amber',
                                'bg-ember-50 text-ember-700' => $sesi['badgeTone'] === 'ember',
                            ])>
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                {{ $sesi['badge'] }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-[11px] text-bark-500 mb-4">
                        <div class="flex items-center gap-1">
                            @include('layouts.partials.icon', ['name' => 'users', 'class' => 'h-3.5 w-3.5'])
                            <span>{{ $sesi['kelas'] }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-3.5 w-3.5'])
                            <span>{{ $sesi['ruang'] }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3.5 w-3.5'])
                            <span>{{ $sesi['siswa'] }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        @foreach ($sesi['actions'] as $action)
                            <a href="#" @class([
                                'flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-[12px] font-semibold transition',
                                'bg-ember-600 text-white hover:bg-ember-700' => $action['tone'] === 'ember',
                                'bg-bark-800 text-white hover:bg-bark-900' => $action['tone'] === 'bark',
                            ])>
                                @include('layouts.partials.icon', ['name' => $action['icon'], 'class' => 'h-4 w-4'])
                                <span class="hidden sm:inline">{{ $action['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Klinik PR Siswa sidebar --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[14px] font-bold text-bark-800">Klinik PR Siswa</h2>
                <span class="text-[11px] font-bold text-ember-600">1 Perlu Tanggapan</span>
            </div>

            <div class="rounded-2xl border border-bark-100 bg-white p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-ember-100 text-[13px] font-bold text-ember-700">AP</div>
                    <div class="min-w-0">
                        <p class="text-[14px] font-bold text-bark-800 leading-snug">Aisyah Putri Azzahra</p>
                        <p class="mt-0.5 text-[11px] text-bark-400">12 IPA • Mengajar hari ini pukul 09:15 WIB</p>
                    </div>
                </div>
                <div class="rounded-xl border border-bark-100 bg-bark-50 p-3 mb-3">
                    <p class="text-[11px] font-bold text-bark-600 uppercase tracking-wide">Topik: Barisan Geometri Bertingkat</p>
                    <p class="mt-1 text-[12.5px] leading-relaxed text-bark-600">"Kak Hendra, untuk soal Try Out UTKB no. 14, jadi geometri tak finit pecahan, cara cepat eliminasi opssijawab tanpa substitusi..."</p>
                </div>
                <a href="#" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-3 text-[12px] font-bold text-white hover:bg-amber-600 transition">
                    @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-4 w-4'])
                    Jawab &amp; Berikan Pembahasan
                </a>
            </div>
        </div>
    </div>

    {{-- Akses Cepat Pengajar --}}
    <section class="rounded-2xl border border-bark-100 bg-white p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-[14px] font-bold text-bark-800">Akses Cepat Pengajar</h2>
        </div>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
            @php
                $quickAccess = [
                    ['Lihat Kalender Jadwal Pekanan', 'calendar', 'bark'],
                    ['Katalog Bank Soal &amp; Modul', 'book-open', 'bark'],
                    ['Kontak Admin Akademik', 'user', 'bark'],
                ];
            @endphp
            @foreach ($quickAccess as [$label, $icon, $tone])
                <a href="#" class="flex items-center gap-3 rounded-xl border border-bark-100 bg-white p-4 hover:border-ember-300 transition group">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500 group-hover:bg-ember-50 group-hover:text-ember-600 transition">
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4'])
                    </div>
                    <p class="text-[12px] font-semibold text-bark-700">{{ $label }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
