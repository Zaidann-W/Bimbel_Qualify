@extends('layouts.admin')

@section('title', 'Keuangan & Transaksi')

{{-- Semua angka pada halaman ini dihitung di KeuanganController dari tabel
     payments, expenses, invoices, cash_accounts, dan cash_sessions. --}}
@section('content')
@php
    $rupiah = fn (float $nilai): string => 'Rp ' . number_format($nilai, 0, ',', '.');

    $status_pil = [
        'lunas' => ['label' => 'Lunas', 'kelas' => 'bg-forest-100 text-forest-700'],
        'belum_bayar' => ['label' => 'Belum Bayar', 'kelas' => 'bg-ember-100 text-ember-700'],
        'sebagian' => ['label' => 'Sebagian', 'kelas' => 'bg-gold text-espresso'],
        'batal' => ['label' => 'Batal', 'kelas' => 'bg-bark-100 text-bark-700'],
        'tercatat' => ['label' => 'Tercatat', 'kelas' => 'bg-sand text-muted'],
    ];

    $totalMasukNum = $totalMasuk ?? 0;
    $totalKeluarNum = $totalKeluar ?? 0;
    $saldoBersihNum = $saldoBersih ?? 0;

    $selisihKas = $sesiTerakhir?->difference;
@endphp

{{-- Kepala halaman --}}
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">

    {{-- Status kasir hari ini --}}
    <div class="flex flex-wrap items-center gap-3 rounded-xl border border-line bg-shell px-4 py-3">
        <div class="flex min-w-0 flex-1 items-center gap-2.5">
            <span @class(['h-2 w-2 shrink-0 rounded-full', $sesiAktif ? 'bg-forest-500' : 'bg-bark-300'])></span>
            <p class="min-w-0 text-[12.5px] leading-snug">
                <span class="font-bold text-ink">Status Kasir: {{ $sesiAktif ? 'AKTIF' : 'BELUM DIBUKA' }}</span>
                <span class="mx-1 text-bark-300">•</span>
                <span class="font-medium text-muted">
                    Kasir: {{ $sesiAktif?->opener?->name ?? '-' }}{{ $sesiAktif ? ' • Dibuka '.$sesiAktif->opened_at->locale('id')->translatedFormat('H:i').' WIB' : '' }}
                </span>
            </p>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <button type="button"
                    class="rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand">
                Tutup Kas
            </button>
            <button type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-espresso px-3.5 py-2.5 text-[12.5px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4 text-gold'])
                Catat Transaksi
            </button>
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
            <p class="flex flex-wrap items-center gap-2.5">
                <span class="rounded-md bg-sandgold px-2.5 py-1.5 text-[10px] font-bold uppercase leading-none tracking-[0.14em] text-bronze">
                    Keuangan &amp; Akuntansi
                </span>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                <span class="text-[11.5px] font-semibold text-muted">{{ $namaLembaga }} T.A. {{ $tahunAjaran }}</span>
            </p>
            <h1 class="mt-3 text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Pencatatan Keuangan Harian
            </h1>
            <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Arus kas bimbel, pembayaran SPP siswa, dan pengeluaran operasional yang tercatat.
            </p>
        </div>

        <div class="shrink-0 rounded-xl border border-line bg-shell px-4 py-3 xl:text-right">
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Buku Kas</p>
            <p class="mt-1.5 text-[13.5px] font-bold text-ink">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    {{-- Tiga angka pokok arus kas --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <article class="rounded-xl border border-line border-t-[3px] border-t-gold bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'trending-up', 'class' => 'h-4 w-4 text-bronze'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Total Pemasukan</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">
                {{ $rupiah($totalMasukNum) }}
            </p>
            <p class="mt-3 text-[11.5px] font-semibold leading-snug text-forest-600">{{ $jumlahMasuk }} transaksi masuk</p>
        </article>

        <article class="rounded-xl border border-espresso border-t-[3px] bg-espresso p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'wallet', 'class' => 'h-4 w-4 text-gold'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-bark-200">Saldo Bersih</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-white">
                {{ $rupiah($saldoBersihNum) }}
            </p>
            <p class="mt-3 flex items-center gap-1.5 text-[11.5px] font-medium leading-snug text-bark-300">
                @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5 shrink-0 text-gold'])
                Arus kas tervalidasi
            </p>
        </article>
    </div>
</section>

{{-- Buku transaksi harian --}}
<div class="px-4 py-5 sm:px-6">
    <section class="overflow-hidden rounded-2xl border border-line bg-white">

        {{-- Tab arah kas & pencarian (disaring server) --}}
        <div class="flex flex-col gap-3 px-4 pt-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex flex-wrap items-center gap-2">
                @foreach ($tabs as $tab)
                    <a href="{{ route('admin.keuangan', array_filter(['arah' => $tab['key'] === 'semua' ? null : $tab['key'], 'q' => $kata !== '' ? $kata : null])) }}"
                       @class([
                           'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition',
                           'bg-espresso text-white' => $tab['key'] === $arah,
                           'text-muted hover:bg-sand hover:text-espresso' => $tab['key'] !== $arah,
                       ])>
                        {{ $tab['label'] }}
                        <span @class([
                            'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none',
                            'bg-gold text-espresso' => $tab['key'] === $arah,
                            'bg-sand text-muted' => $tab['key'] !== $arah,
                        ])>{{ $tab['jumlah'] }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.keuangan') }}" class="relative w-full sm:w-64">
                @if ($arah !== 'semua')
                    <input type="hidden" name="arah" value="{{ $arah }}">
                @endif
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                    @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                </span>
                <input type="search" name="q" value="{{ $kata }}" aria-label="Cari transaksi"
                       placeholder="Cari transaksi..."
                       class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-9 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                @if ($kata !== '')
                    <a href="{{ route('admin.keuangan', array_filter(['arah' => $arah === 'semua' ? null : $arah])) }}"
                       class="absolute inset-y-0 right-0 grid w-9 place-items-center text-bark-300 transition hover:text-espresso"
                       aria-label="Bersihkan pencarian">
                        @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-4 w-4'])
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel transaksi --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] border-collapse text-left">
                <thead>
                    <tr class="bg-shell">
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Waktu / Tanggal</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Keterangan</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Metode</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Nominal</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</th>
                    </tr>
                </thead>
                <tbody id="keuangan-body">
                    @forelse ($transaksi as $row)
                        @php $status = $status_pil[$row['status']] ?? ['label' => ucfirst($row['status']), 'kelas' => 'bg-sand text-muted']; @endphp
                        <tr class="border-t border-line align-top odd:bg-white even:bg-shell">
                            <td class="px-5 py-4">
                                <p class="text-[13px] font-bold leading-snug tabular-nums text-ink">{{ $row['waktu'] }}</p>
                                <p class="mt-1 text-[11.5px] leading-snug text-muted">{{ $row['nomor'] }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $row['keterangan'] }}</p>
                                <p class="mt-1.5 text-[11.5px] leading-snug text-muted">{{ $row['pihak'] }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-[13px] font-medium leading-snug text-ink">{{ $row['metode'] }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <p @class([
                                    'text-[13.5px] font-bold leading-snug tabular-nums',
                                    'text-forest-700' => $row['arah'] === 'masuk',
                                    'text-ember-700' => $row['arah'] === 'keluar',
                                ])>
                                    {{ $row['arah'] === 'masuk' ? '+' : '−' }} {{ $rupiah($row['nominal']) }}
                                </p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $status['kelas'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-line">
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="text-[13.5px] font-bold text-ink">Tidak ada transaksi yang cocok</p>
                                <p class="mt-1.5 text-[12.5px] text-muted">
                                    @if ($kata !== '' || $arah !== 'semua')
                                        Ubah kata kunci pencarian atau pilih tab Semua untuk melihat seluruh arus kas.
                                    @else
                                        Belum ada data tagihan SPP atau pengeluaran kas yang tercatat.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Kaki tabel --}}
        <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-[12px] leading-snug text-muted">
                Menampilkan <span class="font-bold text-ink">{{ $transaksi->firstItem() ?? 0 }}–{{ $transaksi->lastItem() ?? 0 }}</span>
                dari <span class="font-bold text-ink">{{ $transaksi->total() }}</span> transaksi
            </p>

            <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman buku transaksi">
                {{ $transaksi->links() }}
            </nav>
        </footer>
    </section>
