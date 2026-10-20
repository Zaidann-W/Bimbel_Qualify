<section id="informasi-kelulusan" class="bg-shell py-20 lg:py-28">
    <div class="mx-auto max-w-[1400px] px-6 lg:px-12">
        <div class="flex flex-wrap items-start justify-between gap-8">
            <div class="max-w-[640px]">
                <p class="text-[11.5px] font-bold uppercase tracking-[0.14em] text-bronze">
                    Bukti Riil Capaian
                </p>
                <h2 class="mt-4 text-[28px] lg:text-[34px] font-extrabold leading-[1.2] tracking-[-0.015em] text-ink">
                    Informasi Kelulusan &amp; Rekam Prestasi
                </h2>
                <p class="mt-4 text-[16px] leading-[1.75] text-muted">
                    Hasil nyata bimbingan intensif siswa-siswi Bimbel QUALIFY menembus seleksi masuk perguruan tinggi
                    negeri dan sekolah unggulan.
                </p>
            </div>

            <span class="inline-flex items-center gap-2.5 bg-paper border border-line rounded-full px-4 py-2.5 text-[14px] font-semibold text-ink shadow-[0_6px_18px_-12px_rgba(42,26,14,0.4)]">
                @include('partials.icon', ['name' => 'award', 'size' => 17, 'class' => 'shrink-0 text-bronze'])
                Terverifikasi TA 2023/2024
            </span>
        </div>

        {{-- Kartu tingkat keberhasilan --}}
        <div class="relative mt-10 overflow-hidden rounded-[28px] bg-espresso">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute inset-0 bg-gradient-to-br from-[#3a2513] via-espresso to-[#1f130a]"></div>
                <div class="absolute -right-10 top-1/2 h-[560px] w-[680px] -translate-y-1/2 rounded-full bg-[radial-gradient(closest-side,rgba(245,183,46,0.42),rgba(245,183,46,0))]"></div>
                <div class="absolute -left-24 -bottom-32 h-[420px] w-[520px] rounded-full bg-[radial-gradient(closest-side,rgba(146,103,30,0.35),rgba(146,103,30,0))]"></div>
            </div>

            {{-- Watermark logo emas (vertikal) --}}
            <div class="pointer-events-none absolute right-[5%] top-1/2 hidden -translate-y-1/2 lg:block opacity-90" aria-hidden="true">
                @include('partials.logo', ['size' => 150, 'variant' => 'gold', 'uid' => 'watermark', 'stack' => true, 'titleSize' => 'text-[54px]', 'subSize' => 'text-[12px]', 'subWrap' => 'max-w-none whitespace-nowrap', 'sub' => 'Lembaga Bimbingan Belajar'])
            </div>

            <div class="relative px-7 py-10 sm:px-10 lg:px-16 lg:py-16 max-w-[660px]">
                <p class="inline-block bg-gradient-to-r from-[#9a741a] to-[#caa02c] text-white font-bold text-[11px] uppercase tracking-[0.13em] px-4 py-2 rounded-full">
                    Tingkat Keberhasilan
                </p>

                <p class="mt-7 text-[58px] lg:text-[74px] font-extrabold leading-none tracking-[-0.02em] text-palegold">
                    94.8%
                </p>
                <h3 class="mt-4 text-[24px] lg:text-[27px] font-bold text-white leading-snug">
                    Siswa Diterima di Pilihan Pertama
                </h3>
                <p class="mt-4 text-[15.5px] leading-[1.75] text-[#c9bfb2] max-w-[540px]">
                    Meliputi jalur SNBP (Prestasi), SNBT (Tes UTBK), Mandiri PTN Favorit, serta Sekolah Kedinasan
                    &amp; SMA Unggulan Nasional.
                </p>

                <p class="mt-7 flex items-center gap-2.5 text-[13.5px] text-[#b3a797]">
                    @include('partials.icon', ['name' => 'info', 'size' => 16, 'class' => 'shrink-0'])
                    Data kelulusan terverifikasi periode Tahun Ajaran 2023/2024
                </p>
            </div>
        </div>

        {{-- Tiga kartu bukti --}}
        <div class="mt-8 grid md:grid-cols-3 gap-6">
            <div class="bg-paper border border-line/60 rounded-[24px] p-6 flex gap-4 items-start">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-sandgold text-espresso">
                    @include('partials.icon', ['name' => 'trending-up', 'size' => 21])
                </span>
                <div>
                    <h3 class="text-[17.5px] font-bold text-ink leading-snug">SNBP &amp; SNBT</h3>
                    <p class="mt-1.5 text-[14.5px] leading-[1.65] text-muted">
                        Lulusan Dominan Lulus dengan jalur SNBP &amp; SNBT
                    </p>
                </div>
            </div>

            <div class="bg-paper border border-line/60 rounded-[24px] p-6 flex gap-4 items-start">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-sandgold text-espresso">
                    @include('partials.icon', ['name' => 'trophy', 'size' => 21])
                </span>
                <div>
                    <h3 class="text-[17.5px] font-bold text-ink leading-snug">100% Lulus ASPD / UN SMP</h3>
                    <p class="mt-1.5 text-[14.5px] leading-[1.65] text-muted">
                        Siswa jenjang SMP seluruhnya diterima di SMA Negeri Favorit Zonasi maupun Prestasi.
                    </p>
                </div>
            </div>

            <div class="bg-paper border border-line/60 rounded-[24px] p-6 flex gap-4 items-start">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-sandgold text-espresso">
                    @include('partials.icon', ['name' => 'contact', 'size' => 21])
                </span>
                <div>
                    <h3 class="text-[17.5px] font-bold text-ink leading-snug">Konseling Minat &amp; Bakat</h3>
                    <p class="mt-1.5 text-[14.5px] leading-[1.65] text-muted">
                        Setiap siswa didampingi dalam penentuan target program studi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
