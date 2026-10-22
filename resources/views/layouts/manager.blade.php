<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Manajer') — Qualify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">
<div class="flex min-h-screen p-0 lg:h-screen lg:overflow-hidden">

    {{-- Sidebar --}}
    <aside id="manager-sidebar"
           class="hidden w-[240px] shrink-0 flex-col overflow-y-auto bg-espresso px-4 py-6 lg:sticky lg:top-0 lg:flex lg:h-screen">

        {{-- Logo --}}
        <div class="flex items-center gap-2.5 px-1">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'h-5 w-5'])
            </span>
            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-wide text-white">QUALIFY</p>
                <p class="text-[9px] font-medium uppercase tracking-[0.18em] text-bark-300">Bimbel Portal</p>
            </div>
        </div>

        {{-- Role badge --}}
        <span class="mt-5 w-fit rounded-md bg-gold px-2 py-[3px] text-[10px] font-bold text-espresso">Manajer Cabang</span>

        {{-- Nav --}}
        <nav class="mt-5 space-y-0.5 text-[13px]">
            <p class="mb-2 px-2.5 text-[9px] font-bold uppercase tracking-[0.15em] text-bark-300">Menu Operasional</p>
            @php
                // Halaman Ringkasan Operasional s/d Profil Saya menyusul; rute ditambahkan
                // saat modul masing-masing dibuat, lalu tautan diganti route().
                $nav = [
                    ['Dashboard', 'layout-grid', route('manager.dashboard'), request()->routeIs('manager.dashboard')],
                    ['Keuangan Bulanan', 'wallet', route('manager.keuanganBulanan'), request()->routeIs('manager.keuanganBulanan')],
                    ['Pemantauan Materi', 'book-open', route('manager.pemantauanMateri'), request()->routeIs('manager.pemantauanMateri')],
                    ['Ringkasan Operasional', 'clipboard-check', '#', false],
                    ['Profil Saya', 'user', '#', false],
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

        {{-- Kartu langganan cabang --}}
        <div class="mt-auto rounded-xl bg-white/5 px-3 py-3">
            <p class="flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-gold">
                <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                Berlangganan
            </p>
            <p class="mt-2 text-[12px] font-bold leading-snug text-white">Ir. Rahmat Hidayat, M.M.</p>
            <p class="mt-0.5 text-[10px] text-bark-300">NIP: 00124487</p>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div id="manager-backdrop" class="fixed inset-0 z-30 hidden bg-espresso/50 lg:hidden"></div>

    {{-- Main column --}}
    <div class="flex min-w-0 flex-1 flex-col lg:overflow-y-auto">

        {{-- Topbar --}}
        <header class="flex flex-wrap items-center gap-x-3 gap-y-2 border-b border-line bg-white px-4 py-3 sm:px-6">
            <button type="button" data-sidebar-toggle aria-controls="manager-sidebar" aria-expanded="false" aria-label="Buka menu"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-500 lg:hidden">
                @include('layouts.partials.icon', ['name' => 'menu', 'class' => 'h-5 w-5'])
            </button>

            {{-- Pencarian global --}}
            <form role="search" action="#" class="hidden min-w-0 flex-1 sm:block">
                <div class="relative w-full max-w-xl">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                        @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                    </span>
                    <input type="search" name="q" aria-label="Cari rombel, modul, atau laporan"
                           placeholder="Cari rombel, modul, pertanyaan PR..."
                           class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition hover:bg-sand focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                </div>
            </form>

            <div class="ml-auto flex min-w-0 shrink items-center justify-end gap-2 sm:gap-3">
                <button type="button" class="relative grid h-9 w-9 shrink-0 place-items-center rounded-lg text-bark-500 transition hover:bg-bark-50" aria-label="Notifikasi">
                    @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-5 w-5'])
                    <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-ember-500 ring-2 ring-white"></span>
                </button>

                <span class="hidden h-5 w-px bg-line sm:block"></span>

                <div class="flex shrink-0 items-center gap-2.5">
                    <div class="hidden leading-tight sm:block">
                        <p class="whitespace-nowrap text-[12.5px] font-semibold text-ink">Ir. Rahmat Hidayat, M.M.</p>
                        <p class="text-[10.5px] text-muted">Manajer Cabang Utama</p>
                    </div>
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-espresso text-gold"
                          title="Ir. Rahmat Hidayat, M.M. — Manajer Cabang Utama">
                        @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-4.5 w-4.5'])
                    </span>
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
        const sidebar = document.getElementById('manager-sidebar');
        const backdrop = document.getElementById('manager-backdrop');
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