</div>

{{-- Rekonsiliasi kas & penagihan SPP --}}
<div class="grid grid-cols-1 gap-4 px-4 pb-8 sm:px-6 lg:grid-cols-2">

    {{-- Kas & Rekening --}}
    <section class="rounded-2xl border border-line bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-[15px] font-bold leading-snug text-ink">Kas &amp; Rekening</h2>
            @if ($selisihKas === null)
                <span class="inline-flex rounded-lg bg-sand px-2.5 py-1.5 text-[11px] font-bold leading-tight text-muted">
                    Belum ada tutup kas
                </span>
            @elseif ((float) $selisihKas == 0.0)
                <span class="inline-flex rounded-lg bg-forest-100 px-2.5 py-1.5 text-[11px] font-bold leading-tight text-forest-700">
                    Status: Klir / Selisih Rp 0
                </span>
            @else
                <span class="inline-flex rounded-lg bg-ember-100 px-2.5 py-1.5 text-[11px] font-bold leading-tight text-ember-700">
                    Status: Selisih {{ $rupiah(abs((float) $selisihKas)) }}
                </span>
            @endif
        </div>

        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
            @forelse ($akunKas as $akun)
                <div class="rounded-xl bg-shell p-4">
                    <p class="text-[12px] font-semibold leading-snug text-muted">{{ $akun['nama'] }}</p>
                    <p class="mt-2 text-[22px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $rupiah($akun['saldo']) }}</p>
                    <p class="mt-2.5 text-[11px] leading-snug text-muted">{{ $akun['keterangan'] }}</p>
                </div>
            @empty
                <div class="rounded-xl bg-shell p-4">
                    <p class="text-[12px] font-semibold leading-snug text-muted">Belum ada akun kas</p>
                    <p class="mt-2.5 text-[11px] leading-snug text-muted">Tambahkan akun kas lewat modul pengaturan keuangan.</p>
                </div>
            @endforelse
        </div>

        <p class="mt-4 flex items-start gap-2 border-t border-line pt-4 text-[12px] leading-relaxed text-muted">
            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'mt-0.5 h-3.5 w-3.5 shrink-0 text-forest-600'])
            @if ($sesiAktif)
                Sesi kas dibuka oleh {{ $sesiAktif->opener?->name ?? '-' }} pada {{ $sesiAktif->opened_at->locale('id')->translatedFormat('d M Y, H:i') }} WIB.
            @elseif ($sesiTerakhir)
                Tutup kas terakhir {{ $sesiTerakhir->closed_at?->locale('id')->translatedFormat('d M Y, H:i') }} WIB oleh {{ $sesiTerakhir->closer?->name ?? '-' }}.
            @else
                Belum ada sesi kas yang dibuka, sehingga selisih kas belum dapat dihitung.
            @endif
        </p>
    </section>

    {{-- Tagihan SPP Belum Lunas --}}
    <section class="rounded-2xl border border-line bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-[15px] font-bold leading-snug text-ink">Tagihan SPP Belum Lunas</h2>
            <span @class([
                'inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight',
                'bg-ember-100 text-ember-700' => $jatuhTempo > 0,
                'bg-sand text-muted' => $jatuhTempo === 0,
            ])>
                {{ $jatuhTempo > 0 ? $jatuhTempo.' Jatuh Tempo' : 'Tidak Ada Jatuh Tempo' }}
            </span>
        </div>

        <ul class="mt-2">
            @forelse ($tagihan as $tunggak)
                <li class="flex flex-wrap items-center gap-3 border-t border-line py-3.5">
                    <div class="min-w-0 flex-1">
                        <p class="text-[13px] font-bold leading-snug text-ink">
                            {{ $tunggak['nama'] }}
                            <span class="font-medium text-muted">({{ $tunggak['kelas'] }})</span>
                        </p>
                        <p class="mt-1 text-[12px] font-semibold leading-snug text-ember-700">
                            {{ $rupiah($tunggak['nominal']) }}
                            <span class="font-medium"> • {{ $tunggak['tempo'] }}</span>
                        </p>
                    </div>
                    <button type="button" data-pengingat
                            class="shrink-0 rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12px] font-semibold text-espresso transition hover:bg-sand">
                        Kirim Pengingat
                    </button>
                </li>
            @empty
                <li class="border-t border-line py-5 text-center">
                    <p class="text-[13.5px] font-bold text-ink">Tidak ada tagihan yang belum lunas</p>
                </li>
            @endforelse
        </ul>

        <div class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
            <p class="text-[12px] leading-snug text-muted">
                Total tertunggak: <span class="font-bold text-ember-700">{{ $rupiah(collect($tagihan)->sum('nominal')) }}</span>
            </p>
            <button type="button" data-pengingat
                    class="inline-flex items-center gap-2 rounded-lg bg-espresso px-4 py-2.5 text-[12.5px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'megaphone', 'class' => 'h-4 w-4 text-gold'])
                Broadcast Semua
            </button>
        </div>
    </section>
</div>

{{-- Umpan balik pengingat tagihan (aksi pengiriman masih menunggu modul notifikasi) --}}
<script>
    (() => {
        document.querySelectorAll('[data-pengingat]').forEach((tombol) => {
            tombol.addEventListener('click', () => {
                tombol.disabled = true;
                tombol.classList.add('text-bark-300');
                tombol.classList.remove('text-espresso', 'text-white');
                tombol.textContent = 'Pengingat Terkirim';
            });
        });
    })();
</script>
@endsection
