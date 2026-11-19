@extends('layouts.tentor')

@section('title', 'Dashboard Overview — Tentor')

@section('content')

    {{-- Header: sambutan + informasi waktu --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[2fr_1fr] mb-6">

        {{-- Card sambutan --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-5">
            <span class="inline-flex items-center gap-1.5 rounded-md bg-ember-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-ember-700">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-ember-500"></span>
                {{ $jobTitle }}
            </span>
            <h1 class="mt-3 text-[22px] font-bold tracking-tight text-bark-800">
                Selamat datang, {{ $staffProfile?->full_name_with_title ?? $user?->name }}! 👋
            </h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">
                Pantau jadwal kelas hari ini, input kehadiran siswa, dan jawab pertanyaan QualiFix.
            </p>
        </div>

        {{-- Card hari, tanggal, dan jam --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Hari Ini</p>
                    <p class="mt-1.5 text-[15px] font-bold leading-snug text-bark-800">{{ now()->locale('id')->translatedFormat('l') }}</p>
                    <p class="mt-0.5 text-[12.5px] text-bark-400">{{ $todayDate }}</p>
                </div>
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4'])
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 border-t border-bark-100 pt-3">
                @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-4 w-4 text-bark-400'])
                <span id="liveClock" class="text-[13px] font-bold text-bark-800">{{ $todayTime }} WIB</span>
            </div>
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
                                <a href="{{ $action['url'] ?? $action['href'] ?? route('tentor.absensi') }}" @class([
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

        {{-- QualiFix sidebar --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[14px] font-bold text-bark-800">Qualifix Siswa</h2>
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
                @foreach ($qualifixTickets->take(2) as $ticket)
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
                            <p class="mt-1 text-[12.5px] leading-relaxed text-bark-600 line-clamp-3">{{ $ticket['preview'] }}</p>
                        </div>
                        <a href="{{ route('tentor.qualifix') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-3 text-[12px] font-bold text-white hover:bg-amber-600 transition">
                            @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-4 w-4'])
                            Jawab &amp; Berikan Pembahasan
                        </a>
                    </div>
                @endforeach

                {{-- Teks akses pertanyaan lainnya di bawah pertanyaan terakhir / di atas akses cepat --}}
                <div class="rounded-xl border border-bark-100 bg-white p-3 text-center">
                    @if ($qualifixTickets->count() > 2)
                        <a href="{{ route('tentor.qualifix') }}" class="inline-flex items-center gap-1.5 text-[12px] font-bold text-ember-600 hover:text-ember-700 transition">
                            <span>Lihat {{ $qualifixTickets->count() - 2 }} pertanyaan lainnya di Qualifix</span>
                            @include('layouts.partials.icon', ['name' => 'arrow-right', 'class' => 'h-3.5 w-3.5'])
                        </a>
                    @else
                        <a href="{{ route('tentor.qualifix') }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-bark-600 hover:text-ember-600 transition">
                            <span>Buka Halaman Qualifix Selengkapnya</span>
                            @include('layouts.partials.icon', ['name' => 'arrow-right', 'class' => 'h-3.5 w-3.5'])
                        </a>
                    @endif
                </div>
            @endif

            {{-- Akses Cepat Pengajar --}}
            <div class="rounded-2xl border border-bark-100 bg-white p-4">
                <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400 mb-3">Akses Cepat Pengajar</p>
                <ul class="space-y-0">
                    @php
                        $quickLinks = [
                            ['Lihat Kalender Jadwal Pekanan', 'calendar', route('tentor.absensi')],
                            ['Katalog Bank Soal & Modul', 'folder-open', route('tentor.materi')],
                            ['Daftar Pertanyaan Qualifix', 'message-circle', route('tentor.qualifix')],
                        ];
                    @endphp
                    @foreach ($quickLinks as [$label, $icon, $href])
                        <li>
                            <a href="{{ $href }}" class="flex items-center gap-3 px-1 py-3 text-[13px] font-medium text-bark-700 hover:text-ember-600 transition @if (!$loop->last) border-b border-bark-100 @endif">
                                @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4 text-bark-400'])
                                <span class="flex-1">{{ $label }}</span>
                                @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 text-bark-300'])
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const clockEl = document.getElementById('liveClock');
    if (!clockEl) return;

    function updateLiveClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
    }

    updateLiveClock();
    setInterval(updateLiveClock, 1000);
});
</script>
@endpush
@endsection
