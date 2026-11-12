@extends('layouts.wali')

@section('title', 'Dashboard Orang Tua')

@section('content')
<div class="space-y-5">

    {{-- Sambutan --}}
    <section class="rounded-2xl border border-bark-100 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gold/25 px-2.5 py-1 text-[10.5px] font-bold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-3.5 w-3.5'])
                    Portal Wali Murid Terhubung
                </span>
                <h1 class="mt-2.5 text-[22px] font-bold leading-snug tracking-tight text-bark-800">
                    Selamat datang kembali, {{ $guardian['name'] ?? 'Bapak/Ibu Wali' }}!
                </h1>
                <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">
                    Pantau perkembangan akademik, presensi tap-in RFID, serta instruksi putra-putri Anda secara transparan dan terintegrasi.
                </p>

                {{-- Anak --}}
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($anak as $a)
                        @php
                            $statusTone = match ($a['status'] ?? 'aktif') {
                                'aktif' => 'bg-gold text-espresso',
                                'cuti' => 'bg-amber-100 text-amber-700',
                                'nonaktif' => 'bg-rose-100 text-rose-600',
                                'lulus' => 'bg-bark-100 text-bark-600',
                                default => 'bg-bark-100 text-bark-600',
                            };
                            $statusLabel = match ($a['status'] ?? 'aktif') {
                                'aktif' => 'Aktif',
                                'cuti' => 'Cuti',
                                'nonaktif' => 'Nonaktif',
                                'lulus' => 'Lulus',
                                default => $a['status'],
                            };
                        @endphp
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-bark-100 bg-bark-50/60 p-3">
                            <div class="flex min-w-0 items-center gap-2.5">
                                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-bark-200 text-bark-600 text-[11.5px] font-bold">
                                    {{ $a['inisial'] ?? '??' }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <p class="text-[13px] font-bold text-bark-800">{{ $a['nama'] }}</p>
                                        <span class="rounded-md px-1.5 py-[2px] text-[9px] font-bold {{ $statusTone }}">{{ $statusLabel }}</span>
                                    </div>
                                    <p class="truncate text-[11px] text-bark-400">{{ $a['kelas'] ?? '-' }} • NISN {{ $a['nisn'] ?? '-' }}</p>
                                </div>
                            </div>
                            <button type="button" class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-bark-200 bg-white px-2.5 py-1.5 text-[11px] font-bold text-bark-600 transition hover:bg-bark-50">
                                Kelola Profil
                                @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3 w-3'])
                            </button>
                        </div>
                    @endforeach
                    @if (empty($anak))
                        <div class="rounded-xl border border-bark-100 bg-bark-50 p-3 text-center text-bark-400 text-sm">
                            Belum ada data siswa yang tercatat atas akun Anda.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $k)
            <div class="flex flex-col rounded-2xl border border-bark-100 bg-white p-4">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">{{ $k['label'] }}</p>
                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg {{ $k['icon_tone'] }}">
                        @include('layouts.partials.icon', ['name' => $k['icon'], 'class' => 'h-4 w-4'])
                    </div>
                </div>
                <p class="mt-2 text-[20px] font-bold leading-tight tracking-tight text-bark-800">{{ $k['value'] }}</p>
                <p class="mt-1 {{ $k['sub_tone'] }}">{{ $k['sub'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">

        {{-- Kiri: jadwal pekan ini --}}
        <section class="self-start rounded-2xl border border-bark-100 bg-white">
            <header class="flex flex-wrap items-center justify-between gap-2 border-b border-bark-100 px-5 py-4">
                <div>
                    <h2 class="text-[15px] font-bold text-bark-800">Jadwal Belajar Pekan Ini</h2>
                    <p class="mt-0.5 text-[11.5px] text-bark-400">
                        {{ $guardian['name'] ?? 'Wali' }}, silakan pantau jadwal anak Anda.
                    </p>
                </div>
                <a href="#" class="inline-flex items-center gap-1 text-[12px] font-bold text-ember-600 hover:text-ember-700">
                    Kalender Lengkap
                    @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                </a>
            </header>

            <ul class="divide-y divide-bark-100">
                @foreach ($jadwal as $j)
                    <li class="flex items-start gap-3 px-5 py-3.5">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-bark-800 text-center leading-none text-white">
                            <span class="text-[8.5px] font-bold uppercase text-bark-300">{{ $j['hari'] }}</span>
                            <span class="mt-0.5 text-[15px] font-bold">{{ $j['tgl'] }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <p class="text-[13.5px] font-bold text-bark-800">{{ $j['mapel'] }}</p>
                                @if ($j['hari_ini'])
                                    <span class="rounded-md px-1.5 py-[2px] text-[9.5px] font-bold bg-gold text-espresso">Hari Ini</span>
                                @endif
                            </div>
                            <p class="mt-1 text-[11.5px] leading-relaxed text-bark-400">{{ $j['waktu'] }} {{ $j['topik'] !== '-' ? '• '.$j['topik'] : '' }}</p>
                        </div>
                        <span class="shrink-0 rounded-md px-2.5 py-1.5 text-[10.5px] font-bold bg-bark-800 text-white">{{ $j['tentor'] }}</span>
                    </li>
                @endforeach
                @if (empty($jadwal))
                    <li class="px-5 py-4 text-center text-bark-400 text-sm">
                        Belum ada jadwal belajar pekan ini.
                    </li>
                @endif
            </ul>
        </section>

        {{-- Kanan: pengumuman + presensi --}}
        <div class="space-y-6">

            <section class="rounded-2xl bg-espresso px-5 py-5">
                <p class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.15em] text-gold">
                    @include('layouts.partials.icon', ['name' => 'megaphone', 'class' => 'h-3.5 w-3.5'])
                    Pengumuman Khusus Orang Tua
                </p>
                @if ($pengumuman)
                    <h2 class="mt-2 text-[16px] font-bold leading-snug text-white">{{ $pengumuman['judul'] }}</h2>
                    <p class="mt-1.5 text-[12px] leading-relaxed text-bark-300">{{ $pengumuman['isi'] }}</p>
                    <p class="mt-2 text-[11px] text-bark-400">{{ $pengumuman['tanggal'] }}</p>
                @else
                    <h2 class="mt-2 text-[16px] font-bold leading-snug text-white">Tidak ada pengumuman terbaru</h2>
                    <p class="mt-1.5 text-[12px] leading-relaxed text-bark-300">Saat ini belum ada pengumuman khusus untuk wali murid.</p>
                @endif
            </section>

            <section class="rounded-2xl border border-bark-100 bg-white px-5 py-4">
                <header class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-[14.5px] font-bold text-bark-800">Presensi Tap-In & Aktivitas</h2>
                        <p class="mt-0.5 text-[11.5px] text-bark-400">Notifikasi langsung realtime</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10.5px] font-bold text-emerald-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Live
                    </span>
                </header>

                <ul class="mt-4 space-y-4">
                    @foreach ($presensi['per_anak'] as $p)
                        <li class="flex items-start gap-2.5">
                            <span @class([
                                'mt-1.5 h-2 w-2 shrink-0 rounded-full',
                                'bg-emerald-500' => $p['persen'] >= 90,
                                'bg-amber-400' => $p['persen'] >= 75,
                                'bg-rose-400' => true,
                            ])></span>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center justify-between gap-x-2">
                                    <p class="text-[12.5px] font-bold text-bark-800">{{ $p['nama'] }}</p>
                                    <p class="text-[10.5px] text-bark-400">{{ number_format($p['persen'], 1, '.', ',') }}%</p>
                                </div>
                                <p class="mt-0.5 text-[11.5px] leading-relaxed text-bark-500">
                                    {{ $p['hadir'] }} Hadir • {{ $p['izin'] }} Izin • {{ $p['sakit'] }} Sakit • {{ $p['alpa'] }} Alpa
                                </p>
                                <p class="mt-0.5 text-[11px] text-bark-400">{{ $p['total_sesi'] }} sesi • NISN {{ $p['nis'] }}</p>
                            </div>
                        </li>
                    @endforeach
                    @if (empty($presensi['per_anak']))
                        <li class="px-3 py-3 text-center text-bark-400 text-sm">
                            Belum ada data presensi.
                        </li>
                    @endif
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection
