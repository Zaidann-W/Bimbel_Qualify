@extends('layouts.admin')

@section('title', 'Laporan & Pengaturan')

{{-- Halaman masih memakai data mock UI sesuai rancangan docs/laporan-admin.png;
     sambungkan ke pembuatan berkas ekspor + tabel preferensi sistem begitu modulnya aktif --}}
@section('content')
@php
    $berkas = [
        ['ikon' => 'wallet', 'judul' => 'Laporan Keuangan & SPP', 'keterangan' => 'Arus kas, uang gedung, dan pelunasan SPP'],
        ['ikon' => 'clipboard-check', 'judul' => 'Presensi & Jadwal Mengajar', 'keterangan' => 'Kehadiran siswa dan jam mengajar tentor'],
        ['ikon' => 'user-plus', 'judul' => 'Pendaftaran Siswa Baru', 'keterangan' => 'Data pendaftar baru dan status registrasi berkas'],
        ['ikon' => 'book-open', 'judul' => 'Rekap Sesi Klinik PR', 'keterangan' => 'Sesi konsultasi belajar dan rekap materi'],
    ];

    $notifikasi = [
        ['id' => 'notif-struk', 'label' => 'Kirim bukti struk pembayaran otomatis', 'aktif' => true],
        ['id' => 'notif-absensi', 'label' => 'Pemberitahuan ketidakhadiran kelas', 'aktif' => true],
        ['id' => 'notif-tagihan', 'label' => 'Pengingat tagihan SPP jatuh tempo', 'aktif' => true],
    ];

    $semester_pil = ['Semester Ganjil', 'Semester Genap'];
@endphp

{{-- Kepala halaman --}}
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Laporan &amp; Pengaturan
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
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-espresso px-4 py-2.5 text-[13px] font-bold text-white transition hover:bg-bark-800 disabled:cursor-wait disabled:opacity-70">
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
                <p class="mt-1 max-w-md text-[12px] leading-relaxed text-muted">Pilih berkas rekapitulasi berkala dalam format Excel atau PDF</p>
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
                        <button type="button" data-ekspor data-judul="{{ $item['judul'] }}" data-format="Excel"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-bold text-espresso transition hover:bg-sand disabled:cursor-wait disabled:opacity-70">
                            @include('layouts.partials.icon', ['name' => 'layout-grid', 'class' => 'h-3.5 w-3.5 text-forest-600'])
                            Excel
                        </button>
                        <button type="button" data-ekspor data-judul="{{ $item['judul'] }}" data-format="PDF"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-espresso px-3 py-2 text-[12px] font-bold text-white transition hover:bg-bark-800 disabled:cursor-wait disabled:opacity-70">
                            @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-3.5 w-3.5 text-gold'])
                            PDF
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Preferensi sistem --}}
    <form id="pengaturan-sistem" data-pengaturan class="grid grid-cols-1 items-start gap-5 lg:grid-cols-2">

        {{-- Notifikasi WhatsApp --}}
        <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start gap-3 px-5 py-4">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-5 w-5'])
                </span>
                <div class="min-w-0">
                    <h2 class="text-[16px] font-extrabold leading-snug tracking-tight text-ink">Notifikasi WhatsApp</h2>
                    <p class="mt-1 max-w-md text-[12px] leading-relaxed text-muted">Kirim otomatis ke nomor orang tua / siswa</p>
                </div>
            </div>

            <div class="border-t border-line">
                @foreach ($notifikasi as $item)
                    <label for="{{ $item['id'] }}"
                           class="flex cursor-pointer items-center justify-between gap-4 border-b border-line px-5 py-3.5 transition last:border-b-0 hover:bg-bark-50">
                        <span class="text-[13px] font-semibold leading-snug text-ink">{{ $item['label'] }}</span>
                        <input type="checkbox" id="{{ $item['id'] }}" name="{{ $item['id'] }}" @checked($item['aktif'])
                               data-notifikasi class="peer sr-only">
                        <span class="relative h-5 w-9 shrink-0 rounded-full bg-bark-200 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform after:content-[''] peer-checked:bg-espresso peer-checked:after:translate-x-4 peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true"></span>
                    </label>
                @endforeach
            </div>

            <footer class="flex items-start gap-2 border-t border-line bg-bark-50 px-5 py-3.5">
                @include('layouts.partials.icon', ['name' => 'whatsapp', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-forest-600'])
                <p class="text-[11.5px] leading-relaxed text-muted">
                    Pesan dikirim dari nomor resmi bimbel, maksimal 200 pesan per hari.
                </p>
            </footer>
        </section>

        {{-- Periode & cabang --}}
        <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start gap-3 px-5 py-4">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-5 w-5'])
                </span>
                <div class="min-w-0">
                    <h2 class="text-[16px] font-extrabold leading-snug tracking-tight text-ink">Periode &amp; Cabang Bimbel</h2>
                    <p class="mt-1 max-w-md text-[12px] leading-relaxed text-muted">Parameter operasional dan kalender akademik</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 border-t border-line px-5 py-5 sm:grid-cols-2">
                <div>
                    <label for="tahun-ajaran" class="block text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">
                        Tahun Ajaran
                    </label>
                    <input id="tahun-ajaran" name="tahun-ajaran" type="text" value="2024/2025"
                           class="mt-2 w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[13.5px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/40">
                </div>

                <div>
                    <label for="semester" class="block text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">
                        Semester
                    </label>
                    <select id="semester" name="semester"
                            class="mt-2 w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[13.5px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/40">
                        @foreach ($semester_pil as $pilihan)
                            <option @selected($pilihan === 'Semester Ganjil')>{{ $pilihan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <footer class="flex flex-wrap items-end justify-between gap-3 border-t border-line bg-bark-50 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">Penanggung Jawab Cabang</p>
                    <p class="mt-2 text-[13.5px] font-bold leading-snug text-ink">Ust. Farhan Kamil, M.Pd.</p>
                </div>
                <span class="shrink-0 whitespace-nowrap rounded-lg border border-line bg-white px-3 py-2 text-[11.5px] font-bold text-bark-600">
                    Branch Director
                </span>
            </footer>
        </section>
    </form>
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

        // Ekspor berkas: umpan balik per baris lewat toast
        document.querySelectorAll('[data-ekspor]').forEach((tombol) => {
            const isiAwal = tombol.innerHTML;

            tombol.addEventListener('click', () => {
                if (tombol.disabled) {
                    return;
                }

                tombol.disabled = true;
                tombol.innerHTML = 'Menyiapkan...';

                window.setTimeout(() => {
                    tombol.disabled = false;
                    tombol.innerHTML = isiAwal;
                    beriToast(`Berkas ${tombol.dataset.judul} (${tombol.dataset.format}) siap diunduh.`);
                }, 900);
            });
        });

        const simpan = () => {
            if (!tombolSimpan || tombolSimpan.disabled) {
                return;
            }
            tombolSimpan.disabled = true;
            labelSimpan.textContent = 'Menyimpan...';

            window.setTimeout(() => {
                tombolSimpan.disabled = false;
                labelSimpan.textContent = labelAwalSimpan;
                tandaiTersimpan();
                beriToast('Pengaturan notifikasi dan periode bimbel tersimpan.');
            }, 650);
        };

        form?.addEventListener('submit', (event) => {
            event.preventDefault();
            simpan();
        });

        form?.addEventListener('input', tandaiBerubah);
        form?.addEventListener('change', tandaiBerubah);
    })();
</script>
@endsection
