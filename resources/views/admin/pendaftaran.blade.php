@extends('layouts.admin')

@section('title', 'Pendaftaran Siswa')

@section('content')
@php
    $status_pil = [
        'review' => ['label' => 'Menunggu Review', 'kelas' => 'bg-sandgold text-bronze'],
        'perbaikan' => ['label' => 'Perlu Perbaikan', 'kelas' => 'bg-ember-100 text-ember-700'],
        'diterima' => ['label' => 'Diterima & Terbit NIS', 'kelas' => 'bg-sand text-muted'],
        'ditolak' => ['label' => 'Ditolak / Batal', 'kelas' => 'bg-white text-ember-700 ring-1 ring-ember-200'],
    ];

    $statusAksi = [
        'review' => ['label' => 'Review', 'kelas' => 'bg-espresso text-white hover:bg-bark-800'],
        'perbaikan' => ['label' => 'Perbaiki', 'ikon' => 'refresh', 'kelas' => 'border border-line bg-white text-espresso hover:bg-sand'],
        'diterima' => ['label' => 'Selesai', 'ikon' => 'check-circle', 'kelas' => 'bg-sand text-bark-400'],
        'ditolak' => ['label' => 'Lihat Alasan', 'ikon' => 'eye', 'kelas' => 'border border-line bg-white text-espresso hover:bg-sand'],
    ];

    $bulanId = [
        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr',
        'Mei' => 'Mei', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Agu' => 'Agu',
        'Sep' => 'Sep', 'Okt' => 'Okt', 'Nov' => 'Nov', 'Des' => 'Des',
    ];

    $statusKey = function ($status) {
        return match ($status) {
            'menunggu_review' => 'review',
            'perlu_perbaikan' => 'perbaikan',
            'diterima' => 'diterima',
            default => 'ditolak',
        };
    };
@endphp

<?php /*  Kepala halaman */ ?>
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
            <p class="flex flex-wrap items-center gap-2.5">
                <span class="rounded-md bg-sandgold px-2.5 py-1.5 text-[10px] font-bold uppercase leading-none tracking-[0.14em] text-bronze">
                    Modul 5.3 Admisi
                </span>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                <span class="text-[11.5px] font-semibold text-muted">Bimbel QUALIFY T.A. 2024/2025</span>
            </p>
            <h1 class="mt-3 text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Verifikasi &amp; Pengelolaan Pendaftaran Siswa
            </h1>
            <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Pemeriksaan formulir pendaftaran masuk, verifikasi berkas &amp; bukti transfer, penetapan status siswa (Menunggu, Perlu Perbaikan, Diterima, Ditolak), dan aktivasi akun.
            </p>
        </div>

        <div class="flex shrink-0 flex-col gap-2.5 sm:flex-row xl:flex-col">
            <button type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-line bg-white px-4 py-3 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4 text-bronze'])
                Ekspor Data (CSV/Excel)
            </button>
            <button type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-espresso px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'user-plus', 'class' => 'h-4 w-4 text-gold'])
                Tambah Pendaftaran Manual
            </button>
        </div>
    </div>

    <?php /*  Empat angka pokok admisi */ ?>
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kpis as $kpi)
            <article @class([
                'rounded-xl border border-line border-t-[3px] bg-white p-4',
                $kpi['aksen'],
            ])>
                <div class="flex items-start justify-between gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ $kpi['tile'] }}">
                        @include('layouts.partials.icon', ['name' => $kpi['ikon'], 'class' => 'h-4.5 w-4.5'])
                    </span>
                </div>
                <p class="mt-3.5 text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">{{ $kpi['label'] }}</p>
                <p class="mt-1.5 flex items-baseline gap-2">
                    <span class="text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $kpi['nilai'] }}</span>
                    <span class="text-[12.5px] font-semibold text-muted">{{ $kpi['satuan'] }}</span>
                </p>

                @if ($kpi['bawah'] === 'sla')
                    <p class="mt-3.5 flex items-center gap-1.5 text-[11.5px] font-medium leading-snug text-bark-700">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                        Target SLA Peninjauan <span class="font-bold text-ink">&lt; 2.4 Jam</span>
                    </p>
                @elseif ($kpi['bawah'] === 'chip')
                    <p class="mt-3.5">
                        <span class="inline-flex rounded-md bg-ember-100 px-2 py-1 text-[10.5px] font-bold leading-none text-ember-700">
                            Slip buram / No. WA salah
                        </span>
                    </p>
                @elseif ($kpi['bawah'] === 'konversi')
                    <p class="mt-3.5 text-[11.5px] font-medium leading-snug text-bark-700">
                        Konversi Formulir <span class="font-bold text-ink">88.4% Masuk Kelas</span>
                    </p>
                @else
                    <p class="mt-3.5 flex items-center gap-1.5 text-[11.5px] font-medium leading-snug text-bark-700">
                        @include('layouts.partials.icon', ['name' => 'info', 'class' => 'h-3.5 w-3.5 shrink-0 text-bark-300'])
                        Batal jadwal / luar domisili
                    </p>
                @endif
            </article>
        @endforeach
    </div>
