<section class="bg-cream pb-20 lg:pb-28">
    <div class="mx-auto max-w-[1400px] px-6 lg:px-12">
        <div class="relative overflow-hidden rounded-[28px] bg-espresso px-6 py-14 sm:px-10 lg:px-16 lg:py-16 text-center">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -right-24 -top-32 h-[440px] w-[560px] rounded-full bg-[radial-gradient(closest-side,rgba(245,183,46,0.55),rgba(245,183,46,0))]"></div>
                <div class="absolute -left-24 -bottom-40 h-[420px] w-[540px] rounded-full bg-[radial-gradient(closest-side,rgba(196,146,44,0.45),rgba(196,146,44,0))]"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-transparent via-transparent to-[#3a2513]/60"></div>
            </div>

            <div class="relative">
                <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gold text-espresso">
                    @include('partials.icon', ['name' => 'graduation', 'size' => 26, 'stroke' => 1.9])
                </span>

                <h2 class="mt-7 mx-auto max-w-[860px] text-[32px] lg:text-[46px] font-extrabold leading-[1.2] tracking-[-0.015em] text-white">
                    Siap Melangkah Menuju Prestasi Terbaikmu?
                </h2>

                <p class="mt-5 mx-auto max-w-[740px] text-[16px] lg:text-[16.5px] leading-[1.75] text-[#c9bfb2]">
                    Formulir pendaftaran dapat diakses langsung secara online tanpa perlu login terlebih dahulu.
                    Mulai perjalanan akademik yang lebih terarah, menyenangkan, dan membanggakan bersama Bimbel QUALIFY.
                </p>

                <div class="mt-9 flex flex-wrap justify-center gap-4">
                    <a
                        href="{{ route('registrasi') }}"
                        class="inline-flex items-center gap-2.5 bg-gold hover:bg-[#ecab20] text-espresso font-bold text-[15.5px] px-6 py-4 rounded-xl transition-colors"
                    >
                        Isi Formulir Pendaftarkan Sekarang
                        @include('partials.icon', ['name' => 'external-link', 'size' => 17])
                    </a>
                    <a
                        href="#"
                        class="inline-flex items-center gap-2.5 bg-white/10 hover:bg-white/[0.16] text-white font-semibold text-[15.5px] px-6 py-4 rounded-xl transition-colors"
                    >
                        @include('partials.icon', ['name' => 'message', 'size' => 17])
                        Hubungi Layanan Informasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
