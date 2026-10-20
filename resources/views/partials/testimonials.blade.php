<section id="testimoni" class="bg-cream py-20 lg:py-28">
    <div class="mx-auto max-w-[1400px] px-6 lg:px-12">
        <div class="max-w-[780px] mx-auto text-center">
            <p class="text-[11.5px] font-bold uppercase tracking-[0.14em] text-bronze">
                Kisah Inspiratif
            </p>
            <h2 class="mt-4 text-[28px] lg:text-[34px] font-extrabold leading-[1.2] tracking-[-0.015em] text-ink">
                Apa Kata Siswa dan Orang Tua?
            </h2>
            <p class="mt-5 text-[16px] leading-[1.75] text-muted">
                Dedikasi kami tercermin dari kepuasan dan pencapaian keluarga besar Bimbel QUALIFY.
            </p>
        </div>

        <div class="mt-14 grid md:grid-cols-3 gap-6">
            {{-- Arya --}}
            <figure class="bg-paper border border-line/60 rounded-[24px] overflow-hidden flex flex-col shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <div class="p-7 flex-1">
                    <div class="flex gap-1 text-bronze" aria-label="Penilaian 5 dari 5">
                        @for ($i = 0; $i < 5; $i++)
                            @include('partials.icon', ['name' => 'star', 'size' => 16, 'stroke' => 1.6])
                        @endfor
                    </div>
                    <blockquote class="mt-5 text-[15.5px] italic leading-[1.8] text-ink/85">
                        “Klinik PR di QUALIFY ngebantu banget pas persiapan ujian dan tugas fisika, tentornya super
                        sabar! Yang tadinya saya bingung rumus diturunkan dari mana, di sini dibedah sampai beneran
                        paham.”
                    </blockquote>
                </div>
                <figcaption class="bg-shell border-t border-line/70 px-7 py-5 flex items-center gap-4">
                    <img
                        src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&h=120&q=80"
                        alt="Foto Arya D."
                        class="h-11 w-11 rounded-full object-cover"
                        width="44"
                        height="44"
                        loading="lazy"
                    >
                    <div>
                        <p class="text-[17px] font-bold text-ink leading-tight">Arya D.</p>
                        <p class="text-[13px] font-bold text-bronze mt-1">Lolos Teknik Mesin ITB</p>
                        <p class="text-[13px] text-muted mt-0.5">Alumni SMA Negeri 1</p>
                    </div>
                </figcaption>
            </figure>

            {{-- Zahra --}}
            <figure class="bg-paper border border-line/60 rounded-[24px] overflow-hidden flex flex-col shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <div class="p-7 flex-1">
                    <div class="flex gap-1 text-bronze" aria-label="Penilaian 5 dari 5">
                        @for ($i = 0; $i < 5; $i++)
                            @include('partials.icon', ['name' => 'star', 'size' => 16, 'stroke' => 1.6])
                        @endfor
                    </div>
                    <blockquote class="mt-5 text-[15.5px] italic leading-[1.8] text-ink/85">
                        “Belajar matematika jadi masuk akal dan nggak bikin stres lagi. Suasana kelasnya seru dan
                        tentornya asyik diajak diskusi sampai malam menjelang ujian ASPD.”
                    </blockquote>
                </div>
                <figcaption class="bg-shell border-t border-line/70 px-7 py-5 flex items-center gap-4">
                    <img
                        src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&h=120&q=80"
                        alt="Foto Zahra A."
                        class="h-11 w-11 rounded-full object-cover"
                        width="44"
                        height="44"
                        loading="lazy"
                    >
                    <div>
                        <p class="text-[17px] font-bold text-ink leading-tight">Zahra A.</p>
                        <p class="text-[13px] font-bold text-bronze mt-1">Peringkat 1 ASPD Tingkat Kota</p>
                        <p class="text-[13px] text-muted mt-0.5">Siswa Kelas 9 SMP</p>
                    </div>
                </figcaption>
            </figure>

            {{-- Ratna --}}
            <figure class="bg-paper border border-line/60 rounded-[24px] overflow-hidden flex flex-col shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <div class="p-7 flex-1">
                    <div class="flex gap-1 text-bronze" aria-label="Penilaian 5 dari 5">
                        @for ($i = 0; $i < 5; $i++)
                            @include('partials.icon', ['name' => 'star', 'size' => 16, 'stroke' => 1.6])
                        @endfor
                    </div>
                    <blockquote class="mt-5 text-[15.5px] italic leading-[1.8] text-ink/85">
                        “Laporannya jelas, tahu persis jadwal dan progres anak tanpa perlu khawatir. Komunikasi pihak
                        bimbel dengan wali murid sangat terbuka dan informatif.”
                    </blockquote>
                </div>
                <figcaption class="bg-shell border-t border-line/70 px-7 py-5 flex items-center gap-4">
                    <img
                        src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=120&h=120&q=80"
                        alt="Foto Ibu Ratna"
                        class="h-11 w-11 rounded-full object-cover"
                        width="44"
                        height="44"
                        loading="lazy"
                    >
                    <div>
                        <p class="text-[17px] font-bold text-ink leading-tight">Ibu Ratna</p>
                        <p class="text-[13px] font-bold text-bronze mt-1">Wali Murid Siswa SMA</p>
                        <p class="text-[13px] text-muted mt-0.5">Pendampingan 2 Tahun Berturut-turut</p>
                    </div>
                </figcaption>
            </figure>
        </div>
    </div>
</section>