</section>

<?php /*  Daftar pendaftar */ ?>
<div class="px-4 py-5 sm:px-6">
    <section class="overflow-hidden rounded-2xl border border-line bg-white">

        <?php /*  Tab status */ ?>
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

        <?php /*  Pencarian & filter tanggal */ ?>
        <div class="flex flex-col gap-2.5 px-4 py-4 sm:flex-row sm:items-center sm:px-5">
            <div class="relative min-w-0 flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                    @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                </span>
                <input type="search" id="pendaftaran-cari" aria-label="Cari calon siswa atau wali, no. registrasi..."
                       placeholder="Cari nama calon siswa atau wali, no. registrasi..."
                       class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
            </div>

            <div class="relative shrink-0 sm:w-52">
                <select id="pendaftaran-tanggal" aria-label="Filter tanggal pendaftaran"
                        class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-3 pr-9 text-[12.5px] font-semibold text-espresso transition hover:bg-sand focus:outline-none focus:ring-2 focus:ring-gold/30">
                    <option value="semua">Semua Tanggal</option>
                    <option value="hari">24 Jam Terakhir</option>
                    <option value="minggu">7 Hari Terakhir</option>
                    <option value="bulan">Bulan Ini</option>
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-0 grid w-9 place-items-center text-bark-400">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4'])
                </span>
            </div>
        </div>

        <?php /*  Tabel */ ?>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] border-collapse text-left">
                <thead>
                    <tr class="bg-shell">
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Nama Siswa</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Orang Tua / Wali</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Pembayaran</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pendaftaran-body">
                    @forelse ($pendaftar as $row)
                        @php
                            $sk = $statusKey($row->status);
                            $status = $status_pil[$sk] ?? $status_pil['ditolak'];
                            $ak = $statusAksi[$sk] ?? $statusAksi['ditolak'];
                            $kelas = $row->school_grade
                                ? 'Kelas ' . $row->school_grade . ($row->education_level ? ' ' . $row->education_level : '')
                                : '-';
                            $tglId = $row->created_at
                                ? $row->created_at->format('d') . ' ' . ($bulanId[$row->created_at->format('M')] ?? $row->created_at->format('M')) . ' ' . $row->created_at->format('Y')
                                : '-';
                        @endphp
                        <tr data-baris data-status="{{ $sk }}" data-periode="bulan"
                            data-cari="{{ strtolower($row->full_name . ' ' . $row->guardian_name . ' ' . $row->registration_number . ' ' . $row->school_name) }}"
                            class="border-t border-line align-top odd:bg-white even:bg-shell">
                            <td class="px-5 py-4">
                                <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $row->full_name }}</p>
                                <p class="mt-1.5 text-[11.5px] leading-snug text-muted">{{ $row->school_name ?: '-' }}</p>
                                <p class="mt-0.5 text-[11.5px] leading-snug text-muted">{{ $kelas }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-[13px] font-medium leading-snug text-ink">{{ $row->guardian_name ?: '-' }}</p>
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $row->phone_wa ?? '') }}" target="_blank" rel="noopener"
                                   class="mt-1.5 inline-flex items-center gap-1.5 text-[11.5px] font-semibold leading-snug text-bronze transition hover:text-espresso hover:underline">
                                    @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-3.5 w-3.5'])
                                    {{ $row->phone_wa ?: '-' }}
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-[13.5px] font-bold leading-snug tabular-nums text-ink">-</p>
                                <p class="mt-1.5 text-[11.5px] leading-snug text-muted">Belum ada tagihan</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $status['kelas'] }}">
                                    {{ $status['label'] }}
                                </span>
                                <p class="mt-1.5 text-[11px] leading-snug text-muted">{{ $tglId }} <span class="text-bark-300">•</span> {{ $row->registration_number }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.pendaftaran.detail', $row->registration_number) }}"
                                   class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3.5 py-2.5 text-[12.5px] font-semibold transition {{ $ak['kelas'] }}">
                                    @if ($ak['ikon'])
                                        @include('layouts.partials.icon', ['name' => $ak['ikon'], 'class' => 'h-4 w-4'])
                                    @endif
                                    {{ $ak['label'] }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="pendaftaran-kosong" class="border-t border-line">
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="text-[13.5px] font-bold text-ink">Tidak ada pendaftar yang cocok</p>
                                <p class="mt-1.5 text-[12.5px] text-muted">Ubah kata kunci pencarian, rentang tanggal, atau pilih tab Semua.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <?php /*  Kaki tabel */ ?>
        <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-[12px] leading-snug text-muted">
                Menampilkan <span class="font-bold text-ink">{{ $pendaftar->firstItem() ?? 0 }} – {{ $pendaftar->lastItem() ?? 0 }}</span>
                dari <span class="font-bold text-ink">{{ $pendaftar->total() }}</span> pendaftar aktif
            </p>

            <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman daftar pendaftar">
                {{ $pendaftar->links() }}
            </nav>
        </footer>
    </section>
</div>

<script>
    (() => {
        const baris = Array.from(document.querySelectorAll('[data-baris]'));
        const tombolTab = Array.from(document.querySelectorAll('[data-tab-btn]'));
        const inputCari = document.getElementById('pendaftaran-cari');
        const selectTanggal = document.getElementById('pendaftaran-tanggal');
        const barisKosong = document.getElementById('pendaftaran-kosong');
        const labelDari = document.getElementById('pendaftaran-dari');
        const labelTotal = document.getElementById('pendaftaran-total');
        const totalAktif = baris.length;

        let tabAktif = 'semua';

        const terapkan = () => {
            const kata = inputCari.value.trim().toLowerCase();
            const periode = selectTanggal.value;
            let tampil = 0;

            baris.forEach((barisData) => {
                const cocokTab = tabAktif === 'semua' || barisData.dataset.status === tabAktif;
                const cocokKata = !kata || barisData.dataset.cari.includes(kata);
                const cocokTanggal = periode === 'semua' || barisData.dataset.periode === periode;
                const terlihat = cocokTab && cocokKata && cocokTanggal;

                barisData.classList.toggle('hidden', !terlihat);
                if (terlihat) {
                    tampil += 1;
                }
            });

            barisKosong.classList.toggle('hidden', tampil > 0);
            if (labelDari) {
                labelDari.textContent = tampil === 0 ? '0' : `1 – ${tampil}`;
            }
            if (labelTotal) {
                labelTotal.textContent = tabAktif === 'semua' && !kata && periode === 'semua' ? baris.length : String(tampil);
            }
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
        selectTanggal.addEventListener('change', terapkan);
    })();
</script>
@endsection
