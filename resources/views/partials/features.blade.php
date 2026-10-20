<section id="keunggulan" class="bg-cream py-20 lg:py-28">
    <div class="mx-auto max-w-[1400px] px-6 lg:px-12">
        <div class="max-w-[760px] mx-auto text-center">
            <p class="text-[11.5px] font-bold uppercase tracking-[0.14em] text-bronze">
                Kenapa Memilih QUALIFY?
            </p>
            <h2 class="mt-4 text-[28px] lg:text-[34px] font-extrabold leading-[1.2] tracking-[-0.015em] text-ink">
                Empat Pilar Keunggulan Belajar Kami
            </h2>
            <p class="mt-5 text-[16px] leading-[1.75] text-muted">
                Dirancang untuk menjawab keresahan siswa dalam menghadapi kurikulum sekolah yang dinamis serta
                seleksi masuk jenjang pendidikan berikutnya.
            </p>
        </div>

        <div class="mt-14 grid md:grid-cols-2 xl:grid-cols-4 gap-6">
            {{-- 1. QualiFix --}}
            <article class="bg-paper border border-line/60 rounded-[24px] overflow-hidden flex flex-col shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <div class="p-7 flex-1 flex flex-col">
                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-sandgold text-espresso">
                        @include('partials.icon', ['name' => 'help-square', 'size' => 24, 'stroke' => 1.9])
                    </span>
                    <span class="mt-6 self-start bg-sandgold text-[#7d5d12] font-bold text-[12px] px-3 py-1.5 rounded-md">
                        Fasilitas Unggulan
                    </span>
                    <h3 class="mt-3.5 text-[23px] font-bold leading-[1.25] text-ink">QualiFix</h3>
                    <p class="mt-4 text-[14.5px] leading-[1.7] text-muted">
                        Konsultasi PR dan tugas sekolah tatap muka bersama tentor pendamping hingga tuntas tanpa
                        dibatasi jumlah soal.
                    </p>
                </div>
                <div class="bg-shell border-t border-line/70 px-7 py-4 flex items-center gap-2.5 text-[13px] font-semibold text-ink">
                    @include('partials.icon', ['name' => 'clock', 'size' => 15, 'class' => 'shrink-0 text-bronze'])
                    Senin s/d Sabtu Bebas Akses
                </div>
            </article>

            {{-- 2. Materi Ajar --}}
            <article class="bg-paper border border-line/60 rounded-[24px] overflow-hidden flex flex-col shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <div class="p-7 flex-1 flex flex-col">
                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-espresso text-[#F7E3B6]">
                        @include('partials.icon', ['name' => 'book-open', 'size' => 24, 'stroke' => 1.9])
                    </span>
                    <span class="mt-6 self-start bg-sand text-ink/75 font-bold text-[12px] px-3 py-1.5 rounded-md">
                        Modul Mutakhir
                    </span>
                    <h3 class="mt-3.5 text-[23px] font-bold leading-[1.25] text-ink">Materi Ajar Terstruktur &amp; Terkurasi</h3>
                    <p class="mt-4 text-[14.5px] leading-[1.7] text-muted">
                        Modul komprehensif mulai dari konsep fundamental, latihan bertingkat, hingga simulasi soal
                        penalaran HOTS SNBT &amp; Ujian Sekolah.
                    </p>
                </div>
                <div class="bg-shell border-t border-line/70 px-7 py-4 flex items-center gap-2.5 text-[13px] font-semibold text-ink">
                    @include('partials.icon', ['name' => 'shield-check', 'size' => 15, 'class' => 'shrink-0 text-bronze'])
                    Kurikulum Merdeka Terintegrasi
                </div>
            </article>

            {{-- 3. Monitoring --}}
            <article class="bg-paper border border-line/60 rounded-[24px] overflow-hidden flex flex-col shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <div class="p-7 flex-1 flex flex-col">
                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-sandgold text-espresso">
                        @include('partials.icon', ['name' => 'clipboard-list', 'size' => 24, 'stroke' => 1.9])
                    </span>
                    <span class="mt-6 self-start bg-sandgold text-[#7d5d12] font-bold text-[12px] px-3 py-1.5 rounded-md">
                        Transparan
                    </span>
                    <h3 class="mt-3.5 text-[23px] font-bold leading-[1.25] text-ink">Monitoring Presensi &amp; Evaluasi Berkala</h3>
                    <p class="mt-4 text-[14.5px] leading-[1.7] text-muted">
                        Pemantauan kehadiran yang disiplin disertai laporan capaian belajar bulanan yang dapat
                        diakses langsung oleh orang tua siswa.
                    </p>
                </div>
                <div class="bg-shell border-t border-line/70 px-7 py-4 flex items-center gap-2.5 text-[13px] font-semibold text-ink">
                    @include('partials.icon', ['name' => 'file-text', 'size' => 15, 'class' => 'shrink-0 text-bronze'])
                    Rapor Progres Berkala
                </div>
            </article>

            {{-- 4. Tentor --}}
            <article class="bg-paper border border-line/60 rounded-[24px] overflow-hidden flex flex-col shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <div class="p-7 flex-1 flex flex-col">
                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-espresso text-[#F7E3B6]">
                        @include('partials.icon', ['name' => 'spark', 'size' => 24, 'stroke' => 1.9])
                    </span>
                    <span class="mt-6 self-start bg-sand text-ink/75 font-bold text-[12px] px-3 py-1.5 rounded-md">
                        Pengajar Teruji
                    </span>
                    <h3 class="mt-3.5 text-[23px] font-bold leading-[1.25] text-ink">Tentor Berpengalaman &amp; Komunikatif</h3>
                    <p class="mt-4 text-[14.5px] leading-[1.7] text-muted">
                        Pengajar pilihan lulusan universitas ternama dengan metode belajar dua arah, ramah,
                        komunikatif, dan memicu rasa ingin tahu.
                    </p>
                </div>
                <div class="bg-shell border-t border-line/70 px-7 py-4 flex items-center gap-2.5 text-[13px] font-semibold text-ink">
                    @include('partials.icon', ['name' => 'lightbulb', 'size' => 15, 'class' => 'shrink-0 text-bronze'])
                    Pendekatan Humanis &amp; Ramah
                </div>
            </article>
        </div>
    </div>
</section>
