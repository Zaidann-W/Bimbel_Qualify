@extends('layouts.admin')

@section('title', 'QualiFix')

{{-- Halaman masih memakai data mock UI sesuai rancangan docs/qualifix-admin.png;
     sambungkan ke tabel tiket konsultasi & penugasan tentor begitu modul Klinik PR tersedia --}}
@section('content')
@php
    $status_pil = [
        'menunggu' => [
            'label' => 'Menunggu Tentor',
            'badge' => 'bg-sandgold text-bronze',
            'aksi' => 'Tugaskan Tentor',
            'aksi_kelas' => 'bg-espresso text-white hover:bg-bark-800',
        ],
        'dibahas' => [
            'label' => 'Sedang Dibahas',
            'badge' => 'bg-shell text-muted ring-1 ring-line',
            'aksi' => 'Lihat Detail',
            'aksi_kelas' => 'border border-line bg-white text-espresso hover:bg-sand',
        ],
        'selesai' => [
            'label' => 'Selesai',
            'badge' => 'bg-sand text-muted',
            'aksi' => 'Lihat Solusi',
            'aksi_kelas' => 'border border-line bg-white text-espresso hover:bg-sand',
        ],
    ];

    // [kode, judul, chip mapel, siswa, tentor, status]
    $tiket = [
        ['KPR-0824', 'Integral Substitusi Trigonometri', 'Matematika (Kelas 12)', 'Arya Danendra', null, 'menunggu'],
        ['KPR-0823', 'Hukum Induksi Faraday', 'Fisika (Kelas 11)', 'Zahra Annisa', null, 'menunggu'],
        ['KPR-0822', 'Limit Fungsi Trigonometri', 'Matematika (Kelas 12)', 'Bima Aryasatya', null, 'menunggu'],
        ['KPR-0821', 'Tata Nama Senyawa Benzena', 'Kimia (Kelas 12)', 'Rizky Maulana', 'Kak Gilang', 'dibahas'],
        ['KPR-0820', 'Persamaan Garis Lurus', 'Matematika (Kelas 10)', 'Salsabila Ayu', null, 'menunggu'],
        ['KPR-0819', 'Hortatory Exposition Text', 'B. Inggris (Kelas 10)', 'Nadira Safitri', 'Kak Sarah', 'selesai'],
        ['KPR-0818', 'Penyusunan Teks Eksposisi', 'B. Indonesia (Kelas 9)', 'Danu Prakoso', 'Kak Sarah', 'selesai'],
        ['KPR-0817', 'Stoikiometri & Persamaan Reaksi', 'Kimia (Kelas 11)', 'Ananda Putri', 'Kak Dimas', 'selesai'],
        ['KPR-0816', 'Gerak Parabola Lintasan Proyektil', 'Fisika (Kelas 11)', 'Rafi Ardiansyah', 'Kak Rangga', 'selesai'],
        ['KPR-0815', 'Sistem Peredaran Darah Manusia', 'Biologi (Kelas 10)', 'Kirana Maharani', 'Kak Dimas', 'selesai'],
        ['KPR-0814', 'Bilangan Bulat & Operasi Hitung', 'Matematika (Kelas 8)', 'Yusuf Alfarizi', 'Kak Gilang', 'selesai'],
        ['KPR-0813', 'Struktur Teks Deskripsi', 'B. Indonesia (Kelas 8)', 'Alya Nurhaliza', 'Kak Sarah', 'selesai'],
        ['KPR-0812', 'Asam Basa & Titrasi Larutan', 'Kimia (Kelas 12)', 'Bagas Wicaksono', 'Kak Dimas', 'selesai'],
        ['KPR-0811', 'Volume & Luas Bangun Ruang', 'Matematika (Kelas 6)', 'Reza Fahlevi', 'Kak Gilang', 'selesai'],
        ['KPR-0810', 'Simple Past & Past Continuous', 'B. Inggris (Kelas 9)', 'Nabila Ayu', 'Kak Sarah', 'selesai'],
        ['KPR-0809', 'Struktur Teks Laporan Hasil Observasi', 'B. Indonesia (Kelas 9)', 'Intan Permatasari', 'Kak Sarah', 'selesai'],
        ['KPR-0808', 'Penerapan Hukum Ohm', 'Fisika (Kelas 11)', 'Satria Nugraha', 'Kak Rangga', 'selesai'],
        ['KPR-0807', 'Ekosistem & Rantai Makanan', 'Biologi (Kelas 10)', 'Melati Anggraeni', 'Kak Dimas', 'selesai'],
    ];

    $tentor = [
        ['Kak Gilang', 'Matematika', 'membimbing'],
        ['Kak Sarah', 'B. Inggris & B. Indo', 'tersedia'],
        ['Kak Dimas', 'Kimia & Biologi', 'tersedia'],
    ];

    $per_halaman = 4;

    $gaya_status = collect($status_pil)->map(fn (array $pil) => [
        'label' => $pil['label'],
        'badge' => $pil['badge'],
        'aksi' => $pil['aksi'],
        'aksiKelas' => $pil['aksi_kelas'],
    ]);
