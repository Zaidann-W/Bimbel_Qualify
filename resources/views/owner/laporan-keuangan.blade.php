@extends('layouts.owner')

@section('title', 'Laporan Keuangan')

@section('content')
@php
    $rupiah = fn (int $nilai): string => 'Rp ' . number_format($nilai, 0, ',', '.');
@endphp

 {{-- Kepala halaman + ringkasan cepat --}}
<section class="rounded-b-2xl bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-xl">
            <span class="inline-flex items-center gap-1.5 rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold text-bronze">
                <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                PRD 5.3 • Financial Governance
            </span>
            <h1 class="mt-2.5 text-[26px] font-bold leading-tight tracking-tight text-ink sm:text-[30px]">Laporan Arus Keuangan Eksekutif</h1>
            <p class="mt-1.5 max-w-md text-[13px] leading-relaxed text-muted">Rekapitulasi pemasukan SPP siswa, beban operasional bimbel, dan laba bersih per periode.</p>
        </div>

        <div class="flex flex-col gap-2.5 lg:items-end">
            <div class="relative w-full lg:w-auto">
                @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300'])
                <select aria-label="Pilih periode laporan"
                        class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-9 pr-9 text-[13px] font-medium text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30 lg:w-auto">
                    <option>Bulan Ini (Mei 2025)</option>
                    <option>April 2025</option>
                    <option>Maret 2025</option>
                    <option>Februari 2025</option>
                    <option>Januari 2025</option>
                    <option>Desember 2024</option>
                    <option>Tahun Ajaran 2024/2025</option>
                </select>
                @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-400'])
            </div>

            <button type="button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[13px] font-bold text-espresso transition hover:bg-gold/80 lg:w-auto">
                @include('layouts.partials.icon', ['name' => 'printer', 'class' => 'h-4 w-4'])
                Cetak Laporan PDF
            </button>
        </div>
    </div>

    {{-- Tiga angka pokok periode berjalan --}}
    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- Total pemasukan --}}
        <article class="rounded-xl border-x border-b border-x-line border-b-line border-t-[3px] border-t-gold bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Total Pemasukan</p>
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'trending-up', 'class' => 'h-4 w-4'])
                </span>
            </div>
            <p class="mt-3 text-[26px] font-bold leading-none tracking-tight tabular-nums text-ink">{{ $totalPemasukan }}</p>
            <p class="mt-3 flex items-center gap-1.5 text-[11px] text-muted">
                @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5 text-gold'])
                94% dari SPP 348 Siswa Aktif
            </p>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full rounded-full bg-gold" style="width: {{ round($totalPemasukan > 0 ? $totalPemasukan * 0.815 / $totalPemasukan * 100 : 0) }}%"></div>
            </div>
        </article>

        {{-- Total pengeluaran --}}
        <article class="rounded-xl border-x border-b border-x-line border-b-line border-t-[3px] border-t-espresso bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Total Pengeluaran</p>
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-espresso text-sandgold">
                    @include('layouts.partials.icon', ['name' => 'wallet', 'class' => 'h-4 w-4'])
                </span>
            </div>
            <p class="mt-3 text-[26px] font-bold leading-none tracking-tight tabular-nums text-ink">{{ $totalPengeluaran }}</p>
            <p class="mt-3 flex items-center gap-1.5 text-[11px] text-muted">
                @include('layouts.partials.icon', ['name' => 'info', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                <span class="truncate">Honor Tentor, Sewa & Operasional Bimbel</span>
            </p>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full rounded-full bg-bark-700" style="width: {{ round($totalPemasukan > 0 ? $totalPengeluaran / $totalPemasukan * 100 : 0) }}%"></div>
            </div>
        </article>

        {{-- Laba bersih --}}
        <article class="rounded-xl border-x border-b border-x-line border-b-line border-t-[3px] border-t-gold bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Laba Bersih / Selisih</p>
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-gold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'briefcase', 'class' => 'h-4 w-4'])
                </span>
            </div>
            <p class="mt-3 text-[26px] font-bold leading-none tracking-tight tabular-nums text-ink">{{ $labaBersih }}</p>
            <span @class(['mt-3 inline-flex items-center gap-1.5 rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-semibold text-bronze', 'hidden' => $totalPemasukan === 0])>
                @if ($totalPemasukan > 0)
                    Margin {{ round($labaBersih / $totalPemasukan * 100) }}% • Arus Kas Sangat Sehat
                @else
                    Margin 0% • Arus Kas Sehat
                @endif
            </span>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                @if ($totalPemasukan > 0)
                    <div class="h-full rounded-full bg-gold" style="width: {{ round($labaBersih / $totalPemasukan * 100) }}%"></div>
                @else
                    <div class="h-full rounded-full bg-gold" style="width: 0%"></div>
                @endif
            </div>
        </article>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Rincian pemasukan vs beban --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

        {{-- Sumber pemasukan --}}
        <section class="overflow-hidden rounded-2xl border border-line bg-white">
            <header class="flex items-center justify-between gap-3 px-5 py-4">
                <h2 class="flex items-center gap-2 text-[15px] font-bold text-ink">
                    <span class="h-2 w-2 shrink-0 rounded-full bg-gold"></span>
                    Sumber Pemasukan
                </h2>
                <span class="rounded-full bg-sand px-2.5 py-1 text-[10px] font-semibold text-muted">3 Pos Penerimaan</span>
            </header>

            <ul class="divide-y divide-line border-t border-line">
                @foreach ($sumberPemasukan as $pos)
                    <li class="flex items-start justify-between gap-4 px-5 py-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-[13.5px] font-semibold leading-snug text-ink">{{ $pos['nama'] }}</p>
                            <p class="mt-1 text-[11px] leading-relaxed text-muted">{{ $pos['rincian'] }}</p>
                            <div class="mt-2.5 h-1.5 w-full max-w-[260px] overflow-hidden rounded-full bg-bark-100">
                                <div class="h-full rounded-full bg-gold" style="width: {{ $pos['persen'] }}%"></div>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="whitespace-nowrap text-[13.5px] font-bold tabular-nums text-ink">{{ $rupiah($pos['nilai']) }}</p>
                            <span @class([
                                'mt-1.5 inline-block rounded-md px-2 py-[3px] text-[9.5px] font-semibold',
                                'bg-sandgold text-bronze' => (($pos['status'] ?? '') === 'lunas'),
                                'bg-sand text-muted' => (($pos['status'] ?? '') === 'netral'),
                            ])>{{ $pos['tag'] }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>

            <footer class="flex items-center justify-between gap-3 border-t border-line bg-bark-50 px-5 py-3.5">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Total Masuk</p>
                <p class="whitespace-nowrap text-[17px] font-bold tabular-nums text-bronze">{{ $totalPemasukan }}</p>
            </footer>
        </section>

        {{-- Alokasi beban operasional --}}
        <section class="overflow-hidden rounded-2xl border border-line bg-white">
            <header class="flex items-start justify-between gap-3 px-5 py-4">
                <h2 class="flex items-start gap-2 text-[15px] font-bold leading-snug text-ink">
                    <span class="mt-[7px] h-2 w-2 shrink-0 rounded-full bg-espresso"></span>
                    Alokasi Beban Operasional
                </h2>
                <span class="shrink-0 rounded-full bg-sand px-2.5 py-1 text-[10px] font-semibold text-muted">3 Pos Pengeluaran</span>
            </header>

            <ul class="divide-y divide-line border-t border-line">
                @foreach ($alokasiBeban as $pos)
                    <li class="flex items-start justify-between gap-4 px-5 py-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-[13.5px] font-semibold leading-snug text-ink">{{ $pos['nama'] }}</p>
                            <p class="mt-1 text-[11px] leading-relaxed text-muted">{{ $pos['rincian'] }}</p>
                            <div class="mt-2.5 h-1.5 w-full max-w-[260px] overflow-hidden rounded-full bg-bark-100">
                                <div class="h-full rounded-full bg-bark-700" style="width: {{ $pos['persen'] }}%"></div>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="whitespace-nowrap text-[13.5px] font-bold tabular-nums text-ink">{{ $rupiah($pos['nilai']) }}</p>
                            <span @class([
                                'mt-1.5 inline-block rounded-md px-2 py-[3px] text-[9.5px] font-semibold',
                                'bg-sandgold text-bronze' => (($pos['status'] ?? '') === 'lunas'),
                                'bg-sand text-muted' => (($pos['status'] ?? '') === 'netral'),
                            ])>{{ $pos['tag'] }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>

            <footer class="flex items-center justify-between gap-3 border-t border-line bg-bark-50 px-5 py-3.5">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Total Beban</p>
                <p class="whitespace-nowrap text-[17px] font-bold tabular-nums text-ink">{{ array_sum(array_column($alokasiBeban, 'nilai')) }}</p>
            </footer>
        </section>
    </div>

    {{-- Tren enam bulan --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-white">
        <header class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
            <div>
                <h2 class="text-[15px] font-bold text-ink">Ringkasan Tren 6 Bulan Terakhir</h2>
                <p class="mt-0.5 text-[11.5px] leading-relaxed text-muted">Laporan komparatif performa profitabilitas bimbel QUALIFY periode Desember 2024 - Mei 2025.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-sandgold px-2.5 py-1 text-[10.5px] font-semibold text-bronze">
                @include('layouts.partials.icon', ['name' => 'trending-up', 'class' => 'h-3.5 w-3.5'])
                Rata-rata Margin: 66,4%
            </span>
        </header>

        <div class="overflow-x-auto border-t border-line">
            <table class="w-full min-w-[720px] text-left">
                <thead>
                    <tr class="border-b border-line">
                        <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Bulan</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Pemasukan</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Pengeluaran</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Selisih (Laba Bersih)</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Margin</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Status Kas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($trenBulanan as $baris)
                        <tr>
                            <td class="px-5 py-3.5">
                                <span class="flex items-center gap-2">
                                    @if ($baris['periode_ini'])
                                        <span class="h-2 w-2 shrink-0 rounded-full bg-gold"></span>
                                    @endif
                                    <span @class(['text-[13px] leading-snug', 'font-bold text-ink' => $baris['periode_ini'], 'font-medium text-bark-500' => ! $baris['periode_ini']])>{{ $baris['bulan'] }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span @class(['block text-[10px]', 'text-bark-400' => ! $baris['periode_ini'], 'text-ink' => $baris['periode_ini']])>Rp</span>
                                <span @class(['text-[13px] tabular-nums', 'font-bold text-ink' => $baris['periode_ini'], 'font-medium text-bark-500' => ! $baris['periode_ini']])>{{ number_format($baris['masuk'], 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="block text-[10px] text-bark-300">Rp</span>
                                <span @class(['text-[13px] tabular-nums', 'font-bold text-bark-400' => $baris['periode_ini'], 'font-medium text-bark-300' => ! $baris['periode_ini']])>{{ number_format($baris['keluar'], 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="block text-[10px] text-bark-400">Rp</span>
                                <span @class(['text-[13px] tabular-nums', 'font-bold text-ink' => $baris['periode_ini'], 'font-semibold text-bark-500' => ! $baris['periode_ini']])>{{ number_format($baris['laba'], 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span @class([
                                    'inline-block rounded-md px-2 py-1 text-[11px] font-bold tabular-nums',
                                    'bg-sandgold text-bronze' => $baris['margin_nilai'] >= 68,
                                    'bg-sand text-muted' => $baris['margin_nilai'] < 68,
                                ])>{{ $baris['margin'] }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="inline-flex items-center justify-end gap-1.5 whitespace-nowrap text-[11.5px] font-semibold text-bronze">
                                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5 text-gold'])
                                    {{ $baris['kas'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- Catatan audit --}}
    <section class="flex flex-col gap-2 rounded-2xl border border-line bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-muted">
            @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'mt-[1px] h-4 w-4 shrink-0 text-gold'])
            Data keuangan telah diaudit internal • Buku Kas ditutup otomatis setiap akhir bulan pukul 23.59 WIB
        </p>
        <p class="shrink-0 text-[11px] text-muted sm:text-right">Terakhir disinkronkan: Hari ini, 09:15 WIB</p>
    </section>
</div>
@endsection