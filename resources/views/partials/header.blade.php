<header class="bg-paper border-b border-line/70 relative z-50">
    <div class="mx-auto max-w-[1400px] px-6 lg:px-12">
        <div class="flex items-center gap-6 h-[92px]">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="shrink-0" aria-label="Bimbel QUALIFY — beranda">
                @include('partials.logo', ['size' => 46, 'variant' => 'default', 'uid' => 'header'])
            </a>

            {{-- Navigasi desktop --}}
            <nav class="hidden lg:flex flex-1 justify-center" aria-label="Navigasi utama">
                <ul class="flex items-center gap-1 bg-sand rounded-full p-1.5">
                    <li>
                        <a
                            href="{{ url('/#beranda') }}"
                            class="block px-5 py-2.5 rounded-full text-[15px] font-semibold bg-paper text-ink shadow-[0_1px_3px_rgba(42,26,14,0.10)]"
                            aria-current="page"
                        >
                            Beranda
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ url('/#tentang-kami') }}"
                            class="block px-5 py-2.5 rounded-full text-[15px] font-medium text-ink/75 hover:text-ink transition-colors"
                        >
                            Tentang Kami
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ url('/#keunggulan') }}"
                            class="block px-5 py-2.5 rounded-full text-[15px] font-medium text-ink/75 hover:text-ink transition-colors"
                        >
                            Keunggulan
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ url('/#testimoni') }}"
                            class="block px-5 py-2.5 rounded-full text-[15px] font-medium text-ink/75 hover:text-ink transition-colors"
                        >
                            Testimoni
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ url('/#informasi-kelulusan') }}"
                            class="block px-5 py-2.5 rounded-full text-[15px] font-medium text-ink/75 hover:text-ink transition-colors"
                        >
                            Informasi Kelulusan
                        </a>
                    </li>
                </ul>
            </nav>

            {{-- Aksi --}}
            <div class="hidden lg:flex items-center gap-7 ml-auto shrink-0">
                <a href="#" class="text-[15px] font-semibold text-ink hover:text-bronze transition-colors">
                    Masuk / Login
                </a>
                <a
                    href="{{ route('registrasi') }}"
                    class="inline-flex items-center bg-gold hover:bg-[#ecab20] text-espresso font-bold text-[15px] px-6 py-3.5 rounded-xl transition-colors"
                >
                    Daftar Sekarang
                </a>
            </div>

            {{-- Toggle menu mobile --}}
            <button
                type="button"
                id="menu-toggle"
                class="lg:hidden ml-auto inline-flex h-11 w-11 items-center justify-center rounded-xl bg-sand text-ink"
                aria-expanded="false"
                aria-controls="mobile-menu"
                aria-label="Buka menu navigasi"
            >
                @include('partials.icon', ['name' => 'menu', 'size' => 22])
            </button>
        </div>
    </div>

    {{-- Menu mobile --}}
    <div id="mobile-menu" class="hidden lg:hidden border-t border-line bg-paper">
        <nav class="mx-auto max-w-[1400px] px-6 py-5 flex flex-col gap-1" aria-label="Navigasi seluler">
            <a href="{{ url('/#beranda') }}" class="py-2.5 text-[15px] font-semibold text-ink">Beranda</a>
            <a href="{{ url('/#tentang-kami') }}" class="py-2.5 text-[15px] text-muted hover:text-ink">Tentang Kami</a>
            <a href="{{ url('/#keunggulan') }}" class="py-2.5 text-[15px] text-muted hover:text-ink">Keunggulan</a>
            <a href="{{ url('/#testimoni') }}" class="py-2.5 text-[15px] text-muted hover:text-ink">Testimoni</a>
            <a href="{{ url('/#informasi-kelulusan') }}" class="py-2.5 text-[15px] text-muted hover:text-ink">Informasi Kelulusan</a>
            <div class="mt-3 flex items-center gap-4">
                <a href="#" class="text-[15px] font-semibold text-ink">Masuk / Login</a>
                <a href="{{ route('registrasi') }}" class="inline-flex items-center bg-gold text-espresso font-bold text-[15px] px-5 py-3 rounded-xl">
                    Daftar Sekarang
                </a>
            </div>
        </nav>
    </div>
</header>
