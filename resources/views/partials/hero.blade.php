<section id="beranda" class="relative overflow-hidden bg-cream">
    {{-- Hangat khas QUALIFY: dua wash radial seperti pada rancangan --}}
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -top-40 left-[6%] h-[620px] w-[880px] rounded-full bg-[radial-gradient(closest-side,rgba(245,196,84,0.30),rgba(245,196,84,0))]"></div>
        <div class="absolute -bottom-56 -left-40 h-[560px] w-[760px] rounded-full bg-[radial-gradient(closest-side,rgba(238,216,172,0.55),rgba(238,216,172,0))]"></div>
        <div class="absolute -top-24 right-[8%] h-[460px] w-[620px] rounded-full bg-[radial-gradient(closest-side,rgba(245,196,84,0.16),rgba(245,196,84,0))]"></div>
    </div>

    <div class="relative mx-auto max-w-[1400px] px-6 lg:px-12 pt-12 lg:pt-20 pb-16 lg:pb-24">
        <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(0,0.7fr)] gap-10 xl:gap-16 items-start">
            {{-- Kolom kiri --}}
            <div class="max-w-[700px]">
                <p class="inline-flex items-center gap-2.5 bg-sand border border-line/80 rounded-full px-4 py-2.5 text-[11.5px] font-bold uppercase tracking-[0.11em] text-ink/85">
                    <span class="h-2 w-2 rounded-full bg-gold" aria-hidden="true"></span>
                    Penerimaan Siswa Baru TA 2025/2026
                </p>

                <h1 class="mt-7 text-[38px] sm:text-[46px] lg:text-[52px] font-extrabold leading-[1.2] tracking-[-0.02em] text-ink">
                    Raih Prestasi Akademik Impian Bersama
                    <span class="text-bronze underline decoration-bronze/90 decoration-[3px] underline-offset-[10px]">Bimbel</span>
                    <span class="text-bronze underline decoration-bronze/90 decoration-[3px] underline-offset-[10px]">QUALIFY</span>
                </h1>

                <p class="mt-6 text-[16px] lg:text-[17px] leading-[1.75] text-muted max-w-[560px]">
                    Bimbingan belajar personal dengan pendampingan tentor ahli, klinik konsultasi tugas sekolah
                    (Klinik PR), dan pemantauan belajar transparan untuk siswa SD, SMP, &amp; SMA.
                </p>

                <div class="mt-9 flex flex-wrap gap-4">
                    <a
                        href="#"
                        class="inline-flex items-center gap-2.5 bg-espresso hover:bg-[#3a2515] text-paper font-bold text-[15.5px] px-7 py-[17px] rounded-xl transition-colors"
                    >
                        Daftar Siswa Baru
                        @include('partials.icon', ['name' => 'arrow-right', 'size' => 18])
                    </a>
                    <a
                        href="{{ url('/#informasi-kelulusan') }}"
                        class="inline-flex items-center gap-2.5 bg-sand hover:bg-[#e9e6dd] border border-line text-ink font-bold text-[15.5px] px-7 py-[17px] rounded-xl transition-colors"
                    >
                        @include('partials.icon', ['name' => 'check-circle', 'size' => 18])
                        Lihat Informasi Kelulusan
                    </a>
                </div>

                <ul class="mt-10 flex flex-wrap gap-3">
                    <li class="inline-flex items-center gap-2.5 bg-sand rounded-xl px-4 py-3 text-[14px] font-semibold text-ink">
                        @include('partials.icon', ['name' => 'users', 'size' => 17, 'class' => 'shrink-0 text-espresso'])
                        1.200+ Siswa Terbimbing
                    </li>
                    <li class="inline-flex items-center gap-2.5 bg-sand rounded-xl px-4 py-3 text-[14px] font-semibold text-ink">
                        @include('partials.icon', ['name' => 'cap', 'size' => 17, 'class' => 'shrink-0 text-espresso'])
                        94.8% Lolos PTN &amp; Sekolah Favorit
                    </li>
                    <li class="inline-flex items-center gap-2.5 bg-sand rounded-xl px-4 py-3 text-[14px] font-semibold text-ink">
                        @include('partials.icon', ['name' => 'headset', 'size' => 17, 'class' => 'shrink-0 text-espresso'])
                        QualiFix Tanpa Biaya Tambahan
                    </li>
                </ul>
            </div>

            {{-- Kolom kanan: kartu unggulan --}}
            <div class="w-full max-w-[560px] lg:ml-auto">
                <div class="bg-paper rounded-[28px] border border-line/60 p-5 shadow-[0_24px_60px_-28px_rgba(42,26,14,0.35)]">
                    <div class="flex items-center justify-between gap-4 px-1">
                        <div class="flex items-center gap-3">
                            @include('partials.logo', ['size' => 40, 'variant' => 'default', 'uid' => 'hero-card', 'titleSize' => 'text-[17px]', 'sub' => 'Metode Penalaran Konseptual', 'subWrap' => 'max-w-none whitespace-nowrap'])
                        </div>
                        <span class="shrink-0 bg-sandgold text-[#7d5d12] font-bold text-[12.5px] px-3.5 py-2 rounded-full">
                            Terakreditasi A
                        </span>
                    </div>

                    <div class="relative mt-4 rounded-2xl overflow-hidden aspect-[16/10]">
                        <img
                            src="https://images.pexels.com/photos/5212648/pexels-photo-5212648.jpeg?auto=compress&cs=tinysrgb&w=1200"
                            alt="Sesi belajar bersama tentor di ruang kelas nyaman Bimbel QUALIFY"
                            class="absolute inset-0 h-full w-full object-cover"
                            width="1200"
                            height="750"
                            loading="eager"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-espresso/90 via-espresso/30 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-4">
                            <p class="text-[10.5px] font-bold uppercase tracking-[0.13em] text-gold">
                                Sesi Interaktif Terbimbing
                            </p>
                            <p class="mt-1 text-[16.5px] font-bold leading-snug text-white">
                                Ruang Belajar Nyaman &amp; Fasilitas Pendukung Berteknologi
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 bg-shell rounded-2xl px-4 py-3.5 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[13px] text-muted">Tingkat Kepuasan</p>
                            <p class="mt-1 flex items-baseline gap-1.5">
                                <span class="text-[30px] font-extrabold leading-none text-ink">4.9</span>
                                <span class="text-[14px] text-muted">/ 5.0 (Ortu &amp; Siswa)</span>
                            </p>
                        </div>
                        @include('partials.icon', ['name' => 'star', 'size' => 20, 'class' => 'shrink-0 text-espresso'])
                    </div>
                </div>

                <div class="mt-5 bg-espresso rounded-2xl px-5 py-4 flex items-center gap-4">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gold text-espresso">
                        @include('partials.icon', ['name' => 'book-open', 'size' => 20, 'class' => 'shrink-0'])
                    </span>
                    <div>
                        <p class="text-[16.5px] font-bold text-white leading-tight">QualiFix!</p>
                        <p class="text-[13.5px] text-white/70 mt-0.5">Setiap Hari Senin - Sabtu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
