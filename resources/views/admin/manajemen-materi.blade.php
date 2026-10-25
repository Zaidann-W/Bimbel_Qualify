@extends('layouts.admin')

@section('title', 'Manajemen Materi Ajar')

{{-- Halaman masih memakai data mock UI sesuai rancangan docs/manajemenmateri-admin.png;
     sambungkan ke tabel materi ajar + storage berkas begitu modulnya tersedia --}}
@section('content')
@php
    $akses_pil = [
        'baca' => ['label' => 'Hanya Baca', 'kelas' => 'bg-sandgold text-bronze'],
        'unduh' => ['label' => 'Boleh Unduh', 'kelas' => 'bg-sand text-muted'],
    ];

    $kpis = [
        [
            'nilai' => '86',
            'satuan' => 'Modul',
            'label' => 'Total Materi Ajar',
            'ikon' => 'file-text',
            'tile' => 'bg-sandgold text-bronze',
        ],
        [
            'nilai' => '18',
            'satuan' => 'Kelas',
            'label' => 'Kelas Terdistribusi',
            'ikon' => 'users',
            'tile' => 'bg-sand text-ink',
        ],
        [
            'nilai' => 'Aktif',
            'satuan' => '',
            'label' => 'Mode Baca Online',
            'ikon' => 'eye',
            'tile' => 'bg-palegold text-espresso',
        ],
    ];

    $tabs = [
        ['key' => 'semua', 'label' => 'Semua', 'jumlah' => '86'],
        ['key' => 'sma', 'label' => 'SMA', 'jumlah' => '42'],
        ['key' => 'smp', 'label' => 'SMP', 'jumlah' => '26'],
        ['key' => 'sd', 'label' => 'SD', 'jumlah' => '18'],
    ];

    $materi = [
        [
            'judul' => 'Bank Soal TPS UTBK SNBT — Penalaran Matematika',
            'tipe' => 'PDF', 'ikon' => 'file-text', 'tile' => 'bg-ember-100 text-ember-700',
            'ukuran' => '4.2 MB', 'tanggal' => '12 Okt 2026',
            'mapel' => 'Matematika Saintek', 'kelas' => '12-IPA-A & 12-IPA-B',
            'tentor' => 'Ust. Ahmad Fauzi',
            'jenjang' => 'sma', 'akses' => 'baca',
        ],
        [
            'judul' => 'Slide Presentasi Gelombang Elektromagnetik',
            'tipe' => 'PPTX', 'ikon' => 'presentation', 'tile' => 'bg-sandgold text-bronze',
            'ukuran' => '18.6 MB', 'tanggal' => '08 Okt 2026',
            'mapel' => 'Fisika Lanjutan', 'kelas' => '12-IPA-A',
            'tentor' => 'Ust. Rizky Maulana',
            'jenjang' => 'sma', 'akses' => 'baca',
        ],
        [
            'judul' => 'LKS Analisis Teks Argumentasi & Resensi Kritis',
            'tipe' => 'DOCX', 'ikon' => 'file', 'tile' => 'bg-sand text-bark-400',
            'ukuran' => '2.1 MB', 'tanggal' => '01 Okt 2026',
            'mapel' => 'Bahasa Indonesia', 'kelas' => '9-SMP-A & 9-SMP-B',
            'tentor' => 'Sdri. Nur Aini',
            'jenjang' => 'smp', 'akses' => 'baca',
        ],
        [
            'judul' => 'Buku Saku Olimpiade Sains: Aritmetika & Pola Bilangan',
            'tipe' => 'PDF', 'ikon' => 'file-text', 'tile' => 'bg-ember-100 text-ember-700',
            'ukuran' => '7.4 MB', 'tanggal' => '25 Sep 2026',
            'mapel' => 'Matematika OSN', 'kelas' => 'SD-OSN-Bintang',
            'tentor' => 'Ust. Ahmad Fauzi',
            'jenjang' => 'sd', 'akses' => 'baca',
        ],
        [
            'judul' => 'Rangkuman Pokok Bahasan Trigonometri',
            'tipe' => 'PDF', 'ikon' => 'file-text', 'tile' => 'bg-ember-100 text-ember-700',
            'ukuran' => '3.8 MB', 'tanggal' => '22 Sep 2026',
            'mapel' => 'Matematika Lanjutan', 'kelas' => '11-IPA-A & 11-IPA-B',
            'tentor' => 'Ust. Dwi Hermanto',
            'jenjang' => 'sma', 'akses' => 'unduh',
        ],
        [
            'judul' => 'Modul Praktikum Listrik Statis',
            'tipe' => 'PPTX', 'ikon' => 'presentation', 'tile' => 'bg-sandgold text-bronze',
            'ukuran' => '12.4 MB', 'tanggal' => '18 Sep 2026',
            'mapel' => 'Fisika Dasar', 'kelas' => '10-IPA-B',
            'tentor' => 'Ust. Rizky Maulana',
            'jenjang' => 'sma', 'akses' => 'baca',
        ],
        [
            'judul' => 'Kumpulan Soal Ujian Nasional Matematika SMP',
            'tipe' => 'PDF', 'ikon' => 'file-text', 'tile' => 'bg-ember-100 text-ember-700',
            'ukuran' => '9.6 MB', 'tanggal' => '12 Sep 2026',
            'mapel' => 'Matematika', 'kelas' => '9-SMP-A, 9-SMP-B & 9-SMP-C',
            'tentor' => 'Sdri. Nur Aini',
            'jenjang' => 'smp', 'akses' => 'unduh',
        ],
        [
            'judul' => 'Flashcard Kosakata Bahasa Inggris SD',
            'tipe' => 'DOCX', 'ikon' => 'file', 'tile' => 'bg-sand text-bark-400',
            'ukuran' => '5.3 MB', 'tanggal' => '05 Sep 2026',
            'mapel' => 'Bahasa Inggris', 'kelas' => '5-SD-A & 5-SD-B',
            'tentor' => 'Sdri. Laila Hasanah',
            'jenjang' => 'sd', 'akses' => 'baca',
        ],
    ];
