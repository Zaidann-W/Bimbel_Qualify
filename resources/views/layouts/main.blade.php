<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name', 'Bimbel QUALIFY') . ' — Lembaga Bimbingan Belajar')</title>
        <meta
            name="description"
            content="@yield('meta_description', 'Bimbel QUALIFY — bimbingan belajar personal untuk siswa SD, SMP, & SMA dengan tentor ahli, Klinik PR QualiFix, dan pemantauan belajar transparan.')"
        >

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-cream text-ink">
        @include('partials.header')

        <main>
            @yield('content')
        </main>

        @include('partials.footer')

        <script>
            (function () {
                var toggle = document.getElementById('menu-toggle');
                var menu = document.getElementById('mobile-menu');

                if (!toggle || !menu) {
                    return;
                }

                toggle.addEventListener('click', function () {
                    var isOpen = !menu.classList.contains('hidden');
                    menu.classList.toggle('hidden', isOpen);
                    toggle.setAttribute('aria-expanded', String(!isOpen));
                    toggle.setAttribute('aria-label', isOpen ? 'Buka menu navigasi' : 'Tutup menu navigasi');
                });

                menu.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        menu.classList.add('hidden');
                        toggle.setAttribute('aria-expanded', 'false');
                    });
                });
            })();
        </script>

        @stack('scripts')
    </body>
</html>
