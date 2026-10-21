<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Siswa') — Qualify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bark-50 font-sans text-bark-800 antialiased">
<div class="flex min-h-screen p-0 lg:h-screen lg:overflow-hidden">

    {{-- Sidebar --}}
    <aside class="hidden w-[240px] shrink-0 flex-col overflow-y-auto bg-espresso px-4 py-6 lg:sticky lg:top-0 lg:flex lg:h-screen">

        {{-- Logo --}}
        <div class="flex items-center gap-2.5 px-1">
            <div class="grid h-9 w-9 place-items-center rounded-full bg-white text-sm font-bold text-espresso">Q</div>
            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-wide text-white">QUALIFY</p>
                <p class="text-[9px] font-medium uppercase tracking-[0.18em] text-bark-300">Bimbel Portal</p>
            </div>
        </div>

        {{-- Role badge --}}
        <span class="mt-5 w-fit rounded-md bg-gold px-2 py-[3px] text-[10px] font-bold text-espresso">Siswa SMA 12 IPA</span>

        {{-- Nav --}}
        <nav class="mt-5 space-y-0.5 text-[13px]">
            <p class="mb-2 px-2.5 text-[9px] font-bold uppercase tracking-[0.15em] text-bark-300">Menu Operasional</p>
            @php
                $nav = [
                    ['Dashboard', 'layout-grid', route('siswa.dashboard'), request()->routeIs('siswa.dashboard')],
                    ['Jadwal Belajar', 'calendar', route('siswa.jadwal.detail'), request()->routeIs('siswa.jadwal*')],
                    ['Materi Pembelajaran', 'book-open', '#', false],
                    ['Qualify', 'message-circle', '#', false],
                    ['Profil Saya', 'users', '#', false],
                ];
            @endphp
            @foreach ($nav as [$label, $icon, $href, $active])
                <a href="{{ $href }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-2.5 py-2.5 transition',
                       'bg-white font-semibold text-espresso' => $active,
                       'font-medium text-bark-200 hover:bg-white/10 hover:text-white' => ! $active,
                   ])>
                    @include('layouts.partials.icon', ['name' => $icon, 'class' => $active ? 'text-espresso' : 'text-bark-300'])
                    <span class="flex-1">{{ $label }}</span>
                </a>
            @endforeach
        </nav>

        {{-- User card --}}
        <div class="mt-auto rounded-xl bg-white/5 px-3 py-3">
            <div class="flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-bark-200">Berlangganan</p>
            </div>
            <p class="mt-1 text-[11px] font-bold uppercase tracking-wide text-white">Aisyah Putri Azzahra</p>
            <p class="mt-0.5 text-[10px] text-bark-300">NISN 0098123471</p>
        </div>
    </aside>

    {{-- Main column --}}
    <div class="flex min-w-0 flex-1 flex-col lg:overflow-y-auto lg:p-3">

        {{-- Topbar --}}
        <header class="flex items-center gap-4 px-4 py-4 sm:px-6">
            <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-bark-100 bg-white text-bark-500 lg:hidden" aria-label="Buka menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>

            <div class="relative flex-1 max-w-sm">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                <input type="search" placeholder="Cari materi, jadwal, pertanyaan PR…"
                       class="w-full rounded-full border border-bark-100 bg-white py-2.5 pl-9 pr-3 text-[13px] text-bark-800 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
            </div>

            <div class="ml-auto flex items-center gap-3">
                <button type="button" class="relative grid h-9 w-9 place-items-center rounded-lg text-bark-500 hover:bg-bark-100" aria-label="Notifikasi">
                    @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-5 w-5'])
                    <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-ember-400 ring-2 ring-bark-50"></span>
                </button>
                <div class="flex items-center gap-2">
                    <div class="grid h-9 w-9 place-items-center rounded-full bg-ember-100 text-[12px] font-bold text-ember-700">AP</div>
                    <div class="hidden leading-tight sm:block">
                        <p class="text-[13px] font-semibold text-bark-800">Aisyah Putri Azzahra</p>
                        <p class="text-[11px] text-bark-400">SMA Kelas 12 IPA</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 pb-6 sm:px-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
