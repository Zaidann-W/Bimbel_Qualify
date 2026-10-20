<footer class="bg-shell border-t border-line/70 pt-16 pb-8">
    <div class="mx-auto max-w-[1400px] px-6 lg:px-12">
        <div class="grid gap-12 lg:grid-cols-[1.45fr_1fr_1.25fr] xl:gap-16">
            {{-- Identitas --}}
            <div>
                @include('partials.logo', ['size' => 46, 'variant' => 'default', 'uid' => 'footer'])
                <p class="mt-5 text-[15px] leading-[1.75] text-muted max-w-[440px]">
                    Mewujudkan impian siswa meraih kelulusan ke perguruan tinggi negeri terbaik dan sekolah kedinasan
                    unggulan dengan bimbingan terstruktur, tutor berdedikasi, dan tryout analitis presisi.
                </p>
                <div class="mt-6 flex gap-3">
                    <a
                        href="#"
                        aria-label="Program studi"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sand border border-line text-ink/70 hover:text-ink hover:bg-paper transition-colors"
                    >
                        @include('partials.icon', ['name' => 'graduation', 'size' => 17])
                    </a>
                    <a
                        href="#"
                        aria-label="Metode belajar"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sand border border-line text-ink/70 hover:text-ink hover:bg-paper transition-colors"
                    >
                        @include('partials.icon', ['name' => 'lightbulb', 'size' => 17])
                    </a>
                    <a
                        href="#"
                        aria-label="Prestasi siswa"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sand border border-line text-ink/70 hover:text-ink hover:bg-paper transition-colors"
                    >
                        @include('partials.icon', ['name' => 'trophy', 'size' => 17])
                    </a>
                </div>
            </div>

            {{-- Navigasi --}}
            <nav aria-label="Navigasi pintar">
                <h2 class="text-[19px] font-bold text-ink">Navigasi Pintar</h2>
                <ul class="mt-5 space-y-3.5 text-[15px] text-muted">
                    <li><a href="{{ url('/#beranda') }}" class="hover:text-ink transition-colors">Beranda Utama</a></li>
                    <li><a href="{{ url('/#tentang-kami') }}" class="hover:text-ink transition-colors">Profil &amp; Visi Bimbel</a></li>
                    <li><a href="{{ url('/#keunggulan') }}" class="hover:text-ink transition-colors">Metode &amp; Keunggulan Belajar</a></li>
                    <li><a href="{{ url('/#testimoni') }}" class="hover:text-ink transition-colors">Kisah Sukses &amp; Testimoni</a></li>
                    <li><a href="{{ url('/#informasi-kelulusan') }}" class="hover:text-ink transition-colors">Statistik &amp; Informasi Kelulusan</a></li>
                </ul>
            </nav>

            {{-- Kontak --}}
            <div>
                <h2 class="text-[19px] font-bold text-ink">Kontak &amp; Kampus</h2>
                <ul class="mt-5 space-y-4 text-[15px] text-muted">
                    <li class="flex gap-3 items-start">
                        @include('partials.icon', ['name' => 'pin', 'size' => 17, 'class' => 'shrink-0 mt-0.5 text-ink/60'])
                        <span>Gedung Edukatif QUALIFY Lt. 1–3,<br>Jl. Pendidikan Mandiri No. 45,<br>Jakarta Selatan 12430</span>
                    </li>
                    <li class="flex gap-3 items-center">
                        @include('partials.icon', ['name' => 'phone', 'size' => 17, 'class' => 'shrink-0 text-ink/60'])
                        <a href="tel:+622178904321" class="hover:text-ink transition-colors">(021) 7890–4321</a>
                    </li>
                    <li class="flex gap-3 items-center">
                        @include('partials.icon', ['name' => 'mail', 'size' => 17, 'class' => 'shrink-0 text-ink/60'])
                        <a href="mailto:halo@qualify-bimbel.id" class="hover:text-ink transition-colors">halo@qualify-bimbel.id</a>
                    </li>
                    <li class="flex gap-3 items-center">
                        @include('partials.icon', ['name' => 'clock', 'size' => 17, 'class' => 'shrink-0 text-ink/60'])
                        <span>Senin - Sabtu: 08:00 - 20:30 WIB</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-14 border-t border-line pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-[13.5px] text-muted">
            <p>© 2025 Lembaga Bimbingan Belajar QUALIFY. Hak Cipta Dilindungi.</p>
            <div class="flex flex-wrap justify-center gap-7">
                <a href="#" class="hover:text-ink transition-colors">Kebijakan Privasi</a>
                <a href="#" class="hover:text-ink transition-colors">Syarat &amp; Ketentuan</a>
                <a href="#" class="hover:text-ink transition-colors">Pusat Bantuan</a>
            </div>
        </div>
    </div>
</footer>
