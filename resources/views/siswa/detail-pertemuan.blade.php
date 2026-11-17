@extends('layouts.siswa')

@section('title', 'Jadwal Saya')

@section('content')
@php
    /**
     * Format satu baris jadwal kelas (ClassSession) menjadi kartu.
     * Data: tanggal, jam, mata pelajaran, topik, tentor, dan ruang.
     */
    $formatJadwalKelas = function (App\Models\ClassSession $session): array {
        $tanggal = \Carbon\Carbon::parse($session->session_date);

        $jam = sprintf(
            '%s – %s WIB',
            \Illuminate\Support\Str::of((string) $session->start_time)->substr(0, 5)->replace(':', '.'),
            \Illuminate\Support\Str::of((string) $session->end_time)->substr(0, 5)->replace(':', '.'),
        );

        $ruang = trim(sprintf('%s %s', $session->room?->name ?? '', $session->room?->floor ?? ''));

        return [
            'hari' => \Illuminate\Support\Str::upper($tanggal->locale('id')->translatedFormat('D')),
            'tgl' => $tanggal->format('j'),
            'mapel' => $session->subject?->name ?? 'Mata Pelajaran',
            'topik' => $session->topic ?? '-',
            'meta' => implode(' · ', array_filter([
                $jam,
                $session->staff?->full_name_with_title,
                $ruang !== '' ? $ruang : null,
            ])),
        ];
    };
@endphp

