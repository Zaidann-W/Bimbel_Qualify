@extends('layouts.owner')

@section('title', 'Pemantauan Materi')

@section('content')
@php
    $statistik = [
        [
            'label' => 'Kesiapan Akademik',
            'angka' => isset($statistik[0]['angka']) ? $statistik[0]['angka'] : '0',
            'satuan' => isset($statistik[0]['satuan']) ? $statistik[0]['satuan'] : 'Modul',
            'kecil' => isset($statistik[0]['kecil']) ? $statistik[0]['kecil'] : false,
            'ikon' => isset($statistik[0]['ikon']) ? $statistik[0]['ikon'] : 'file-text',
            'aksen' => isset($statistik[0]['aksen']) ? $statistik[0]['aksen'] : 'bg-sandgold/60',
            'penanda' => isset($statistik[0]['penanda']) ? $statistik[0]['penanda'] : 'check-circle',
            'catatan' => isset($statistik[0]['catatan']) ? $statistik[0]['catatan'] : '100% kurikulum nasional terverifikasi',
        ],
        [
            'label' => 'Evaluasi & Uji Coba',
            'angka' => isset($statistik[1]['angka']) ? $statistik[1]['angka'] : '0',
            'satuan' => isset($statistik[1]['satuan']) ? $statistik[1]['satuan'] : 'Paket Try Out',
            'kecil' => isset($statistik[1]['kecil']) ? $statistik[1]['kecil'] : false,
            'ikon' => isset($statistik[1]['ikon']) ? $statistik[1]['ikon'] : 'clipboard-form',
            'aksen' => isset($statistik[1]['aksen']) ? $statistik[1]['aksen'] : 'bg-shell',
            'penanda' => isset($statistik[1]['penanda']) ? $statistik[1]['penanda'] : 'dot',
            'catatan' => isset($statistik[1]['catatan']) ? $statistik[1]['catatan'] : 'Soal HOTS & format SNBT terbaru',
        ],
        [
            'label' => 'Pembaruan Terakhir',
            'angka' => isset($statistik[2]['angka']) ? $statistik[2]['angka'] : 'Hari Ini, 09:15',
            'satuan' => isset($statistik[2]['satuan']) ? $statistik[2]['satuan'] : null,
            'kecil' => isset($statistik[2]['kecil']) ? $statistik[2]['kecil'] : true,
            'ikon' => isset($statistik[2]['ikon']) ? $statistik[2]['ikon'] : 'refresh',
            'aksen' => isset($statistik[2]['aksen']) ? $statistik[2]['aksen'] : 'bg-sandgold/50',
            'penanda' => isset($statistik[2]['penanda']) ? $statistik[2]['penanda'] : 'clock',
            'catatan' => isset($statistik[2]['catatan']) ? $statistik[2]['catatan'] : 'Diunggah oleh Admin',
        ],
    ];

    $modul = isset($modul) ? $modul : [];
@endphp

 {{-- Kepala halaman + tiga angka pokok distribusi materi --}}
<section class="rounded-b-2xl bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-xl">
            <p class="flex items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.14em] text-bronze">
                @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'h-4 w-4 text-gold'])
                Audit Internal Kurikulum
            </p>
            <h1 class="mt-2.5 text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Pemantauan Distribusi Materi Belajar
            </h1>
            <p class="mt-3 max-w-lg text-[13.5px] leading-relaxed text-muted">
                Pengawasan ketersediaan bahan ajar, modul tervalidasi, dan silabus yang diunggah oleh Admin untuk tiap jenjang kelas.
            </p>
        </div>

        <div class="inline-flex shrink-0 items-center gap-2.5 rounded-xl border border-line bg-white px-4 py-3 lg:mt-1">
            <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-gold"></span>
            <div class="leading-tight">
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Integritas Dokumen</p>
                <p class="mt-1 text-[13px] font-bold text-ink">Sinkron & Terproteksi</p>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($statistik as $angka)
            <article class="relative overflow-hidden rounded-xl border border-line bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
                <span aria-hidden="true" class="absolute -bottom-10 -right-8 h-28 w-28 rounded-full {{ $angka['aksen'] }}"></span>
                <div class="relative flex items-start justify-between gap-3">
                    <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">{{ $angka['label'] }}</p>
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => $angka['ikon'], 'class' => 'h-4 w-4'])
                    </span>
                </div>
                <p @class([
                    'relative mt-3 flex items-baseline gap-1.5 font-extrabold leading-none tracking-tight tabular-nums text-ink',
                    'text-[22px]' => $angka['kecil'],
                    'text-[30px]' => ! $angka['kecil'],
                ])>
                    {{ $angka['angka'] }}
                    @if ($angka['satuan'])
                        <span class="text-[15px] font-bold tracking-normal text-bark-500">{{ $angka['satuan'] }}</span>
                    @endif
                </p>
                <p class="relative mt-3.5 flex items-center gap-1.5 text-[11px] font-medium leading-snug text-bark-700">
                    @if ($angka['penanda'] === 'dot')
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                    @else
                        @include('layouts.partials.icon', ['name' => $angka['penanda'], 'class' => 'h-3.5 w-3.5 text-gold'])
                    @endif
                    {{ $angka['catatan'] }}
                </p>
            </article>
        @endforeach
    </div>
</section>

 {{-- Tabel pemantauan modul --}}
