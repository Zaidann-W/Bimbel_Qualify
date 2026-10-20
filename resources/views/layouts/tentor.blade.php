<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Tentor') — Qualify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bark-50 font-sans text-bark-800 antialiased">
<div class="flex min-h-screen p-0 lg:p-3">

    {{-- Sidebar --}}
    <aside class="hidden w-[240px] shrink-0 flex-col rounded-2xl bg-bark-800 px-4 py-6 lg:flex">

        {{-- Logo --}}
        <div class="flex items-center gap-2.5 px-1">
            <div class="grid h-9 w-9 place-items-center rounded-full bg-white text-sm font-bold text-bark-800">Q</div>
            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-wide text-white">QUALIFY</p>
                <p class="text-[9px] font-medium uppercase tracking-[0.18em] text-bark-300">Bimbel Portal</p>
            </div>
        </div>

        {{-- Role badge --}}
        <span class="mt-5 w-fit rounded-md bg-ember-400 px-2 py-[3px] text-[10px] font-bold text-bark-900">Admin</span>

        {{-- Nav --}}
        <nav class="mt-3 space-y-0.5 text-[13px]">
            @php
                $nav = [
                    ['Dashboard Overview', 'layout-grid', true, null],
                    ['Absensi Siswa', 'clipboard-check', false, null],
                    ['Qualify', 'message-circle', false, 3],
                    ['Materi Pembelajaran', 'book-open', false, null],
                    ['Profil Saya', 'users', false, null],
                ];
            @endphp
            @foreach ($nav as [$label, $icon, $active, $count])
                <a href="#"
                   @class([
                       'flex items-center gap-3 rounded-lg px-2.5 py-2.5 transition',
                       'bg-white font-semibold text-bark-800' => $active,
                       'font-medium text-bark-200 hover:bg-bark-700 hover:text-white' => ! $active,
                   ])>
                    @include('layouts.partials.icon', ['name' => $icon, 'class' => $active ? 'text-bark-800' : 'text-bark-300'])
                    <span class="flex-1">{{ $label }}</span>
                    @if ($count)
                        <span @class([
                            'grid h-5 w-5 place-items-center rounded-full text-[10px] font-bold',
                            'bg-bark-800 text-white' => $active,
                            'bg-bark-600 text-bark-100' => ! $active,
                        ])>{{ $count }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- User card --}}
        <div class="mt-auto rounded-xl bg-bark-700 px-3 py-3">
            <div class="flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-ember-400"></span>
                <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-bark-200">Tentor</p>
            </div>
            <p class="mt-0.5 text-[13px] font-semibold text-white">Farhan Kamil</p>
        </div>
    </aside>

    {{-- Main column --}}
    <div class="flex min-w-0 flex-1 flex-col">

        {{-- Topbar --}}
        <header class="flex items-center gap-4 px-4 py-4 sm:px-6">
            <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-bark-100 bg-white text-bark-500 lg:hidden" aria-label="Buka menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>

            <div class="relative flex-1 max-w-sm">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                <input type="search" placeholder="Cari siswa, NIS, kelas, transaksi…"
                       class="w-full rounded-full border border-bark-100 bg-white py-2.5 pl-9 pr-3 text-[13px] text-bark-800 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
            </div>

            <div class="ml-auto flex items-center gap-3">
                <button type="button" class="relative grid h-9 w-9 place-items-center rounded-lg text-bark-500 hover:bg-bark-100" aria-label="Notifikasi">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-2.1A2 2 0 0 1 18 13.8V10a6 6 0 1 0-12 0v3.8a2 2 0 0 1-.6 1.1L4 17h5m6 0a3 3 0 1 1-6 0"/></svg>
                    <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-ember-400 ring-2 ring-bark-50"></span>
                </button>
                <div class="flex items-center gap-2">
                    <div class="grid h-9 w-9 place-items-center rounded-full bg-ember-100 text-[12px] font-bold text-ember-700">KH</div>
                    <div class="hidden leading-tight sm:block">
                        <p class="text-[13px] font-semibold text-bark-800">Kak Hendra</p>
                        <p class="text-[11px] text-bark-400">Tentor</p>
                    </div>
                    <svg class="hidden h-4 w-4 text-bark-400 sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
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