<div class="w-full max-w-[1000px] space-y-10">

    {{-- Konteks akademik & judul halaman --}}
    <header class="space-y-3">
        @if ($contextLabel !== '')
            <p class="inline-flex items-center gap-2 rounded-full border border-gold/60 bg-sandgold px-3.5 py-1.5 text-[11.5px] font-bold text-bark-700">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                {{ $contextLabel }}
            </p>
        @endif
        <h1 class="text-[26px] font-extrabold tracking-tight text-bark-800">Jadwal Belajar Siswa</h1>
    </header>

    @php
        $totalJadwalKelas = $todayClassSessions->count() + $upcomingClassSessions->count() + $pastClassSessions->count();
        $totalSesiBelajar = $todaySessions->count() + $upcomingSessions->count() + $pastSessions->count();
        $kosongSemua = $totalJadwalKelas === 0 && $totalSesiBelajar === 0;
    @endphp

    @if ($kosongSemua)
        <div class="rounded-2xl border border-bark-100 bg-white px-5 py-12 text-center">
            <p class="text-[14px] font-bold text-bark-700">Belum ada sesi belajar yang terjadwal.</p>
            <p class="mt-1.5 text-[12.5px] text-bark-400">Jadwal kelas akan muncul di sini setelah diterbitkan.</p>
        </div>
    @else

        {{-- ==================== JADWAL KELAS (ClassSession) ==================== --}}
        @if ($totalJadwalKelas > 0)
            <section class="space-y-4">
                <h2 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Jadwal Kelas</h2>

                @if ($todayClassSessions->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Hari Ini</h3>
                        <ul class="space-y-3">
                            @foreach ($todayClassSessions as $session)
                                @php $kartu = $formatJadwalKelas($session); @endphp
                                <li>
                                    <div class="group flex items-center gap-4 rounded-2xl border border-gold px-4 py-5 transition sm:px-5 sm:gap-5">
                                        <span class="grid h-14 w-16 shrink-0 place-items-center rounded-xl bg-gold text-center text-espresso">
                                            <span class="block whitespace-nowrap text-[9px] font-bold uppercase leading-none tracking-wide">Hari Ini</span>
                                            <span class="mt-1.5 block text-[18px] font-extrabold leading-none">{{ $kartu['tgl'] }}</span>
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-[16px] font-bold leading-snug text-bark-800 sm:text-[18px]">{{ $kartu['mapel'] }}</span>
                                            <span class="mt-1 block text-[13.5px] font-semibold leading-snug text-ember-700">{{ $kartu['topik'] }}</span>
                                            <span class="mt-1.5 block text-[12.5px] leading-relaxed text-bark-400">{{ $kartu['meta'] }}</span>
                                        </span>
                                        <span class="hidden shrink-0 items-center gap-1.5 rounded-lg bg-espresso px-4 py-2.5 text-[12.5px] font-bold text-white transition group-hover:bg-bark-900 sm:inline-flex">
                                            Lihat detail
                                            @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($upcomingClassSessions->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Selanjutnya</h3>
                        <ul class="space-y-3">
                            @foreach ($upcomingClassSessions as $session)
                                @php $kartu = $formatJadwalKelas($session); @endphp
                                <li>
                                    <div class="flex items-center gap-4 rounded-2xl border border-bark-100 bg-white px-4 py-5 transition hover:border-bark-200 sm:px-5 sm:gap-5">
                                        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-bark-50 text-center text-bark-600">
                                            <span class="block whitespace-nowrap text-[9px] font-bold uppercase leading-none tracking-wide">{{ $kartu['hari'] }}</span>
                                            <span class="mt-1.5 block text-[18px] font-extrabold leading-none">{{ $kartu['tgl'] }}</span>
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-[16px] font-bold leading-snug text-bark-800 sm:text-[18px]">{{ $kartu['mapel'] }}</span>
                                            <span class="mt-1 block text-[13.5px] font-semibold leading-snug text-ember-700">{{ $kartu['topik'] }}</span>
                                            <span class="mt-1.5 block text-[12.5px] leading-relaxed text-bark-400">{{ $kartu['meta'] }}</span>
                                        </span>
                                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 shrink-0 text-bark-300 transition group-hover:text-bark-500'])
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($pastClassSessions->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Sebelumnya</h3>
                        <ul class="space-y-3">
                            @foreach ($pastClassSessions as $session)
                                @php $kartu = $formatJadwalKelas($session); @endphp
                                <li>
                                    <div class="flex items-center gap-4 rounded-2xl border border-bark-100 bg-white px-4 py-5 transition hover:border-bark-200 sm:px-5 sm:gap-5">
                                        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-bark-50 text-center text-bark-600">
                                            <span class="block whitespace-nowrap text-[9px] font-bold uppercase leading-none tracking-wide">{{ $kartu['hari'] }}</span>
                                            <span class="mt-1.5 block text-[18px] font-extrabold leading-none">{{ $kartu['tgl'] }}</span>
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-[16px] font-bold leading-snug text-bark-800 sm:text-[18px]">{{ $kartu['mapel'] }}</span>
                                            <span class="mt-1 block text-[13.5px] font-semibold leading-snug text-ember-700">{{ $kartu['topik'] }}</span>
                                            <span class="mt-1.5 block text-[12.5px] leading-relaxed text-bark-400">{{ $kartu['meta'] }}</span>
                                        </span>
                                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 shrink-0 text-bark-300 transition group-hover:text-bark-500'])
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>
        @endif

        {{-- ==================== SESI BELAJAR (StudySession) ==================== --}}
        @if ($totalSesiBelajar > 0)
            <section class="space-y-4">
                <h2 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Sesi Belajar</h2>

                {{-- Sesuai hari ini --}}
                @if ($todaySessions->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Hari Ini</h3>
                        <ul class="space-y-3">
                            @foreach ($todaySessions as $session)
                                <li><x-jadwal-sesi-baris :session="$session" highlight /></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Sesi berikutnya --}}
                @if ($upcomingSessions->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Selanjutnya</h3>
                        <ul class="space-y-3">
                            @foreach ($upcomingSessions as $session)
                                <li><x-jadwal-sesi-baris :session="$session" /></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Sesi yang sudah lewat --}}
                @if ($pastSessions->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Sebelumnya</h3>
                        <ul class="space-y-3">
                            @foreach ($pastSessions as $session)
                                <li><x-jadwal-sesi-baris :session="$session" /></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>
        @endif

    @endif
</div>
@endsection
