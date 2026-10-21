<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Pemilik') — Qualify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">
<div class="flex min-h-screen p-0 lg:h-screen lg:overflow-hidden">

    {{-- Sidebar --}}
    <aside id="owner-sidebar"
           class="hidden w-[240px] shrink-0 flex-col overflow-y-auto bg-espresso px-4 py-6 lg:sticky lg:top-0 lg:flex lg:h-screen">

        {{-- Logo --}}
        <div class="flex items-center gap-2.5 px-1">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'h-5 w-5'])
            </span>
            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-wide text-white">QUALIFY</p>
                <p class="text-[9px] font-medium uppercase tracking-[0.18em] text-bark-300">Owner Portal</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="mt-6 space-y-0.5 text-[13px]">
            <p class="mb-2 px-2.5 text-[9px] font-bold uppercase tracking-[0.15em] text-bark-300">Menu Utama</p>
            @php
                $nav = [
                    ['Dashboard', 'layout-grid', route('owner.dashboard'), request()->routeIs('owner.dashboard')],
                    ['Laporan Keuangan', 'file-text', route('owner.laporanKeuangan'), request()->routeIs('owner.laporanKeuangan')],
                    ['Laporan Absensi', 'clipboard-check', '#', false],
                    ['Pemantauan Materi', 'book-open', '#', false],
                    ['Pengaturan Website & Profil', 'sliders', '#', false],
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
            <div class="flex items-center gap-2.5">
                <span class="relative grid h-9 w-9 shrink-0 place-items-center rounded-full bg-palegold text-[11px] font-bold text-espresso">
                    IH
                    <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-gold ring-2 ring-espresso"></span>
                </span>
                <div class="min-w-0 flex-1 leading-tight">
                    <p class="text-[12px] font-bold leading-snug text-white">Ir. H. Hendra Gunawan</p>
                    <p class="mt-0.5 text-[10px] text-bark-300">Owner &amp; Founder</p>
                </div>
                <button type="button" aria-label="Keluar dari portal" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg text-bark-300 transition hover:bg-white/10 hover:text-white">
                    @include('layouts.partials.icon', ['name' => 'log-out', 'class' => 'h-4 w-4'])
                </button>
            </div>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div id="owner-backdrop" class="fixed inset-0 z-30 hidden bg-espresso/50 lg:hidden"></div>

    {{-- Main column --}}
    <div class="flex min-w-0 flex-1 flex-col lg:overflow-y-auto">

        {{-- Topbar --}}
        <header class="flex flex-wrap items-center gap-x-3 gap-y-2 border-b border-line bg-white px-4 py-3 sm:px-6">
            <button type="button" data-sidebar-toggle aria-controls="owner-sidebar" aria-expanded="false" aria-label="Buka menu"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-500 lg:hidden">
                @include('layouts.partials.icon', ['name' => 'menu', 'class' => 'h-5 w-5'])
            </button>

            <div class="flex min-w-0 items-center gap-2.5">
                <p class="truncate text-[12.5px] font-semibold text-ink">Bimbel QUALIFY</p>
                @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                <p class="truncate text-[12.5px] text-muted">Executive Center</p>
                <span class="hidden items-center gap-1.5 rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold text-bronze md:inline-flex">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                    TA 2024/2025 Semester Genap
                </span>
            </div>

            <div class="ml-auto flex min-w-0 shrink items-center justify-end gap-2 sm:gap-3">
                <button type="button" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-line bg-white px-2.5 py-2 text-[12px] font-semibold text-espresso transition hover:bg-sand">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4 text-bronze'])
                    <span class="hidden sm:inline">Hari ini: 24 Mei 2025</span>
                    <span class="sm:hidden">24 Mei 2025</span>
                    @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5 text-bark-400'])
                </button>

                <button type="button" class="relative grid h-9 w-9 shrink-0 place-items-center rounded-lg text-bark-500 transition hover:bg-bark-50" aria-label="Notifikasi">
                    @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-5 w-5'])
                    <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-ember-500 ring-2 ring-white"></span>
                </button>

                <div class="flex shrink-0 items-center gap-2">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-gold text-[11px] font-bold text-espresso">IH</span>
                    <div class="hidden leading-tight sm:block">
                        <p class="whitespace-nowrap text-[12.5px] font-semibold text-ink">Ir. H. Hendra G.</p>
                        <p class="text-[10.5px] text-muted">Owner</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>
    </div>
</div>

<script>
    (() => {
        const sidebar = document.getElementById('owner-sidebar');
        const backdrop = document.getElementById('owner-backdrop');
        const toggles = document.querySelectorAll('[data-sidebar-toggle]');

        const setMenuOpen = (open) => {
            sidebar.classList.toggle('hidden', !open);
            sidebar.classList.toggle('flex', open);
            backdrop.classList.toggle('hidden', !open);
            document.body.classList.toggle('overflow-hidden', open);
            toggles.forEach((button) => button.setAttribute('aria-expanded', String(open)));
        };

        toggles.forEach((button) => button.addEventListener('click', () => setMenuOpen(sidebar.classList.contains('hidden'))));
        backdrop.addEventListener('click', () => setMenuOpen(false));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setMenuOpen(false);
            }
        });
        window.addEventListener('resize', () => setMenuOpen(false));
    })();
</script>
</body>
</html>
