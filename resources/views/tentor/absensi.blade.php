@extends('layouts.tentor')

@section('title', 'Absensi Siswa')

@section('content')
@php
    $siswa = [
        ['Aisyah Putri Azzahra', '2410-0847', '0058237651', 'SMAN 8 Bandung', '10:31', 'hadir'],
        ['Dimas Bagus Pratama', '2410-0851', '0058129004', 'SMAN 1 Bandung', '10:32', 'hadir'],
        ['Siti Nabila Azzam', '2410-0855', '0058309912', 'SMA Tunas Bakti', '10:34', 'hadir'],
        ['Farhan Maulana', '2410-0860', '0058207731', 'SMA BPI Pemula', '10:36', 'hadir'],
        ['Rizky Ramadhan', '2410-0863', '0058411288', 'SMAN 8 Bandung', null, 'hadir'],
        ['Nadia Syahrani', '2410-0868', '0058105567', 'SMA Tunas Bakti', null, 'izin'],
        ['Muhammad Fajar', '2410-0871', '0058327410', 'SMAN 1 Bandung', '10:41', 'hadir'],
        ['Amanda Citra', '2410-0875', '0058234196', 'SMA BPI Pemula', '10:43', 'sakit'],
    ];

    $statusMap = [
        'hadir' => ['Hadir', 'bg-ember-100 text-ember-700 ring-ember-200'],
        'izin' => ['Izin', 'bg-amber-50 text-amber-700 ring-amber-200'],
        'sakit' => ['Sakit', 'bg-sky-50 text-sky-700 ring-sky-200'],
        'alpa' => ['Alpa', 'bg-rose-50 text-rose-700 ring-rose-200'],
    ];

    $riwayat = [
        ['Pertemuan 13', 'Jumat, 07/03/2025', 'Hadir: 14 · Sakit: 1 · Izin: 0', '14 / 15 Hadir', '94%'],
        ['Pertemuan 12', 'Selasa, 04/03/2025', 'Hadir: 13 · Sakit: 1 · Izin: 0', '13 / 15 Hadir', '87%'],
        ['Pertemuan 11', 'Jumat, 28/02/2025', 'Hadir: 14 · Sakit: 1 · Izin: 0', '14 / 15 Hadir', '94%'],
    ];
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
            <span class="inline-flex items-center gap-1.5 rounded-full bg-ember-100 px-2.5 py-1 text-[11px] font-bold text-ember-700">
                <span class="h-1.5 w-1.5 rounded-full bg-ember-500"></span>
                Sesi Berlangsung
            </span>
        </div>
        <p class="mt-1 text-[13px] text-bark-400">Catat kehadiran murid dan pantau rekap per pertemuan.</p>
    </div>

    {{-- Panel sesi --}}
    <div class="grid grid-cols-1 gap-x-6 gap-y-4 rounded-2xl border border-bark-100 bg-white p-5 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $sesi = [
                ['Rombongan Belajar', '12 IPA', 'Ruang Eureka Lt.2', 'graduation-cap'],
                ['Waktu Sesi', 'Senin, 10 Mar 2025', '10.30 – 11.30 WIB', 'clock'],
                ['Mata Pelajaran', 'Matematika', 'Topik: Kaidah Pencacahan', 'book-open'],
                ['Tentor Pengajar', 'Kak Hendra', 'Matematika · 8 tahun', 'user-check'],
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

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        @php
            $stats = [
                ['Total Siswa', '14', 'Terdaftar di kelas', 'bg-bark-100 text-bark-600', 'users'],
                ['Hadir', '13', 'Sudah tap-in', 'bg-emerald-50 text-emerald-600', 'user-check'],
                ['Izin / Sakit', '1', 'Dengan keterangan', 'bg-amber-50 text-amber-600', 'mail'],
                ['Alpa', '0', 'Tanpa keterangan', 'bg-rose-50 text-rose-600', 'siren'],
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

    {{-- Tabel kehadiran --}}
    <section class="overflow-hidden rounded-2xl border border-bark-100 bg-white">
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-5 py-4">
            <div>
                <h2 class="text-[15px] font-bold text-bark-800">Daftar Kehadiran</h2>
                <p class="text-[11px] text-bark-400">Perubahan tersimpan otomatis saat kamu menandai kehadiran.</p>
            </div>
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                <input type="search" placeholder="Cari nama siswa…"
                       class="w-full rounded-lg border border-bark-200 bg-bark-50 py-2 pl-8 pr-3 text-[12px] placeholder:text-bark-300 focus:border-ember-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-ember-100 sm:w-56">
            </div>
        </header>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left">
                <thead>
                    <tr class="border-b border-bark-100 bg-bark-50/60 text-[10px] font-bold uppercase tracking-wide text-bark-400">
                        <th class="w-14 px-5 py-3 text-center">No</th>
                        <th class="px-5 py-3">Nama Siswa &amp; NIS / NISN</th>
                        <th class="w-32 px-5 py-3">Waktu Tap-In</th>
                        <th class="w-64 px-5 py-3">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bark-100">
                    @foreach ($siswa as $i => [$nama, $nis, $nisn, $sekolah, $waktu, $status])
                        @php [$label, $badge] = $statusMap[$status]; @endphp
                        <tr class="transition hover:bg-bark-50/60">
                            <td class="px-5 py-3.5 text-center text-[12px] font-medium text-bark-400">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-ember-100 text-[11px] font-bold text-ember-700">
                                        {{ strtoupper(mb_substr($nama, 0, 1) . mb_substr(explode(' ', $nama)[1] ?? '', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-[13px] font-semibold text-bark-800">{{ $nama }}</p>
                                        <p class="truncate text-[11px] text-bark-400">{{ $nis }} · {{ $nisn }} · {{ $sekolah }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($waktu)
                                    <span class="text-[13px] font-semibold text-bark-700">{{ $waktu }}</span>
                                @else
                                    <span class="text-[13px] font-medium text-bark-300">— Belum tap-in</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="inline-flex rounded-lg bg-bark-50 p-0.5 ring-1 ring-bark-100">
                                    @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'] as $key => $opt)
                                        <span @class([
                                            'cursor-pointer rounded-md px-2.5 py-1 text-[11px] font-semibold transition',
                                            'text-bark-400 hover:text-bark-600' => $key !== $status,
                                            $badge . ' ring-1' => $key === $status,
                                        ])>{{ $opt }}</span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-bark-100 bg-bark-50/50 px-5 py-4">
            <p class="text-[12px] text-bark-400">
                Menampilkan <span class="font-bold text-bark-600">{{ count($siswa) }}</span> dari 14 siswa terdaftar.
            </p>
            <div class="flex items-center gap-2">
                <button type="button" class="rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                    Reset Form
                </button>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-500 px-3.5 py-2 text-[12px] font-semibold text-white shadow-sm transition hover:bg-ember-600">
                    @include('layouts.partials.icon', ['name' => 'clipboard-check', 'class' => 'h-3.5 w-3.5'])
                    Simpan Presensi Kelas
                </button>
            </div>
        </footer>
    </section>

    {{-- Riwayat pertemuan --}}
    <section class="rounded-2xl border border-bark-100 bg-white">
        <header class="border-b border-bark-100 px-5 py-4">
            <h2 class="text-[15px] font-bold text-bark-800">Riwayat Sesi Terakhir</h2>
            <p class="text-[11px] text-bark-400">Rekap kehadiran pertemuan yang sudah ditutup</p>
        </header>
        <ul class="grid grid-cols-1 divide-y divide-bark-100 sm:grid-cols-3 sm:divide-y-0">
            @foreach ($riwayat as [$pertemuan, $tanggal, $rincian, $jumlah, $persen])
                <li class="px-5 py-4 sm:border-r sm:border-bark-100 sm:last:border-r-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[13px] font-bold text-bark-800">{{ $pertemuan }}</p>
                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $persen }}</span>
                    </div>
                    <p class="mt-0.5 text-[11px] text-bark-400">{{ $tanggal }}</p>
                    <p class="mt-2 text-[12px] text-bark-600">
                        <span class="font-bold text-bark-800">{{ $jumlah }}</span>
                    </p>
                    <p class="mt-0.5 text-[11px] text-bark-400">{{ $rincian }}</p>
                </li>
            @endforeach
        </ul>
    </section>
</div>
@endsection