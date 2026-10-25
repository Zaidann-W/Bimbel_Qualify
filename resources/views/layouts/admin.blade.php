<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Admin') — Qualify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">
<div class="flex min-h-screen p-0 lg:h-screen lg:overflow-hidden print:h-auto print:overflow-visible">

    {{-- Sidebar --}}
    <aside id="admin-sidebar"
           class="hidden w-[240px] shrink-0 flex-col overflow-y-auto bg-espresso px-4 py-6 lg:sticky lg:top-0 lg:flex lg:h-screen print:hidden">

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
        <span class="mt-5 w-fit rounded-md bg-gold px-2 py-[3px] text-[10px] font-bold text-espresso">Admin</span>

        {{-- Nav --}}
        <nav class="mt-5 space-y-1 text-[13px]">
            @php
                $nav = [
                    ['Dashboard Overview', 'layout-grid', route('admin.dashboard'), 'admin.dashboard', null],
                    ['Pendaftaran Siswa', 'user-plus', route('admin.pendaftaran'), 'admin.pendaftaran*', '12'],
                    ['Data Siswa & Kelas', 'users', route('admin.dataSiswa'), 'admin.dataSiswa', null],
                    ['Keuangan & Transaksi', 'wallet', route('admin.keuangan'), 'admin.keuangan', null],
                    ['Manajemen Materi Ajar', 'book-open', route('admin.manajemenMateri'), 'admin.manajemenMateri', null],
                    ['Absensi & Jadwal', 'clipboard-check', route('admin.absensiJadwal'), 'admin.absensiJadwal', null],
                    ['Klinik PR', 'help-circle', '#', null, '5'],
                    ['Laporan & Pengaturan', 'sliders', '#', null, null],
                ];
            @endphp
            @foreach ($nav as [$label, $icon, $href, $routeName, $badge])
                @php
                    $active = $routeName && request()->routeIs($routeName);
                @endphp
                <a href="{{ $href }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-3 py-2.5 transition',
                       'bg-white font-semibold text-espresso' => $active,
                       'font-medium text-bark-200 hover:bg-white/10 hover:text-white' => ! $active,
                   ])>
                    @include('layouts.partials.icon', ['name' => $icon, 'class' => $active ? 'text-espresso' : 'text-bark-300'])
                    <span class="flex-1 leading-snug">{{ $label }}</span>
                    @if ($badge)
                        <span class="shrink-0 rounded-md bg-gold px-1.5 py-0.5 text-[10px] font-bold leading-none text-espresso">{{ $badge }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- User card --}}
        <div class="mt-auto rounded-xl bg-white/5 px-3 py-3">
            <div class="flex items-center gap-2.5">
                <span class="min-w-0 flex-1 leading-tight">
                    <span class="flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                        <span class="text-[9.5px] font-bold uppercase tracking-[0.14em] text-bark-300">Admin</span>
                    </span>
                    <span class="mt-1 block truncate text-[12.5px] font-semibold text-white">Farhan Kamil</span>
                </span>
                <a href="{{ route('login') }}" aria-label="Keluar dari portal"
                   class="grid h-7 w-7 shrink-0 place-items-center rounded-lg text-bark-300 transition hover:bg-white/10 hover:text-white">
                    @include('layouts.partials.icon', ['name' => 'log-out', 'class' => 'h-4 w-4'])
                </a>
            </div>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div id="admin-backdrop" class="fixed inset-0 z-30 hidden bg-espresso/50 lg:hidden print:hidden"></div>

    {{-- Main column --}}
    <div id="admin-utama" class="flex min-w-0 flex-1 flex-col lg:overflow-y-auto print:overflow-visible">

        {{-- Topbar --}}
        <header class="flex flex-wrap items-center gap-x-3 gap-y-2 border-b border-line bg-white px-4 py-3 sm:px-6 print:hidden">
            <button type="button" data-sidebar-toggle aria-controls="admin-sidebar" aria-expanded="false" aria-label="Buka menu"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-500 lg:hidden">
                @include('layouts.partials.icon', ['name' => 'menu', 'class' => 'h-5 w-5'])
            </button>

            {{-- Pencarian global --}}
            <form role="search" action="#" class="block min-w-0 flex-1">
                <div class="relative w-full max-w-xl">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                        @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                    </span>
                    <input type="search" name="q" aria-label="Cari siswa, NIS, kelas, atau transaksi"
                           placeholder="Cari siswa, NIS, kelas, transaksi..."
                           class="w-full rounded-lg border border-transparent bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition hover:bg-sand focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                </div>
            </form>

            <div class="ml-auto flex min-w-0 shrink items-center justify-end gap-2 sm:gap-3">
                <button type="button" class="relative grid h-9 w-9 shrink-0 place-items-center rounded-lg text-bark-500 transition hover:bg-bark-50" aria-label="Notifikasi">
                    @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-5 w-5'])
                    <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-ember-500 ring-2 ring-white"></span>
                </button>

                <span class="hidden h-5 w-px bg-line sm:block"></span>

                <button type="button" class="flex shrink-0 items-center gap-2.5 rounded-lg pr-1 text-left transition hover:bg-bark-50"
                        aria-label="Menu akun Farhan Kamil">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-palegold text-[11.5px] font-bold text-espresso"
                          title="Ust. Farhan Kamil — Branch Director / Admin">
                        FK
                    </span>
                    <span class="hidden leading-tight sm:block">
                        <span class="block whitespace-nowrap text-[12.5px] font-semibold text-ink">Ust. Farhan Kamil</span>
                        <span class="block text-[10.5px] text-muted">Branch Director / Admin</span>
                    </span>
                    @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'hidden h-3.5 w-3.5 text-bark-400 sm:block'])
                </button>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>
    </div>
</div>

<script>
    (() => {
        const sidebar = document.getElementById('admin-sidebar');
        const backdrop = document.getElementById('admin-backdrop');
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