@endphp

{{-- Kepala halaman --}}
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="max-w-2xl">
        <h1 class="text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">
            Manajemen Materi Ajar
        </h1>
        <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
            Kelola repositori materi pelajaran, modul ajar, dan distribusi berkas untuk kelas siswa bimbel QUALIFY.
        </p>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Tiga angka pokok repositori materi --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($kpis as $kpi)
            <article class="rounded-xl border border-line bg-white p-4">
                <div class="flex items-center gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg {{ $kpi['tile'] }}">
                        @include('layouts.partials.icon', ['name' => $kpi['ikon'], 'class' => 'h-5 w-5'])
                    </span>
                    <div class="min-w-0">
                        <p class="flex items-baseline gap-1.5">
                            <span class="text-[24px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $kpi['nilai'] }}</span>
                            @if ($kpi['satuan'] !== '')
                                <span class="text-[13px] font-bold leading-none text-ink">{{ $kpi['satuan'] }}</span>
                            @endif
                        </p>
                        <p class="mt-1.5 text-[11.5px] font-medium leading-snug text-muted">{{ $kpi['label'] }}</p>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    {{-- Unggah materi baru --}}
    <section class="rounded-2xl border border-line bg-white p-5">
        <header class="flex items-start gap-3">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-ink">
                @include('layouts.partials.icon', ['name' => 'file-text', 'class' => 'h-4.5 w-4.5'])
            </span>
            <div>
                <h2 class="text-[15px] font-bold leading-snug text-ink">Unggah Materi Pelajaran</h2>
                <p class="mt-0.5 text-[12px] leading-snug text-muted">Lampirkan berkas dan tentukan kelas sasaran siswa.</p>
            </div>
        </header>

        <form id="materi-form" action="#" method="post" enctype="multipart/form-data" class="mt-5" novalidate>
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-12">

                {{-- Berkas modul --}}
                <div class="md:col-span-6">
                    <label for="materi-berkas" class="block text-[12px] font-bold leading-snug text-ink">
                        File Modul <span class="font-medium text-muted">(PDF / PPTX / DOCX)</span>
                    </label>
                    <label for="materi-berkas"
                           class="mt-2 flex cursor-pointer items-center gap-2.5 rounded-xl border border-bark-200 bg-shell px-4 py-3 transition hover:bg-sand focus-within:border-gold focus-within:bg-white focus-within:ring-2 focus-within:ring-gold/30">
                        @include('layouts.partials.icon', ['name' => 'upload', 'class' => 'h-4 w-4 shrink-0 text-bronze'])
                        <span id="materi-berkas-nama" class="min-w-0 flex-1 truncate text-[13px] leading-snug text-muted">
                            Belum ada berkas dipilih — klik untuk memilih dari komputer
                        </span>
                    </label>
                    <input type="file" id="materi-berkas" name="berkas" accept=".pdf,.pptx,.docx"
                           class="sr-only" aria-describedby="materi-berkas-error">
                    <p id="materi-berkas-error" class="mt-1.5 hidden text-[11.5px] font-semibold leading-snug text-ember-700"></p>
                </div>

                {{-- Judul materi --}}
                <div class="md:col-span-3">
                    <label for="materi-judul" class="block text-[12px] font-bold leading-snug text-ink">Judul Materi</label>
                    <input type="text" id="materi-judul" name="judul" aria-describedby="materi-judul-error"
                           placeholder="Contoh: Bank Soal Penalaran Matematika"
                           class="mt-2 w-full rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                    <p id="materi-judul-error" class="mt-1.5 hidden text-[11.5px] font-semibold leading-snug text-ember-700"></p>
                </div>

                {{-- Mata pelajaran --}}
                <div class="md:col-span-3">
                    <label for="materi-mapel" class="block text-[12px] font-bold leading-snug text-ink">Mata Pelajaran</label>
                    <div class="relative mt-2">
                        <select id="materi-mapel" name="mapel"
                                class="w-full appearance-none rounded-xl border border-bark-200 bg-white px-4 py-3 pr-10 text-[13px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                            <option>Matematika</option>
                            <option>Fisika</option>
                            <option>Kimia</option>
                            <option>Biologi</option>
                            <option>Bahasa Indonesia</option>
                            <option>Bahasa Inggris</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-0 grid w-10 place-items-center text-bark-400">
                            @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                        </span>
                    </div>
                </div>

                {{-- Target kelas --}}
                <div class="md:col-span-9">
                    <label for="materi-target" class="block text-[12px] font-bold leading-snug text-ink">Target Kelas / Jenjang</label>
                    <div class="relative mt-2">
                        <select id="materi-target" name="target"
                                class="w-full appearance-none rounded-xl border border-bark-200 bg-white px-4 py-3 pr-10 text-[13px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                            <option>12 SMA — Persiapan UTBK (Semua Rombel)</option>
                            <option>12 IPA — Rombel A &amp; B</option>
                            <option>11 IPA — Rombel A &amp; B</option>
                            <option>10 IPA — Rombel B</option>
                            <option>9 SMP — Rombel A, B &amp; C</option>
                            <option>SD — Kelas Bintang (OSN)</option>
                            <option>SD — Kelas 5 A &amp; B</option>
                            <option>Semua Jenjang (SMA, SMP, SD)</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-0 grid w-10 place-items-center text-bark-400">
                            @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                        </span>
                    </div>
                </div>

                {{-- Aksi unggah --}}
                <div class="flex flex-col gap-3 md:col-span-12 sm:flex-row sm:items-center sm:justify-end">
                    <p id="materi-status" role="status" class="min-w-0 flex-1 hidden text-[12px] font-semibold leading-snug text-bronze sm:block"></p>
                    <div class="flex shrink-0 flex-col gap-2.5 sm:flex-row">
                        <button type="button" id="materi-draf"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-line bg-white px-5 py-3 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                            @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-4 w-4 text-bronze'])
                            Simpan Draf
                        </button>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-espresso px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                            @include('layouts.partials.icon', ['name' => 'upload', 'class' => 'h-4 w-4 text-gold'])
                            Unggah Materi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>

    {{-- Daftar materi terunggah --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-white">

        {{-- Tab jenjang --}}
        <div class="flex flex-wrap items-center gap-2 px-4 pt-4 sm:px-5">
            @foreach ($tabs as $tab)
                <button type="button" data-tab="{{ $tab['key'] }}" data-tab-btn
                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                        @class([
                            'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition',
                            'bg-espresso text-white' => $loop->first,
                            'text-muted hover:bg-sand hover:text-espresso' => ! $loop->first,
                        ])>
                    {{ $tab['label'] }}
                    <span @class([
                        'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none',
                        'bg-gold text-espresso' => $loop->first,
                        'bg-sand text-muted' => ! $loop->first,
                    ])>{{ $tab['jumlah'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Pencarian & filter --}}
        <div class="flex items-center gap-2.5 px-4 py-4 sm:px-5">
            <div class="relative min-w-0 flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                    @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                </span>
                <input type="search" id="materi-cari" aria-label="Cari judul materi, mata pelajaran, atau tentor"
                       placeholder="Cari judul materi, topik soal, atau tentor..."
                       class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
            </div>

            <button type="button" aria-label="Filter lanjutan materi ajar"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                @include('layouts.partials.icon', ['name' => 'filter', 'class' => 'h-4 w-4'])
            </button>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] border-collapse text-left">
                <thead>
                    <tr class="bg-shell">
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Judul &amp; Berkas</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Mata Pelajaran &amp; Kelas</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status Akses</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody id="materi-body">
                    @foreach ($materi as $row)
                        @php $akses = $akses_pil[$row['akses']]; @endphp
                        <tr data-baris data-jenjang="{{ $row['jenjang'] }}"
                            data-cari="{{ strtolower($row['judul'] . ' ' . $row['mapel'] . ' ' . $row['kelas'] . ' ' . $row['tentor'] . ' ' . $row['tipe']) }}"
                            class="border-t border-line align-top odd:bg-white even:bg-shell">
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg {{ $row['tile'] }}" aria-hidden="true">
                                        @include('layouts.partials.icon', ['name' => $row['ikon'], 'class' => 'h-4.5 w-4.5'])
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $row['judul'] }}</p>
                                        <p class="mt-1.5 text-[11.5px] leading-snug text-muted">
                                            {{ $row['tipe'] }} <span class="text-bark-300">•</span> {{ $row['ukuran'] }}
                                            <span class="text-bark-300">•</span> Diunggah {{ $row['tanggal'] }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-[13px] font-bold leading-snug text-ink">{{ $row['mapel'] }}</p>
                                <p class="mt-1.5 text-[11.5px] leading-snug text-muted">{{ $row['kelas'] }}</p>
                                <p class="mt-0.5 text-[11.5px] leading-snug text-muted">{{ $row['tentor'] }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $akses['kelas'] }}">
                                    @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-3.5 w-3.5'])
                                    {{ $akses['label'] }}
                                </span>
                                <p class="mt-1.5 text-[11px] leading-snug text-muted">Akses Online</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" aria-label="Atur distribusi &amp; hak akses {{ $row['judul'] }}"
                                            class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                        @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-4 w-4'])
                                    </button>
                                    <button type="button" aria-label="Hapus materi {{ $row['judul'] }}"
                                            class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-ember-100 text-ember-700 transition hover:bg-ember-200">
                                        @include('layouts.partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <tr id="materi-kosong" class="hidden border-t border-line">
                        <td colspan="4" class="px-5 py-12 text-center">
                            <p class="text-[13.5px] font-bold text-ink">Tidak ada materi yang cocok</p>
                            <p class="mt-1.5 text-[12.5px] text-muted">Ubah kata kunci pencarian atau pilih tab Semua.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Kaki tabel --}}
        <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-[12px] leading-snug text-muted">
                Menampilkan <span id="materi-dari" class="font-bold text-ink">4</span>
                dari <span id="materi-total" class="font-bold text-ink">86</span> materi pelajaran terdaftar di sistem
            </p>

            <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman daftar materi ajar">
                <button type="button" disabled
                        class="rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-semibold text-bark-300">
                    Sebelumnya
                </button>
                <button type="button" aria-current="page"
                        class="grid h-8 w-8 place-items-center rounded-lg bg-espresso text-[12.5px] font-bold text-white">1</button>
                <button type="button"
                        class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-[12.5px] font-semibold text-espresso transition hover:bg-sand">2</button>
                <button type="button"
                        class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-[12.5px] font-semibold text-espresso transition hover:bg-sand">3</button>
                <span class="px-1 text-[12.5px] text-bark-300">…</span>
                <button type="button"
                        class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-[12.5px] font-semibold text-espresso transition hover:bg-sand">22</button>
                <button type="button"
                        class="rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-semibold text-espresso transition hover:bg-sand">
                    Selanjutnya
                </button>
            </nav>
        </footer>
    </section>
