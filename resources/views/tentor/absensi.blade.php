@extends('layouts.tentor')

@section('title', 'Absensi Siswa')

@section('content')
@php
    $statusMap = [
        'hadir' => ['Hadir', 'bg-emerald-100 text-emerald-700 ring-emerald-200'],
        'izin' => ['Izin', 'bg-amber-50 text-amber-700 ring-amber-200'],
        'sakit' => ['Sakit', 'bg-sky-50 text-sky-700 ring-sky-200'],
        'alpa' => ['Alpa', 'bg-rose-50 text-rose-700 ring-rose-200'],
    ];

    $sessionStatusOptions = ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'];

    // Jam sesi disimpan sebagai teks HH:MM:SS, tampilkan dengan pemisah titik.
    $jam = fn (?string $waktu) => $waktu === null ? '-' : str_replace(':', '.', substr($waktu, 0, 5));
@endphp

<div class="space-y-5">

    {{-- Breadcrumb + judul --}}
    <div>
        <nav class="flex items-center gap-1.5 text-[11px] font-medium text-bark-400">
            <a href="{{ route('tentor.dashboard') }}" class="transition hover:text-ember-600">Dashboard</a>
            @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3 w-3 text-bark-300'])
            <span class="text-bark-600">Absensi</span>
        </nav>
        <div class="mt-2 flex flex-wrap items-center gap-3">
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Absensi Siswa</h1>
            @if ($session)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    {{ $session->tanggal->isToday() ? 'Sesi Hari Ini' : 'Sesi '.$session->tanggal->locale('id')->translatedFormat('d/m/Y') }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-bark-100 px-2.5 py-1 text-[11px] font-bold text-bark-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-bark-400"></span>
                    Tidak Ada Sesi
                </span>
            @endif
        </div>
        <p class="mt-1 text-[13px] text-bark-400">Catat kehadiran murid dan pantau rekap per pertemuan.</p>
    </div>

    {{-- Filter rentang & daftar sesi --}}
    <div class="rounded-2xl border border-bark-100 bg-white p-4">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div class="flex flex-wrap items-end gap-2.5">
                <label class="block">
                    <span class="mb-1 block text-[11px] font-semibold text-bark-600">Dari</span>
                    <input type="date" name="dari" value="{{ $dari->toDateString() }}" form="filter-rentang"
                           class="rounded-lg border border-bark-100 bg-bark-50 px-3 py-2 text-[12.5px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                </label>
                <label class="block">
                    <span class="mb-1 block text-[11px] font-semibold text-bark-600">Sampai</span>
                    <input type="date" name="sampai" value="{{ $sampai->toDateString() }}" form="filter-rentang"
                           class="rounded-lg border border-bark-100 bg-bark-50 px-3 py-2 text-[12.5px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                </label>
                <button type="submit" form="filter-rentang"
                        class="rounded-lg bg-bark-800 px-4 py-2 text-[12.5px] font-bold text-white transition hover:bg-bark-900">
                    Tampilkan
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                @foreach ([
                    'Harian ini' => [today()->toDateString(), today()->toDateString()],
                    'Minggu ini' => [today()->startOfWeek()->toDateString(), today()->endOfWeek()->toDateString()],
                    '7 Hari' => [today()->subDays(7)->toDateString(), today()->addDays(7)->toDateString()],
                    '30 Hari Ke Depan' => [today()->toDateString(), today()->addDays(30)->toDateString()],
                ] as $label => $rentang)
                    <a href="{{ route('tentor.absensi', ['dari' => $rentang[0], 'sampai' => $rentang[1]]) }}"
                       class="rounded-lg border border-bark-200 bg-white px-3 py-1.5 text-[11.5px] font-semibold text-bark-600 transition hover:bg-bark-50">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <form id="filter-rentang" method="GET" action="{{ route('tentor.absensi') }}" class="hidden"></form>

        @if (count($barisSesi) > 0)
            <div class="mt-4 flex items-center gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Daftar Sesi</span>
                <span class="rounded-full bg-bark-100 px-2 py-0.5 text-[10.5px] font-bold text-bark-600">
                    {{ count($barisSesi) }} Sesi
                </span>
            </div>
            <div class="mt-2 overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead>
                        <tr class="bg-bark-50/60 text-[10px] font-bold uppercase tracking-wide text-bark-400">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Jam</th>
                            <th class="px-4 py-3">Mata Pelajaran</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Ruang</th>
                            <th class="px-4 py-3 text-right">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-bark-100">
                        @foreach ($barisSesi as $baris)
                            @php
                                $isSelected = $session && $session->id === $baris['id'];
                                $sesiUrl = route('tentor.absensi', [
                                    'session_id' => $baris['id'],
                                    'dari' => $dari->toDateString(),
                                    'sampai' => $sampai->toDateString(),
                                ]);
                            @endphp
                            <tr @class([
                                'transition cursor-pointer group',
                                'bg-ember-50/70 border-l-4 border-l-ember-500 font-medium' => $isSelected,
                                'hover:bg-bark-50/80 border-l-4 border-l-transparent' => ! $isSelected,
                            ]) onclick="window.location.href='{{ $sesiUrl }}'">
                                <td class="px-4 py-3">
                                    <a href="{{ $sesiUrl }}"
                                       class="text-[12.5px] font-bold text-bark-800 group-hover:text-ember-600 {{ $isSelected ? 'text-ember-600' : '' }}">
                                        {{ $baris['label_tanggal'] }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-[12.5px] font-semibold tabular-nums text-bark-600">{{ $baris['waktu'] }}</td>
                                <td class="px-4 py-3 text-[12.5px] font-medium text-bark-700">{{ $baris['mapel'] }}</td>
                                <td class="px-4 py-3 text-[12.5px] font-medium text-bark-700">{{ $baris['rombel'] }}</td>
                                <td class="px-4 py-3 text-[12.5px] font-medium text-bark-500">{{ $baris['ruang'] }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-block rounded-md px-2 py-1 text-[11px] font-bold {{ $baris['tone'] }}">
                                        {{ $baris['badge'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-4 rounded-xl border border-dashed border-bark-100 bg-bark-50 px-4 py-8 text-center">
                <p class="text-[14px] font-bold text-bark-800">Tidak ada sesi mengajar pada rentang ini</p>
                <p class="mt-1 text-[12px] text-bark-400">
                    {{ $dari->locale('id')->translatedFormat('d M Y') }} — {{ $sampai->locale('id')->translatedFormat('d M Y') }}
                </p>
                <a href="{{ route('tentor.absensi', ['dari' => today()->toDateString(), 'sampai' => today()->addDays(30)->toDateString()]) }}"
                   class="mt-3 inline-block text-[12px] font-bold text-ember-600 hover:underline">
                    Lihat 30 hari ke depan
                </a>
            </div>
        @endif
    </div>

    {{-- Panel sesi --}}
    @if ($session)
        <div class="grid grid-cols-1 gap-x-6 gap-y-4 rounded-2xl border border-bark-100 bg-white p-5 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $sesi = [
                    ['Rombongan Belajar', $session->classGroup->code ?? '-', $session->classGroup->education_level.' '.$session->classGroup->grade.' '.$session->classGroup->major ?? '-', 'graduation-cap'],
                    ['Waktu Sesi', $session->tanggal->locale('id')->translatedFormat('l, j F Y'), $jam($session->start_time).' – '.$jam($session->end_time).' WIB', 'clock'],
                    ['Mata Pelajaran', $session->subject->name ?? $session->topic ?? '-', 'Topik: '.($session->topic ?? '-'), 'book-open'],
                    ['Ruangan', $session->room->name ?? '-', $session->room->building ?? '', 'map-pin'],
                ];
            @endphp
            @foreach ($sesi as [$label, $value, $meta, $icon])
                <div class="flex items-start gap-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500">
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                        <p class="mt-0.5 truncate text-[14px] font-bold text-bark-800">{{ $value }}</p>
                        <p class="truncate text-[11px] text-bark-400">{{ $meta }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Kehadiran tentor --}}
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-bark-100 bg-white px-5 py-4">
            <div class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-ember-50 text-ember-600">
                    @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-[18px] w-[18px]'])
                </span>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Kehadiran Anda</p>
                    @if (! $kehadiran)
                        <p class="mt-1 text-[14px] font-bold text-bark-800">Belum check-in</p>
                        <p class="truncate text-[11px] text-bark-400">Catat kehadiran supaya admin tahu Anda sudah mengajar.</p>
                    @elseif ($kehadiran->check_out_at)
                        <p class="mt-1 text-[14px] font-bold text-bark-800">
                            Check-in {{ $kehadiran->check_in_at?->format('H.i') }} &middot; Checkout {{ $kehadiran->check_out_at->format('H.i') }}
                        </p>
                        <p class="truncate text-[11px] text-emerald-600">Selesai mengajar</p>
                    @else
                        <p class="mt-1 text-[14px] font-bold text-bark-800">Sudah check-in pukul {{ $kehadiran->check_in_at?->format('H.i') }}</p>
                        <p class="truncate text-[11px] text-bark-400">
                            {{ $kehadiran->status === 'terlambat' ? 'Check-in terlambat' : 'Check-in tepat waktu' }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if (! $kehadiran || ! $kehadiran->check_out_at)
                    @if ($session->tanggal->isToday())
                        <form method="POST" action="{{ route('tentor.absensi.check-in') }}">
                            @csrf
                            <input type="hidden" name="session_id" value="{{ $session->id }}">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-espresso px-4 py-2.5 text-[12.5px] font-bold text-white transition hover:bg-bark-800">
                                @include('layouts.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4 text-gold'])
                                {{ $kehadiran ? 'Check-in Ulang' : 'Check-in' }}
                            </button>
                        </form>
                    @else
                        <span class="rounded-lg border border-bark-200 bg-white px-4 py-2.5 text-[12px] font-semibold text-bark-400">
                            Bisa check-in pada hari sesi
                        </span>
                    @endif
                @endif

                @if ($kehadiran && ! $kehadiran->check_out_at)
                    <form method="POST" action="{{ route('tentor.absensi.check-out') }}">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $session->id }}">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg border border-bark-200 bg-white px-4 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                            @include('layouts.partials.icon', ['name' => 'log-out', 'class' => 'h-4 w-4 text-bark-400'])
                            Checkout
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Stat cards --}}
        @php
            $totalSiswa = $students->count();
            $hadirCount = $attendances->where('status', 'hadir')->count();
            $izinCount = $attendances->whereIn('status', ['izin', 'sakit'])->count();
            $alpaCount = $attendances->where('status', 'alpa')->count();
            $belumInput = $totalSiswa - $attendances->count();
        @endphp
        <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            @php
                $stats = [
                    ['Total Siswa', $totalSiswa, 'Terdaftar', 'bg-bark-100 text-bark-600', 'users'],
                    ['Hadir', $hadirCount, 'Sudah tap-in', 'bg-emerald-50 text-emerald-600', 'user-check'],
                    ['Izin / Sakit', $izinCount, 'Dengan keterangan', 'bg-amber-50 text-amber-600', 'mail'],
                    ['Alpa', $alpaCount, 'Tanpa keterangan', 'bg-rose-50 text-rose-600', 'siren'],
                ];
            @endphp
            @foreach ($stats as [$label, $value, $sub, $tone, $icon])
                <div class="relative overflow-hidden rounded-2xl border border-bark-100 bg-white p-5">
                    <div class="absolute inset-x-0 top-0 h-1 bg-ember-400"></div>
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                            <p class="mt-2 text-[28px] font-bold leading-none tracking-tight text-bark-800">{{ $value }}</p>
                        </div>
                        <div @class(['grid h-9 w-9 shrink-0 place-items-center rounded-xl', $tone])>
                            @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-[18px] w-[18px]'])
                        </div>
                    </div>
                    <p class="mt-3 text-[11px] text-bark-400">{{ $sub }}</p>
                </div>
            @endforeach
        </div>

        {{-- Status Penyimpanan Absensi --}}
        @if ($isSaved)
            @if ($isDuringClassHours)
                <div id="attendanceLockBanner" class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                    <div class="flex items-center gap-3">
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-emerald-100 text-emerald-700">
                            @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-5 w-5'])
                        </div>
                        <div>
                            <p class="text-[13.5px] font-bold text-emerald-900">Absensi Telah Tersimpan</p>
                            <p class="text-[11.5px] text-emerald-700">Status kehadiran terkunci. Anda dapat mengedit kehadiran karena saat ini jam pembelajaran sedang berlangsung ({{ substr((string)$session->start_time, 0, 5) }} – {{ substr((string)$session->end_time, 0, 5) }} WIB).</p>
                        </div>
                    </div>
                    <button type="button" id="btnEditAbsensi"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-[12px] font-bold text-white shadow-sm transition hover:bg-emerald-700">
                        @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-3.5 w-3.5 text-white'])
                        Edit Absensi
                    </button>
                </div>
            @else
                <div id="attendanceLockBanner" class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-bark-200 bg-bark-50/80 p-4">
                    <div class="flex items-center gap-3">
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bark-200 text-bark-600">
                            @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-5 w-5'])
                        </div>
                        <div>
                            <p class="text-[13.5px] font-bold text-bark-800">Absensi Telah Tersimpan & Terkunci</p>
                            <p class="text-[11.5px] text-bark-500">Edit absensi hanya dapat dilakukan saat jam pembelajaran sesi ini berlangsung ({{ substr((string)$session->start_time, 0, 5) }} – {{ substr((string)$session->end_time, 0, 5) }} WIB).</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-xl border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-bark-400 cursor-not-allowed">
                        @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-3.5 w-3.5 text-bark-400'])
                        Edit Hanya Saat Jam Pembelajaran
                    </span>
                </div>
            @endif

            <div id="attendanceEditBanner" class="hidden flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50/70 p-4">
                <div class="flex items-center gap-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-amber-100 text-amber-700">
                        @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-5 w-5'])
                    </div>
                    <div>
                        <p class="text-[13.5px] font-bold text-amber-900">Mode Edit Absensi Aktif</p>
                        <p class="text-[11.5px] text-amber-700">Anda sedang mengubah data kehadiran siswa. Klik "Simpan Perubahan Presensi" setelah selesai.</p>
                    </div>
                </div>
                <button type="button" id="btnBatalEdit"
                        class="inline-flex items-center gap-2 rounded-xl border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-bark-600 shadow-sm transition hover:bg-bark-50">
                    Batal Edit
                </button>
            </div>
        @else
            <div class="flex items-center gap-3 rounded-2xl border border-bark-100 bg-white p-4">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500">
                    @include('layouts.partials.icon', ['name' => 'clipboard-check', 'class' => 'h-5 w-5'])
                </div>
                <div>
                    <p class="text-[13.5px] font-bold text-bark-800">Absensi Belum Disimpan</p>
                    <p class="text-[11.5px] text-bark-400">Silakan input status kehadiran siswa di bawah ini, lalu klik "Simpan Presensi Kelas".</p>
                </div>
            </div>
        @endif

        {{-- Form absensi --}}
        <form action="{{ route('tentor.absensi.store') }}" method="POST" id="attendanceForm" class="space-y-5">
            @csrf
            <input type="hidden" name="session_id" value="{{ $session->id }}">
            <input type="hidden" name="dari" value="{{ $dari->toDateString() }}">
            <input type="hidden" name="sampai" value="{{ $sampai->toDateString() }}">

            {{-- Tabel kehadiran --}}
            <section class="overflow-hidden rounded-2xl border border-bark-100 bg-white">
                <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-5 py-4">
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Daftar Kehadiran</h2>
                        <p class="text-[11px] text-bark-400">Pilih status kehadiran setiap peserta didik pada sesi ini.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                            <input type="search" placeholder="Cari nama siswa…"
                                   class="w-full rounded-lg border border-bark-200 bg-bark-50 py-2 pl-8 pr-3 text-[12px] placeholder:text-bark-300 focus:border-ember-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-ember-100 sm:w-48"
                                   id="searchInput">
                        </div>
                        <button type="button" id="setAllHadirBtn"
                                @class([
                                    'inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-[12px] font-semibold text-emerald-700 transition hover:bg-emerald-100',
                                    'hidden' => $isSaved,
                                ])>
                            @include('layouts.partials.icon', ['name' => 'check', 'class' => 'h-3.5 w-3.5'])
                            Set Semua Hadir
                        </button>
                    </div>
                </header>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-left">
                        <thead>
                            <tr class="border-b border-bark-100 bg-bark-50/60 text-[10px] font-bold uppercase tracking-wide text-bark-400">
                                <th class="w-14 px-5 py-3 text-center">No</th>
                                <th class="px-5 py-3">Nama Siswa & NIS / NISN</th>
                                <th class="w-32 px-5 py-3">Waktu Datang</th>
                                <th class="w-64 px-5 py-3">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bark-100" id="attendanceTableBody">
                            @forelse ($students as $i => $siswa)
                                @php
                                    $attendance = $attendances->get($siswa->id);
                                    $currentStatus = $attendance->status ?? 'alpa';
                                    [$label, $badge] = $statusMap[$currentStatus];
                                    $waktu = $attendance?->arrived_at ? $attendance->arrived_at->format('H:i') : null;
                                @endphp
                                <tr class="transition hover:bg-bark-50/60" data-nama="{{ strtolower($siswa->full_name) }}">
                                    <td class="px-5 py-3.5 text-center text-[12px] font-medium text-bark-400">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-ember-100 text-[11px] font-bold text-ember-700">
                                                {{ strtoupper(mb_substr($siswa->full_name, 0, 1) . mb_substr(explode(' ', $siswa->full_name)[1] ?? '', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-[13px] font-semibold text-bark-800">{{ $siswa->full_name }}</p>
                                                <p class="truncate text-[11px] text-bark-400">{{ $siswa->nis }} · {{ $siswa->nisn }} · {{ $siswa->school_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <input type="time" name="attendances[{{ $siswa->id }}][arrived_at]"
                                               value="{{ $waktu }}"
                                               @if ($isSaved) disabled @endif
                                               class="time-input w-full rounded-lg border border-bark-200 bg-bark-50 py-1.5 pl-2 pr-3 text-[12px] placeholder:text-bark-300 focus:border-ember-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-ember-100 disabled:opacity-60 disabled:bg-bark-100/50">
                                        <input type="hidden" name="attendances[{{ $siswa->id }}][student_id]" value="{{ $siswa->id }}">
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div @class([
                                            'status-group inline-flex rounded-lg bg-bark-50 p-0.5 ring-1 ring-bark-100',
                                            'pointer-events-none opacity-90' => $isSaved,
                                        ])>
                                            @foreach ($sessionStatusOptions as $key => $opt)
                                                @php
                                                    $isActive = ($key === $currentStatus);
                                                @endphp
                                                <button type="button"
                                                        data-student="{{ $siswa->id }}"
                                                        data-status="{{ $key }}"
                                                        @if ($isSaved) disabled @endif
                                                        class="status-btn px-2.5 py-1 text-[11px] font-semibold rounded-md transition
                                                            {{ $isActive ? $badge.' ring-1' : 'text-bark-400 hover:text-bark-600' }}"
                                                        @class(['cursor-pointer' => ! $isSaved, 'cursor-default' => $isSaved])
                                                >
                                                    {{ $opt }}
                                                </button>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="attendances[{{ $siswa->id }}][status]" value="{{ $currentStatus }}" class="status-input" data-student="{{ $siswa->id }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-bark-400">Tidak ada siswa terdaftar di kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-bark-100 bg-bark-50/50 px-5 py-4">
                    <p class="text-[12px] text-bark-400">
                        Menampilkan <span class="font-bold text-bark-600">{{ $totalSiswa }}</span> siswa terdaftar.
                    </p>
                    <div class="flex items-center gap-2">
                        <button type="button" id="resetBtn"
                                @class([
                                    'rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50',
                                    'hidden' => $isSaved,
                                ])>
                            Reset Form
                        </button>
                        @if ($isSaved && $isDuringClassHours)
                            <button type="button" id="footerEditBtn"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-bark-700 shadow-sm transition hover:bg-bark-50">
                                @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-3.5 w-3.5 text-ember-600'])
                                Edit Presensi
                            </button>
                        @elseif ($isSaved)
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-bark-100/60 px-3.5 py-2 text-[11.5px] font-medium text-bark-400 cursor-not-allowed" title="Edit hanya dapat dilakukan saat jam sesi berlangsung">
                                @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-3 w-3 text-bark-400'])
                                Terkunci di Luar Jam Sesi
                            </span>
                        @endif
                        <button type="submit" id="submitBtn"
                                @class([
                                    'inline-flex items-center gap-1.5 rounded-lg bg-ember-500 px-3.5 py-2 text-[12px] font-semibold text-white shadow-sm transition hover:bg-ember-600',
                                    'hidden' => $isSaved,
                                ])
                                @if ($students->isEmpty()) disabled @endif>
                            @include('layouts.partials.icon', ['name' => 'clipboard-check', 'class' => 'h-3.5 w-3.5'])
                            <span id="submitBtnText">{{ $isSaved ? 'Simpan Perubahan Presensi' : 'Simpan Presensi Kelas' }}</span>
                        </button>
                    </div>
                </footer>
            </section>

        </form>

        {{-- Riwayat pertemuan --}}
        @php
            // Ambil sesi sebelumnya untuk riwayat
            $riwayatSessions = \App\Models\ClassSession::with(['classGroup', 'subject'])
                ->where('staff_id', $staffProfile->id)
                ->whereDate('session_date', '<', today())
                ->orderByDesc('session_date')
                ->take(5)
                ->get();
        @endphp

        @if ($riwayatSessions->isNotEmpty())
            <section class="rounded-2xl border border-bark-100 bg-white">
                <header class="border-b border-bark-100 px-5 py-4">
                    <h2 class="text-[15px] font-bold text-bark-800">Riwayat Sesi Terakhir</h2>
                    <p class="text-[11px] text-bark-400">Rekap kehadiran pertemuan yang sudah ditutup</p>
                </header>
                <ul class="grid grid-cols-1 divide-y divide-bark-100 sm:grid-cols-3 sm:divide-y-0">
                    @foreach ($riwayatSessions as $sesi)
                        @php
                            $histAttendances = \App\Models\StudentAttendance::where('class_session_id', $sesi->id)->get();
                            $histHadir = $histAttendances->where('status', 'hadir')->count();
                            $histSakit = $histAttendances->where('status', 'sakit')->count();
                            $histIzin = $histAttendances->where('status', 'izin')->count();
                            $histTotal = $histAttendances->count();
                            $histPersen = $histTotal > 0 ? round(($histHadir / $histTotal) * 100) : 0;
                        @endphp
                        <li class="px-5 py-4 sm:border-r sm:border-bark-100 sm:last:border-r-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[13px] font-bold text-bark-800">{{ $sesi->subject->name ?? 'Sesi' }} · {{ $sesi->tanggal->format('d/m/Y') }}</p>
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $histPersen }}%</span>
                            </div>
                            <p class="mt-0.5 text-[11px] text-bark-400">{{ $sesi->tanggal->locale('id')->translatedFormat('l, j F Y') }}</p>
                            <p class="mt-2 text-[12px] text-bark-600">
                                <span class="font-bold text-bark-800">{{ $histHadir }} / {{ $histTotal }} Hadir</span>
                            </p>
                            <p class="mt-0.5 text-[11px] text-bark-400">Hadir: {{ $histHadir }} · Sakit: {{ $histSakit }} · Izin: {{ $histIzin }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

    @else
        {{-- Tidak ada sesi pada rentang aktif --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-12 text-center">
            @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'mx-auto h-12 w-12 text-bark-300'])
            <h3 class="mt-4 text-[18px] font-bold text-bark-800">Tidak ada jadwal mengajar pada rentang ini</h3>
            <p class="mt-2 text-bark-400">
                Anda tidak memiliki sesi mengajar antara
                {{ $dari->locale('id')->translatedFormat('l, j F Y') }} dan
                {{ $sampai->locale('id')->translatedFormat('l, j F Y') }}.
            </p>
            <a href="{{ route('tentor.absensi', ['dari' => today()->toDateString(), 'sampai' => today()->addDays(30)->toDateString()]) }}"
               class="mt-4 inline-block text-[12.5px] font-bold text-ember-600 hover:underline">
                Lihat 30 hari ke depan
            </a>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isInitiallySaved = {{ $isSaved ? 'true' : 'false' }};
    const canEdit = {{ ($isSaved && ! $isDuringClassHours) ? 'false' : 'true' }};
    let isEditMode = !isInitiallySaved;

    const badgeClasses = {
        hadir: 'bg-emerald-100 text-emerald-700 ring-emerald-200',
        izin: 'bg-amber-50 text-amber-700 ring-amber-200',
        sakit: 'bg-sky-50 text-sky-700 ring-sky-200',
        alpa: 'bg-rose-50 text-rose-700 ring-rose-200',
    };

    // Simpan nilai awal untuk fungsi batal edit
    const originalData = [];
    document.querySelectorAll('#attendanceTableBody tr[data-nama]').forEach(row => {
        const studentInput = row.querySelector('.status-input');
        const timeInput = row.querySelector('.time-input');
        if (studentInput) {
            originalData.push({
                studentId: studentInput.dataset.student,
                status: studentInput.value,
                time: timeInput ? timeInput.value : '',
            });
        }
    });

    function updateStudentStatusVisual(studentId, status) {
        document.querySelectorAll(`.status-btn[data-student="${studentId}"]`).forEach(b => {
            const isActive = b.dataset.status === status;
            if (isActive) {
                b.className = 'status-btn px-2.5 py-1 text-[11px] font-semibold rounded-md transition ' + badgeClasses[status] + ' ring-1 ' + (isEditMode ? 'cursor-pointer' : 'cursor-default');
            } else {
                b.className = 'status-btn px-2.5 py-1 text-[11px] font-semibold rounded-md transition text-bark-400 hover:text-bark-600 ' + (isEditMode ? 'cursor-pointer' : 'cursor-default');
            }
        });
    }

    function setEditMode(editable) {
        isEditMode = editable;

        // Toggle banner
        const lockBanner = document.getElementById('attendanceLockBanner');
        const editBanner = document.getElementById('attendanceEditBanner');
        if (lockBanner && editBanner) {
            if (editable) {
                lockBanner.classList.add('hidden');
                editBanner.classList.remove('hidden');
            } else {
                lockBanner.classList.remove('hidden');
                editBanner.classList.add('hidden');
            }
        }

        // Toggle grup status dan tombol status
        document.querySelectorAll('.status-group').forEach(group => {
            if (editable) {
                group.classList.remove('pointer-events-none', 'opacity-90');
            } else {
                group.classList.add('pointer-events-none', 'opacity-90');
            }
        });

        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.disabled = !editable;
            if (editable) {
                btn.classList.add('cursor-pointer');
                btn.classList.remove('cursor-default');
            } else {
                btn.classList.remove('cursor-pointer');
                btn.classList.add('cursor-default');
            }
        });

        // Toggle input waktu datang
        document.querySelectorAll('.time-input').forEach(input => {
            input.disabled = !editable;
        });

        // Toggle tombol aksi
        const setAllHadirBtn = document.getElementById('setAllHadirBtn');
        const resetBtn = document.getElementById('resetBtn');
        const footerEditBtn = document.getElementById('footerEditBtn');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');

        if (setAllHadirBtn) setAllHadirBtn.classList.toggle('hidden', !editable);
        if (resetBtn) resetBtn.classList.toggle('hidden', !editable);
        if (footerEditBtn) footerEditBtn.classList.toggle('hidden', editable);
        if (submitBtn) {
            submitBtn.classList.toggle('hidden', !editable);
            if (submitBtnText) {
                submitBtnText.textContent = isInitiallySaved ? 'Simpan Perubahan Presensi' : 'Simpan Presensi Kelas';
            }
        }
    }

    // Tombol Edit Presensi
    const btnEditAbsensi = document.getElementById('btnEditAbsensi');
    const footerEditBtn = document.getElementById('footerEditBtn');
    [btnEditAbsensi, footerEditBtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!canEdit) {
                    alert('Edit absensi hanya dapat dilakukan saat jam pembelajaran sesi ini berlangsung.');
                    return;
                }
                setEditMode(true);
            });
        }
    });

    // Tombol Batal Edit
    const btnBatalEdit = document.getElementById('btnBatalEdit');
    if (btnBatalEdit) {
        btnBatalEdit.addEventListener('click', function(e) {
            e.preventDefault();
            // Kembalikan ke data awal
            originalData.forEach(item => {
                const statusInput = document.querySelector(`.status-input[data-student="${item.studentId}"]`);
                if (statusInput) statusInput.value = item.status;

                const timeInput = document.querySelector(`input[name="attendances[${item.studentId}][arrived_at]"]`);
                if (timeInput) timeInput.value = item.time;

                updateStudentStatusVisual(item.studentId, item.status);
            });
            setEditMode(false);
        });
    }

    // Tombol ganti status siswa
    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!isEditMode) return;
            const studentId = this.dataset.student;
            const status = this.dataset.status;

            // Update hidden input
            const statusInput = document.querySelector(`.status-input[data-student="${studentId}"]`);
            if (statusInput) statusInput.value = status;

            // Update button styles
            updateStudentStatusVisual(studentId, status);

            // Auto-fill jam saat dipilih hadir jika belum ada jam, dan kosongkan jika status bukan hadir
            const timeInput = document.querySelector(`input[name="attendances[${studentId}][arrived_at]"]`);
            if (timeInput) {
                if (status === 'hadir' && !timeInput.value) {
                    const now = new Date();
                    const hh = String(now.getHours()).padStart(2, '0');
                    const mm = String(now.getMinutes()).padStart(2, '0');
                    timeInput.value = `${hh}:${mm}`;
                } else if (status !== 'hadir') {
                    timeInput.value = '';
                }
            }
        });
    });

    // Search filter
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('attendanceTableBody');
    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            tableBody.querySelectorAll('tr[data-nama]').forEach(row => {
                row.style.display = row.dataset.nama.includes(query) ? '' : 'none';
            });
        });
    }

    // Set semua hadir
    const setAllHadirBtn = document.getElementById('setAllHadirBtn');
    if (setAllHadirBtn) {
        setAllHadirBtn.addEventListener('click', function() {
            if (!isEditMode) return;
            document.querySelectorAll('.status-btn[data-status="hadir"]').forEach(btn => {
                btn.click();
            });
        });
    }

    // Reset button
    const resetBtn = document.getElementById('resetBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (!isEditMode) return;
            if (confirm('Reset semua status ke Alpa dan kosongkan waktu datang?')) {
                document.querySelectorAll('.status-btn[data-status="alpa"]').forEach(btn => {
                    btn.click();
                });
                document.querySelectorAll('.time-input').forEach(input => {
                    input.value = '';
                });
            }
        });
    }
});
</script>
@endpush