<div class="px-4 py-5 sm:px-6">
    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
        <div class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">

            {{-- Pencarian --}}
            <div class="relative w-full lg:w-[340px]">
                @include('layouts.partials.icon', ['name' => 'search', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300'])
                <input type="search" data-modul-search placeholder="Cari modul materi, topik, atau mata pelajaran..." aria-label="Cari modul materi, topik, atau mata pelajaran"
                       class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition:hover:bg-sand focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
            </div>

            <div class="flex flex-wrap items-center gap-2.5">

                {{-- Filter jenjang --}}
                <div class="relative w-full sm:w-[210px]">
                    @include('layouts.partials.icon', ['name' => 'filter', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300'])
                    <select data-jenjang-filter aria-label="Saring modul berdasarkan jenjang kelas"
                            class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-9 pr-9 text-[12.5px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                        <option value="semua">Semua Jenjang Kelas</option>
                        <option value="12-sma">12 SMA — SNBT, Alumni & Fase F</option>
                        <option value="11-sma">11 SMA — Kurikulum Merdeka</option>
                        <option value="10-sma">10 SMA — Fase E</option>
                        <option value="smp">Kelas SMP</option>
                    </select>
                    @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-400'])
                </div>

                <p class="inline-flex w-full items-center gap-1.5 whitespace-nowrap rounded-lg bg-sand px-3 py-2.5 text-[11.5px] font-bold text-espresso sm:w-auto">
                    @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-3.5 w-3.5 text-bronze'])
                    Akses Baca Terkunci <span class="font-medium text-bark-300">•</span> Anti-Duplikasi
                </p>
            </div>
        </div>

        <div class="overflow-x-auto border-t border-line">
            <table class="w-full min-w-[860px] text-left">
                <thead>
                    <tr class="border-b border-line">
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Judul Modul & Mata Pelajaran</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Target Jenjang</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Tanggal Diperbarui</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Admin Pengelola</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Spesifikasi Modul</th>
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Kebijakan Akses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($modul as $baris)
                        <tr data-modul-row data-jenjang="{{ $baris['jenjang'] }}" class="transition hover:bg-bark-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-sand text-bronze">
                                        @include('layouts.partials.icon', ['name' => $baris['ikon'], 'class' => 'h-5 w-5'])
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $baris['judul'] }}</p>
                                        <p class="mt-0.5 text-[11px] leading-relaxed text-muted">{{ $baris['mata'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex whitespace-nowrap rounded-md border border-line bg-white px-2.5 py-1 text-[10.5px] font-bold text-espresso">{{ $baris['jenjang_label'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-[12.5px] font-medium whitespace-nowrap text-bark-500">{{ $baris['diperbarui'] }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-2 whitespace-nowrap text-[12.5px] font-medium text-bark-500">
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                                    {{ $baris['admin'] }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-cream text-bark-400">
                                        @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-4 w-4'])
                                    </span>
                                    <div class="leading-tight">
                                        <p class="text-[12px] font-bold whitespace-nowrap text-ink">Modul Digital</p>
                                        <p class="mt-0.5 whitespace-nowrap text-[11px] text-muted">({{ $baris['halaman'] }} Hal)</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md bg-sand px-2.5 py-1.5 text-[10.5px] font-bold text-espresso">
                                    @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-3.5 w-3.5 text-bronze'])
                                    Read-Only Aktif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-[12.5px] text-muted">Belum ada data modul materi yang terdaftar.</td>
                        </tr>
                    @endforelse

                    <tr data-empty-row hidden>
                        <td colspan="6" class="px-5 py-10 text-center">
                            <p class="text-[13px] font-bold text-ink">Tidak ada modul yang cocok</p>
                            <p class="mt-1 text-[12px] text-muted">Ubah kata kunci pencarian atau pilih jenjang kelas lain.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-line bg-bark-50 px-5 py-3.5">
            <p class="flex items-center gap-2 text-[11.5px] leading-relaxed text-muted">
                @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'h-4 w-4 text-gold'])
                Protected View <span class="text-bark-300">•</span> Read-Only Siswa & Tentor
            </p>
            <p class="text-[11.5px] text-muted" aria-live="polite">
                Menampilkan <span data-row-count class="font-bold tabular-nums text-ink">{{ count($modul) }}</span> dari 78 modul terdaftar
            </p>
        </footer>
    </section>
</div>

<script>
    (() => {
        const search = document.querySelector('[data-modul-search]');
        const filter = document.querySelector('[data-jenjang-filter]');
        const rows = document.querySelectorAll('[data-modul-row]');
        const empty = document.querySelector('[data-empty-row]');
        const count = document.querySelector('[data-row-count]');

        const terapkanFilter = () => {
            const kata = (search?.value ?? '').trim().toLowerCase();
            const jenjang = filter?.value ?? 'semua';
            let tampil = 0;

            rows.forEach((row) => {
                const cocokKata = kata === '' || row.textContent.toLowerCase().includes(kata);
                const cocokJenjang = jenjang === 'semua' || row.dataset.jenjang === jenjang;
                const sembunyi = !(cocokKata && cocokJenjang);

                row.hidden = sembunyi;
                if (!sembunyi) {
                    tampil += 1;
                }
            });

            if (empty) {
                empty.hidden = tampil !== 0;
            }
            if (count) {
                count.textContent = String(tampil);
            }
        };

        search?.addEventListener('input', terapkanFilter);
        filter?.addEventListener('change', terapkanFilter);
    })();
</script>
@endsection