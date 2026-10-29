@extends('layouts.tentor')

@section('title', 'Dashboard Overview — Tentor')

@section('content')

    {{-- Top header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">
                Selamat datang, {{ $user?->name }}! 👋
            </h1>
            <p class="text-[13px] text-bark-400">Pantau jadwal kelas hari ini, input kehadiran siswa, dan jawab pertanyaan Klinik PR.</p>
        </div>
        <div class="flex items-center gap-3 rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
            <div class="flex flex-col items-center gap-1">
                <span class="text-[11px] font-bold uppercase tracking-wide text-bark-400">{{ now()->locale('id')->translatedFormat('l') }}</span>
                <span class="text-[13px] font-bold text-bark-800">{{ $todayDate }}</span>
            </div>
            <div class="w-px h-5 bg-bark-200 mx-1"></div>
            <span class="text-[13px] font-bold text-bark-800">{{ $todayTime }} WIB</span>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 mb-6">
        @foreach ($stats as $stat)
            <div class="rounded-2xl border border-bark-100 bg-white p-5 @if ($stat['label'] === 'Status Absensi' && $stat['value'] > 0) relative overflow-hidden @endif">
                @if ($stat['label'] === 'Status Absensi' && $stat['value'] > 0)
                    <div class="absolute inset-x-0 top-0 h-1 bg-ember-400"></div>
                @endif
                <div class="flex items-start justify-between">
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">{{ $stat['label'] }}</p>
                        <p class="mt-1 text-[30px] font-bold leading-none tracking-tight text-bark-800">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-[11px] text-bark-400">{{ $stat['sub'] }}</p>
                    </div>
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl {{ $stat['tone'] }}">
                        @include('layouts.partials.icon', ['name' => $stat['icon'], 'class' => 'h-5 w-5'])
                    </div>
                </div>
            </div>
        @endforeach
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
                <span class="text-[11px] text-bark-400">{{ $sessionsToday->count() }} Sesi Terjadwal</span>
            </div>

            @if ($sessionsToday->isEmpty())
                <div class="rounded-2xl border border-dashed border-bark-100 bg-bark-50/50 p-12 text-center">
                    <p class="text-[12px] text-bark-400">Belum ada jadwal mengajar untuk hari ini.</p>
                </div>
            @else
                @foreach ($sessionsToday as $sesi)
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
            @endif
        </div>

        {{-- Klinik PR Siswa sidebar --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[14px] font-bold text-bark-800">Klinik PR Siswa</h2>
                <span @class([
                    'text-[11px] font-bold',
                    'text-ember-600' => $qualifixTickets->isNotEmpty(),
                    'text-bark-400' => $qualifixTickets->isEmpty(),
                ])>
                    {{ $qualifixTickets->isNotEmpty() ? $qualifixTickets->count().' Perlu Tanggapan' : 'Tidak Ada Antrean' }}
                </span>
            </div>

            @if ($qualifixTickets->isEmpty())
                <div class="rounded-2xl border border-dashed border-bark-100 bg-bark-50/50 p-6 text-center">
                    <p class="text-[12px] text-bark-400">Tidak ada pertanyaan menunggu tanggapan Anda.</p>
                </div>
            @else
                @foreach ($qualifixTickets as $ticket)
                    <div class="rounded-2xl border border-bark-100 bg-white p-4">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-ember-100 text-[13px] font-bold text-ember-700">{{ $ticket['initials'] }}</div>
                            <div class="min-w-0">
                                <p class="text-[14px] font-bold text-bark-800 leading-snug">{{ $ticket['nama'] }}</p>
                                <p class="mt-0.5 text-[11px] text-bark-400">{{ $ticket['meta'] }}</p>
                            </div>
                        </div>
                        <div class="rounded-xl border border-bark-100 bg-bark-50 p-3 mb-3">
                            <p class="text-[11px] font-bold text-bark-600 uppercase tracking-wide">Topik: {{ $ticket['topik'] }}</p>
                            <p class="mt-1 text-[12.5px] leading-relaxed text-bark-600">{{ $ticket['preview'] }}</p>
                        </div>
                        <a href="#" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-3 text-[12px] font-bold text-white hover:bg-amber-600 transition">
                            @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-4 w-4'])
                            Jawab &amp; Berikan Pembahasan
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

@endsection