@endphp

{{-- Kepala halaman --}}
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="max-w-2xl">
        <p class="flex flex-wrap items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-700">
            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
            Layanan Akademik
            <span class="text-bark-300" aria-hidden="true">•</span>
            Bimbel Qualify
        </p>
        <h1 class="mt-2.5 text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">
            QualiFix
        </h1>
        <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
            Daftar pertanyaan dan sesi konsultasi tugas sekolah siswa.
        </p>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Tiga angka pokok antrean Klinik PR --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <article class="rounded-xl border border-line bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Total Masuk</p>
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-bark-500">
                    @include('layouts.partials.icon', ['name' => 'help-circle', 'class' => 'h-4.5 w-4.5'])
                </span>
            </div>
            <p data-kpi="total" class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ count($tiket) }}</p>
            <p class="mt-2 text-[12.5px] leading-snug text-muted">Pertanyaan</p>
        </article>

        <article class="rounded-xl border border-line bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Menunggu Tentor</p>
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'hourglass', 'class' => 'h-4.5 w-4.5'])
                </span>
            </div>
            <p data-kpi="menunggu" class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-bronze">{{ collect($tiket)->where(5, '!=', 'selesai')->count() }}</p>
            <p class="mt-2 text-[12.5px] leading-snug text-muted">Perlu Respon</p>
        </article>

        <article class="rounded-xl border border-line bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Selesai</p>
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-palegold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4.5 w-4.5'])
                </span>
            </div>
            <p data-kpi="selesai" class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ collect($tiket)->where(5, 'selesai')->count() }}</p>
            <p class="mt-2 text-[12.5px] leading-snug text-muted">Terselesaikan</p>
        </article>
    </div>

    <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-3">

        {{-- Kolom utama: antrean tiket konsultasi --}}
        <div class="space-y-5 xl:col-span-2">

            {{-- Tab status & pencarian --}}
            <section class="rounded-2xl border border-line bg-white p-3">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <div class="flex min-w-0 flex-1 flex-wrap gap-1.5" role="group" aria-label="Saring tiket berdasarkan status">
                        @php
                            $tab_pil = [
                                ['semua', 'Semua', count($tiket)],
                                ['menunggu', 'Menunggu', collect($tiket)->where(5, '!=', 'selesai')->count()],
                                ['selesai', 'Selesai', collect($tiket)->where(5, 'selesai')->count()],
                            ];
                        @endphp
                        @foreach ($tab_pil as [$key, $label, $jumlah])
                            <button type="button" data-tab="{{ $key }}" data-tab-btn
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                    @class([
                                        'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition',
                                        'bg-espresso text-white' => $loop->first,
                                        'text-muted hover:bg-sand hover:text-espresso' => ! $loop->first,
                                    ])>
                                {{ $label }}
                                <span data-tab-jumlah="{{ $key }}" @class([
                                    'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none',
                                    'bg-gold text-espresso' => $loop->first,
                                    'bg-sand text-muted' => ! $loop->first,
                                ])>{{ $jumlah }}</span>
                            </button>
                        @endforeach
                    </div>

                    <div class="relative min-w-0 shrink-0 lg:w-64">
                        <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                            @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                        </span>
                        <input type="search" id="tiket-cari" aria-label="Cari tiket, siswa, atau mata pelajaran"
                               placeholder="Cari tiket, siswa, mapel..."
                               class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                    </div>
                </div>
            </section>

            {{-- Daftar tiket --}}
            <section id="daftar-tiket" aria-label="Daftar tiket konsultasi QualiFix" class="space-y-3">
                @foreach ($tiket as [$kode, $judul, $mapel, $siswa, $tentor_nama, $status])
                    @php $pil = $status_pil[$status]; @endphp
                    <article data-tiket
                             data-kode="{{ $kode }}"
                             data-status="{{ $status }}"
                             data-cari="{{ strtolower($kode . ' ' . $judul . ' ' . $mapel . ' ' . $siswa . ' ' . ($tentor_nama ?? '')) }}"
                             class="rounded-xl border border-line bg-white p-4 sm:p-5">
                        <div class="flex h-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-5">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                    <span class="text-[11.5px] font-bold tracking-[0.04em] text-muted">#{{ $kode }}</span>
                                    <span class="inline-flex rounded-md bg-sandgold px-2 py-1 text-[11px] font-bold leading-none text-bronze">{{ $mapel }}</span>
                                </div>

                                <h2 class="mt-2.5 text-[17px] font-extrabold leading-snug tracking-tight text-ink">{{ $judul }}</h2>

                                <p data-meta class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-[12.5px] leading-snug text-muted">
                                    @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                                    <span>{{ $siswa }}</span>
                                    @if ($tentor_nama)
                                        <span class="text-bark-300" aria-hidden="true">•</span>
                                        <span>Tentor: {{ $tentor_nama }}</span>
                                    @endif
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-wrap items-center gap-2 sm:justify-end">
                                <span data-badge @class(['rounded-md px-2.5 py-1.5 text-[11px] font-bold leading-none', $pil['badge']])>
                                    {{ $pil['label'] }}
                                </span>
                                <button type="button" data-tiket-aksi
                                        @class(['inline-flex items-center justify-center rounded-lg px-3.5 py-2.5 text-[12px] font-bold leading-none transition', $pil['aksi_kelas']])>
                                    {{ $pil['aksi'] }}
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach

                <div id="tiket-kosong" class="hidden rounded-xl border border-dashed border-line bg-white px-5 py-12 text-center">
                    @include('layouts.partials.icon', ['name' => 'search', 'class' => 'mx-auto h-6 w-6 text-bark-300'])
                    <p class="mt-2.5 text-[13.5px] font-bold text-ink">Tidak ada tiket yang cocok</p>
                    <p class="mt-1.5 text-[12.5px] leading-relaxed text-muted">Ubah kata kunci pencarian atau pilih tab Semua.</p>
                </div>
            </section>

            {{-- Kaki daftar --}}
            <section class="flex flex-col gap-3 rounded-2xl border border-line bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <p data-hitung aria-live="polite" class="text-[12px] leading-snug text-muted">
                        Menampilkan <span class="font-bold text-ink">1–{{ $per_halaman }}</span>
                        dari <span class="font-bold text-ink">{{ count($tiket) }}</span> Tiket Konsultasi
                    </p>
                    <p data-pesan role="status" aria-live="polite" class="mt-1 hidden text-[11.5px] font-semibold leading-snug text-bronze"></p>
                </div>

                <nav class="flex shrink-0 items-center gap-1.5" aria-label="Navigasi halaman daftar tiket">
                    <button type="button" data-halaman="prev"
                            class="rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-semibold text-espresso transition hover:bg-sand">
                        Prev
                    </button>
                    <span data-nomor class="flex items-center gap-1.5"></span>
                    <button type="button" data-halaman="next"
                            class="rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-semibold text-espresso transition hover:bg-sand">
                        Next
                    </button>
                </nav>
            </section>
        </div>

        {{-- Rail kanan: ketersediaan tentor --}}
        <div class="space-y-5">
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-center gap-2.5">
                    <h2 class="min-w-0 text-[17px] font-extrabold leading-snug tracking-tight text-ink">Ketersediaan Tentor</h2>
                    <span data-standby class="ml-auto shrink-0 rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold leading-none text-bronze">
                        {{ collect($tentor)->where(2, 'tersedia')->count() }} Standby
                    </span>
                </header>
                <p class="mt-1 text-[12px] leading-snug text-muted">Tentor Pendamping Klinik PR</p>

                <div class="mt-4 space-y-3">
                    @foreach ($tentor as [$nama, $bidang, $kondisi])
                        <article data-tentor data-kondisi="{{ $kondisi }}"
                                 class="flex items-center gap-3 rounded-xl bg-cream px-4 py-3.5">
                            <span data-tentor-ikon class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                                @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-4.5 w-4.5'])
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13.5px] font-bold leading-snug text-ink">{{ $nama }}</p>
                                <p class="truncate text-[11.5px] leading-snug text-muted">{{ $bidang }}</p>
                            </div>
                            <span data-tentor-status @class([
                                'flex shrink-0 items-center gap-1.5 text-[11.5px] font-bold leading-none',
                                'text-bronze' => $kondisi === 'membimbing',
                                'text-muted' => $kondisi === 'tersedia',
                            ])>
                                <span data-tentor-titik @class([
                                    'h-1.5 w-1.5 shrink-0 rounded-full',
                                    'bg-gold' => $kondisi === 'membimbing',
                                    'bg-bark-300' => $kondisi === 'tersedia',
                                ])}></span>
                                <span data-tentor-label>{{ $kondisi === 'membimbing' ? 'Membimbing' : 'Tersedia' }}</span>
                            </span>
                        </article>
                    @endforeach
                </div>

                <div data-tentor-kosong class="hidden rounded-xl border border-dashed border-line bg-cream px-4 py-6 text-center">
                    @include('layouts.partials.icon', ['name' => 'users', 'class' => 'mx-auto h-6 w-6 text-bark-300'])
                    <p class="mt-2.5 text-[13px] font-bold text-ink">Semua pendamping sedang mengajar</p>
                    <p class="mt-1.5 text-[12px] leading-relaxed text-muted">Tugaskan tiket setelah ada tentor yang kembali standby.</p>
                </div>
            </section>

            <section class="rounded-2xl border border-line bg-white px-5 py-4">
                <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-muted">
                    @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'mt-[1px] h-4 w-4 shrink-0 text-gold'])
                    Tiket Klinik PR hanya terbaca oleh siswa pengirim, tentor pendamping, dan pengelola cabang.
                </p>
            </section>
        </div>
    </div>
