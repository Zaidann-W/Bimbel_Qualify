@extends('layouts.siswa')

@section('title', 'Jadwal Saya')

@section('content')
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

    @if ($todaySessions->isEmpty() && $upcomingSessions->isEmpty() && $pastSessions->isEmpty())
        <div class="rounded-2xl border border-bark-100 bg-white px-5 py-12 text-center">
            <p class="text-[14px] font-bold text-bark-700">Belum ada sesi belajar yang terjadwal.</p>
            <p class="mt-1.5 text-[12.5px] text-bark-400">Jadwal kelas akan muncul di sini setelah diterbitkan.</p>
        </div>
    @else

        {{-- Sesuai hari ini --}}
        @if ($todaySessions->isNotEmpty())
            <section class="space-y-4">
                <h2 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Hari Ini</h2>
                <ul class="space-y-3">
                    @foreach ($todaySessions as $session)
                        <li><x-jadwal-sesi-baris :session="$session" highlight /></li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Sesi berikutnya --}}
        @if ($upcomingSessions->isNotEmpty())
            <section class="space-y-4">
                <h2 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Selanjutnya</h2>
                <ul class="space-y-3">
                    @foreach ($upcomingSessions as $session)
                        <li><x-jadwal-sesi-baris :session="$session" /></li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Sesi yang sudah lewat --}}
        @if ($pastSessions->isNotEmpty())
            <section class="space-y-4">
                <h2 class="text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-400">Sebelumnya</h2>
                <ul class="space-y-3">
                    @foreach ($pastSessions as $session)
                        <li><x-jadwal-sesi-baris :session="$session" /></li>
                    @endforeach
                </ul>
            </section>
        @endif

    @endif
</div>
@endsection