</div>

<script>
    (() => {
        const baris = Array.from(document.querySelectorAll('[data-baris]'));
        const tombolTab = Array.from(document.querySelectorAll('[data-tab-btn]'));
        const inputCari = document.getElementById('materi-cari');
        const barisKosong = document.getElementById('materi-kosong');
        const labelDari = document.getElementById('materi-dari');
        const labelTotal = document.getElementById('materi-total');

        let tabAktif = 'semua';

        const terapkan = () => {
            const kata = inputCari.value.trim().toLowerCase();
            let tampil = 0;

            baris.forEach((barisData) => {
                const cocokTab = tabAktif === 'semua' || barisData.dataset.jenjang === tabAktif;
                const cocokKata = !kata || barisData.dataset.cari.includes(kata);
                const terlihat = cocokTab && cocokKata;

                barisData.classList.toggle('hidden', !terlihat);
                if (terlihat) {
                    tampil += 1;
                }
            });

            barisKosong.classList.toggle('hidden', tampil > 0);
            labelDari.textContent = String(tampil);
            labelTotal.textContent = tabAktif === 'semua' && !kata ? '86' : String(tampil);
        };

        tombolTab.forEach((tombol) => {
            tombol.addEventListener('click', () => {
                tabAktif = tombol.dataset.tab;

                tombolTab.forEach((lain) => {
                    const aktif = lain === tombol;
                    const lencana = lain.querySelector('span');

                    lain.setAttribute('aria-pressed', String(aktif));
                    lain.className = aktif
                        ? 'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition bg-espresso text-white'
                        : 'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition text-muted hover:bg-sand hover:text-espresso';
                    lencana.className = aktif
                        ? 'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none bg-gold text-espresso'
                        : 'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none bg-sand text-muted';
                });

                terapkan();
            });
        });

        inputCari.addEventListener('input', terapkan);

        // ---- Formulir unggah (mock: belum tersambung storage) ----
        const form = document.getElementById('materi-form');
        const berkas = document.getElementById('materi-berkas');
        const namaBerkas = document.getElementById('materi-berkas-nama');
        const errorBerkas = document.getElementById('materi-berkas-error');
        const judul = document.getElementById('materi-judul');
        const errorJudul = document.getElementById('materi-judul-error');
        const status = document.getElementById('materi-status');
        const draf = document.getElementById('materi-draf');

        berkas.addEventListener('change', () => {
            const file = berkas.files[0];
            namaBerkas.textContent = file
                ? `${file.name} — ${(file.size / (1024 * 1024)).toFixed(1)} MB`
                : 'Belum ada berkas dipilih — klik untuk memilih dari komputer';
            namaBerkas.classList.toggle('text-ink', Boolean(file));
            namaBerkas.classList.toggle('text-muted', ! file);
            errorBerkas.classList.add('hidden');
        });

        const sembunyikanStatus = () => {
            status.textContent = '';
            status.classList.add('hidden');
        };

        judul.addEventListener('input', () => {
            errorJudul.classList.add('hidden');
            sembunyikanStatus();
        });

        berkas.addEventListener('change', sembunyikanStatus);

        form.addEventListener('submit', (event) => {
            event.preventDefault();

            let sah = true;

            if (! berkas.files.length) {
                errorBerkas.textContent = 'Pilih berkas modul terlebih dahulu (PDF, PPTX, atau DOCX).';
                errorBerkas.classList.remove('hidden');
                sah = false;
            }

            if (! judul.value.trim()) {
                errorJudul.textContent = 'Isi judul materi agar siswa mudah menemukannya.';
                errorJudul.classList.remove('hidden');
                sah = false;
            }

            if (! sah) {
                return;
            }

            status.textContent = `Berkas "${judul.value.trim()}" siap diunggah. Penyimpanan berkas akan aktif setelah modul materi ajar tersambung.`;
            status.classList.remove('hidden');
        });

        draf.addEventListener('click', () => {
            if (! judul.value.trim()) {
                errorJudul.textContent = 'Isi judul materi sebelum menyimpan draf.';
                errorJudul.classList.remove('hidden');
                judul.focus();
                return;
            }

            status.textContent = `Draf "${judul.value.trim()}" ditandai siap disimpan.`;
            status.classList.remove('hidden');
        });
    })();
</script>
@endsection