</div>

<script>
    (() => {
        const perHalaman = {{ $per_halaman }};
        const gayaStatus = @json($gaya_status);

        const tiket = Array.from(document.querySelectorAll('[data-tiket]'));
        const tombolTab = Array.from(document.querySelectorAll('[data-tab-btn]'));
        const inputCari = document.getElementById('tiket-cari');
        const barisKosong = document.getElementById('tiket-kosong');
        const labelHitung = document.querySelector('[data-hitung]');
        const labelStatus = document.querySelector('[data-pesan]');
        const wadahNomor = document.querySelector('[data-nomor]');
        const tombolNav = Array.from(document.querySelectorAll('[data-halaman]'));
        const kpi = {
            total: document.querySelector('[data-kpi="total"]'),
            menunggu: document.querySelector('[data-kpi="menunggu"]'),
            selesai: document.querySelector('[data-kpi="selesai"]'),
        };
        const lencanaTab = {
            semua: document.querySelector('[data-tab-jumlah="semua"]'),
            menunggu: document.querySelector('[data-tab-jumlah="menunggu"]'),
            selesai: document.querySelector('[data-tab-jumlah="selesai"]'),
        };

        const daftarTentor = Array.from(document.querySelectorAll('[data-tentor]'));
        const lencanaStandby = document.querySelector('[data-standby]');
        const kotakTentorKosong = document.querySelector('[data-tentor-kosong]');

        let tab = 'semua';
        let halaman = 1;

        const tersaring = () => {
            const kata = inputCari.value.trim().toLowerCase();

            return tiket.filter((kartu) => {
                const cocokTab = tab === 'semua'
                    || (tab === 'menunggu' ? kartu.dataset.status !== 'selesai' : kartu.dataset.status === 'selesai');
                const cocokKata = !kata || kartu.dataset.cari.includes(kata);

                return cocokTab && cocokKata;
            });
        };

        const bangunNomor = (totalHalaman) => {
            const awal = Math.max(1, Math.min(halaman - 1, totalHalaman - 1));
            const akhir = Math.min(totalHalaman, awal + 1);

            wadahNomor.innerHTML = '';

            for (let nomor = awal; nomor <= akhir; nomor += 1) {
                const tombol = document.createElement('button');
                tombol.type = 'button';
                tombol.textContent = String(nomor);
                tombol.setAttribute('aria-label', `Halaman ${nomor}`);
                tombol.className = nomor === halaman
                    ? 'grid h-8 w-8 place-items-center rounded-lg bg-espresso text-[12.5px] font-bold text-white'
                    : 'grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-[12.5px] font-semibold text-espresso transition hover:bg-sand';

                if (nomor === halaman) {
                    tombol.setAttribute('aria-current', 'page');
                }

                tombol.addEventListener('click', () => {
                    halaman = nomor;
                    terapkan();
                });

                wadahNomor.appendChild(tombol);
            }
        };

        const hitungStatus = () => {
            const selesai = tiket.filter((kartu) => kartu.dataset.status === 'selesai').length;
            const menunggu = tiket.length - selesai;

            kpi.total.textContent = String(tiket.length);
            kpi.menunggu.textContent = String(menunggu);
            kpi.selesai.textContent = String(selesai);
            lencanaTab.semua.textContent = String(tiket.length);
            lencanaTab.menunggu.textContent = String(menunggu);
            lencanaTab.selesai.textContent = String(selesai);
        };

        const hitungStandby = () => {
            const standby = daftarTentor.filter((kartu) => kartu.dataset.kondisi === 'tersedia').length;

            lencanaStandby.textContent = `${standby} Standby`;
            kotakTentorKosong.classList.toggle('hidden', standby > 0);
        };

        const terapkan = () => {
            const daftar = tersaring();
            const totalHalaman = Math.max(1, Math.ceil(daftar.length / perHalaman));

            halaman = Math.min(halaman, totalHalaman);

            const awal = (halaman - 1) * perHalaman;
            const tampil = daftar.slice(awal, awal + perHalaman);

            tiket.forEach((kartu) => kartu.classList.add('hidden'));
            tampil.forEach((kartu) => kartu.classList.remove('hidden'));

            barisKosong.classList.toggle('hidden', daftar.length > 0);
            hitungStatus();

            const dari = daftar.length === 0 ? 0 : awal + 1;
            const sampai = Math.min(awal + perHalaman, daftar.length);

            labelHitung.innerHTML = `Menampilkan <span class="font-bold text-ink">${dari}–${sampai}</span>`
                + ` dari <span class="font-bold text-ink">${daftar.length}</span> Tiket Konsultasi`;

            bangunNomor(totalHalaman);

            tombolNav.forEach((tombol) => {
                const sebelum = tombol.dataset.halaman === 'prev';
                tombol.disabled = sebelum ? halaman === 1 : halaman === totalHalaman;
                tombol.classList.toggle('text-espresso', ! tombol.disabled);
                tombol.classList.toggle('text-bark-300', tombol.disabled);
            });
        };

        tombolTab.forEach((tombol) => {
            tombol.addEventListener('click', () => {
                tab = tombol.dataset.tab;
                halaman = 1;

                tombolTab.forEach((lain) => {
                    const aktif = lain === tombol;

                    lain.setAttribute('aria-pressed', String(aktif));
                    lain.classList.toggle('bg-espresso', aktif);
                    lain.classList.toggle('text-white', aktif);
                    lain.classList.toggle('text-muted', ! aktif);

                    const lencana = lain.querySelector('span');
                    lencana.classList.toggle('bg-gold', aktif);
                    lencana.classList.toggle('text-espresso', aktif);
                    lencana.classList.toggle('bg-sand', ! aktif);
                    lencana.classList.toggle('text-muted', ! aktif);
                });

                terapkan();
            });
        });

        inputCari.addEventListener('input', () => {
            halaman = 1;
            terapkan();
        });

        tombolNav.forEach((tombol) => {
            tombol.addEventListener('click', () => {
                halaman += tombol.dataset.halaman === 'prev' ? -1 : 1;
                terapkan();
            });
        });

        // ---- Penugasan tentor (mock: belum tersambung tabel tiket) ----
        const tampilkanPesan = (pesan) => {
            labelStatus.textContent = pesan;
            labelStatus.classList.remove('hidden');
        };

        document.getElementById('daftar-tiket').addEventListener('click', (event) => {
            const tombol = event.target.closest('[data-tiket-aksi]');
            if (!tombol) {
                return;
            }

            const kartu = tombol.closest('[data-tiket]');

            if (kartu.dataset.status !== 'menunggu') {
                tampilkanPesan(`Tiket #${kartu.dataset.kode} dibuka — tampilan detail belum tersambung modul Klinik PR.`);
                return;
            }

            const pendamping = daftarTentor.find((tentor) => tentor.dataset.kondisi === 'tersedia');

            if (! pendamping) {
                tampilkanPesan('Belum ada tentor standby. Tugaskan tiket setelah ada pendamping yang tersedia.');
                return;
            }

            const nama = pendamping.querySelector('p').textContent.trim();
            const kode = kartu.dataset.kode;

            kartu.dataset.status = 'dibahas';
            kartu.dataset.cari = `${kartu.dataset.cari} ${nama.toLowerCase()}`;

            const badge = kartu.querySelector('[data-badge]');
            badge.className = `rounded-md px-2.5 py-1.5 text-[11px] font-bold leading-none ${gayaStatus.dibahas.badge}`;
            badge.textContent = gayaStatus.dibahas.label;

            tombol.className = `inline-flex items-center justify-center rounded-lg px-3.5 py-2.5 text-[12px] font-bold leading-none transition ${gayaStatus.dibahas.aksiKelas}`;
            tombol.textContent = gayaStatus.dibahas.aksi;

            const meta = kartu.querySelector('[data-meta]');
            if (meta && ! meta.textContent.includes('Tentor:')) {
                const pemisah = document.createElement('span');
                pemisah.className = 'text-bark-300';
                pemisah.setAttribute('aria-hidden', 'true');
                pemisah.textContent = '•';

                const penugasan = document.createElement('span');
                penugasan.textContent = `Tentor: ${nama}`;

                meta.append(pemisah, penugasan);
            }

            pendamping.dataset.kondisi = 'membimbing';
            pendamping.querySelector('[data-tentor-label]').textContent = 'Membimbing';

            const titik = pendamping.querySelector('[data-tentor-titik]');
            titik.className = 'h-1.5 w-1.5 shrink-0 rounded-full bg-gold';

            const label = pendamping.querySelector('[data-tentor-status]');
            label.className = 'flex shrink-0 items-center gap-1.5 text-[11.5px] font-bold leading-none text-bronze';

            hitungStandby();
            terapkan();
            tampilkanPesan(`Tiket #${kode} ditugaskan ke ${nama}. ${tiket.filter((t) => t.dataset.status !== 'selesai').length} tiket masih menunggu respons.`);
        });

        hitungStandby();
        terapkan();
    })();
</script>
@endsection
