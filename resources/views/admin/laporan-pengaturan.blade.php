@extends('layouts.admin')

@section('title', 'Laporan & Pengaturan')

{{-- Halaman masih memakai data mock UI sesuai rancangan docs/laporan-admin.png;
     sambungkan ke pembuatan berkas ekspor + tabel preferensi sistem begitu modulnya aktif --}}
@section('content')
@php
    $berkas = [
        ['ikon' => 'wallet', 'judul' => 'Laporan Keuangan & SPP', 'keterangan' => 'Arus kas, uang gedung, dan pelunasan SPP', 'route' => route('admin.laporanKeuangan.export')],
        ['ikon' => 'clipboard-check', 'judul' => 'Presensi & Jadwal Mengajar', 'keterangan' => 'Kehadiran siswa dan jam mengajar tentor', 'route' => route('admin.laporanPresensi.export')],
        ['ikon' => 'user-plus', 'judul' => 'Pendaftaran Siswa Baru', 'keterangan' => 'Data pendaftar baru dan status registrasi berkas', 'route' => route('laporanPendaftaran.export')],
        ['ikon' => 'book-open', 'judul' => 'Rekap Sesi QualiFix', 'keterangan' => 'Sesi konsultasi belajar dan rekap materi', 'route' => route('laporanQualifix.export')],
    ];
@endphp

<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Laporan & Pengaturan
            </h1>
            <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Unduh berkas rekapitulasi operasional dan pengaturan preferensi sistem.
            </p>
            <p data-status-simpan hidden
               class="mt-3 w-fit rounded-md bg-sandgold px-2.5 py-1.5 text-[11px] font-bold leading-none text-bronze">
                Perubahan Belum Disimpan
            </p>
        </div>

        <button type="submit" form="pengaturan-sistem" data-simpan
                class="inline-flex shrink-0 items-center justify-center rounded-lg bg-espresso px-4 py-2.5 text-[13px] font-bold text-white transition hover:bg-bark-800 disabled:cursor-wait disabled:opacity-70">
            @include('layouts.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4 text-gold'])
            <span data-simpan-label>Simpan Pengaturan</span>
        </button>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Ekspor berkas rekapitulasi --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
        <div class="flex items-start gap-3 px-5 py-4">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                @include('layouts.partials.icon', ['name' => 'file-text', 'class' => 'h-5 w-5'])
            </span>
            <div class="min-w-0">
                <h2 class="text-[16px] font-extrabold leading-snug tracking-tight text-ink">Ekspor Laporan Operasional</h2>
                <p class="mt-1 max-w-md text-[12px] leading-relaxed text-muted">
                    Pilih berkas rekapitulasi berkala dalam format Excel atau PDF
                </p>
            </div>
        </div>

        <ul class="border-t border-line">
            @foreach ($berkas as $item)
                <li class="flex flex-wrap items-center gap-x-4 gap-y-3 border-b border-line px-5 py-3.5 last:border-b-0">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => $item['ikon'], 'class' => 'h-4.5 w-4.5'])
                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $item['judul'] }}</p>
                        <p class="mt-1 text-[12px] leading-snug text-muted">{{ $item['keterangan'] }}</p>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        @if ($item['route'])
                            <a href="{{ $item['route'] }}?format=pdf"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-bold text-espresso transition hover:bg-sand">
                                @include('layouts.partials.icon', ['name' => 'layout-grid', 'class' => 'h-3.5 w-3.5 text-forest-600'])
                                PDF
                            </a>
                            <a href="{{ $item['route'] }}?format=csv"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-espresso px-3 py-2 text-[12px] font-bold text-white transition hover:bg-bark-800">
                                @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-3.5 w-3.5 text-gold'])
                                Excel
                            </a>
                        @else
                            <button type="button" disabled
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-sand px-3 py-2 text-[12px] font-bold text-bark-300 cursor-not-allowed">
                                @include('layouts.partials.icon', ['name' => 'layout-grid', 'class' => 'h-3.5 w-3.5 text-bark-400'])
                                Excel
                            </button>
                            <button type="button" disabled
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-sand px-3 py-2 text-[12px] font-bold text-bark-300 cursor-not-allowed">
                                @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-3.5 w-3.5 text-bark-400'])
                                PDF
                            </button>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Sisa fitur akan diimplementasikan saat modul aktif --}}

</div>

{{-- Penegas aksi --}}
<div data-toast hidden role="status" aria-live="polite"
     class="fixed bottom-5 right-5 z-50 flex max-w-[calc(100vw-2.5rem)] items-center gap-3 rounded-xl bg-espresso px-4 py-3.5 shadow-[0_8px_24px_rgba(20,20,20,0.25)]">
    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-gold text-espresso">
        @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4 w-4'])
    </span>
    <p data-toast-teks class="text-[12.5px] font-semibold text-white"></p>
</div>

<script>
    (() => {
        const form = document.querySelector('[data-pengaturan]');
        const tombolSimpan = document.querySelector('[data-simpan]');
        const labelSimpan = document.querySelector('[data-simpan-label]');
        const statusSimpan = document.querySelector('[data-status-simpan]');
        const toast = document.querySelector('[data-toast]');
        const toastTeks = document.querySelector('[data-toast-teks]');
        const labelAwalSimpan = labelSimpan?.textContent ?? 'Simpan Pengaturan';

        let timerToast;

        const beriToast = (pesan) => {
            if (!toast || !toastTeks) {
                return;
            }
            toastTeks.textContent = pesan;
            toast.hidden = false;
            window.clearTimeout(timerToast);
            timerToast = window.setTimeout(() => {
                toast.hidden = true;
            }, 3600);
        };

        const tandaiBerubah = () => {
            if (statusSimpan) {
                statusSimpan.hidden = false;
            }
        };

        const tandaiTersimpan = () => {
            if (statusSimpan) {
                statusSimpan.hidden = true;
            }
        };

        // Tandai "belum disimpan" saat ada perubahan input
        form?.addEventListener('input', tandaiBerubah);
        form?.addEventListener('change', tandaiBerubah);
    })();
</script>
@endsection