@extends('layouts.admin')

@section('title', 'Laporan Keuangan & SPP — Qualify')

@section('content')
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Laporan Keuangan &amp; SPP
            </h1>
            <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Rekapitulasi tagihan SPP, pembayaran, dan transaksi keuangan bimbel.
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('admin.laporanKeuangan.export', array_filter(array_merge($request->query(), ['format' => 'csv']))) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12.5px] font-bold text-espresso transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'layout-grid', 'class' => 'h-4 w-4 text-forest-600'])
                <span>Export Excel (CSV)</span>
            </a>
        </div>
    </div>
</section>

<!-- Ringkasan -->
<div class="px-4 py-5 sm:px-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
        <article class="rounded-xl border border-line border-t-[3px] border-t-gold bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'trending-up', 'class' => 'h-4 w-4 text-bronze'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Total Tagihan</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">
                Rp {{ number_format($summary['total_tagihan'] ?? 0, 0, ',', '.') }}
            </p>
        </article>

        <article class="rounded-xl border border-line border-t-[3px] border-t-forest-500 bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'receipt', 'class' => 'h-4 w-4 text-forest-600'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Total Terbayar</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">
                Rp {{ number_format($summary['total_terbayar'] ?? 0, 0, ',', '.') }}
            </p>
        </article>

        <article class="rounded-xl border border-line border-t-[3px] border-t-ember-400 bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-4 w-4 text-ember-600'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Belum Dibayar</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">
                Rp {{ number_format($summary['total_belum_bayar'] ?? 0, 0, ',', '.') }}
            </p>
        </article>

        <article class="rounded-xl border border-espresso border-t-[3px] bg-espresso p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4 w-4 text-gold'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-bark-200">Lunas</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-white">
                {{ $summary['total_lunas'] ?? 0 }}
            </p>
        </article>
    </div>
</div>

<!-- Filter dan Tabel -->
<div class="px-4 py-5 sm:px-6">
    <section class="overflow-hidden rounded-2xl border border-line bg-white">

        <!-- Filter -->
        <div class="px-5 py-4 border-b border-line">
            <form method="GET" action="{{ route('admin.laporanKeuangan') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <label class="block text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">Dari Tanggal</label>
                    <input name="start_date" type="date" value="{{ $request->query('start_date') ?? '' }}"
                           class="mt-2 w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[13.5px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/40">
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">Sampai Tanggal</label>
                    <input name="end_date" type="date" value="{{ $request->query('end_date') ?? '' }}"
                           class="mt-2 w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[13.5px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/40">
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">Status</label>
                    <select name="status" class="mt-2 w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[13.5px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/40">
                        <option value="">Semua Status</option>
                        @foreach ($statusList as $st)
                            <option value="{{ $st }}" {{ $request->query('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">Jenis Tagihan</label>
                    <select name="type" class="mt-2 w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[13.5px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/40">
                        <option value="">Semua Jenis</option>
                        @foreach ($typeList as $tp)
                            <option value="{{ $tp }}" {{ $request->query('type') === $tp ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $tp)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-lg bg-espresso px-3.5 py-2.5 text-[13.5px] font-bold text-white transition hover:bg-bark-800">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead>
                    <tr class="bg-shell">
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">No Invoice</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Nama Siswa</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">NIS</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Kelas</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Jenis</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Nominal</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Tgl Jatuh Tempo</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Tgl Bayar</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($data))
                        <tr>
                            <td colspan="10" class="px-5 py-12 text-center text-muted">
                                Tidak ada data keuangan yang cocok dengan filter.
                            </td>
                        </tr>
                    @else
                        @foreach ($data as $row)
                            <tr class="border-t border-line align-top odd:bg-white even:bg-shell">
                                <td class="px-5 py-4 text-[13px] font-bold text-ink">{{ $row['invoice_number'] }}</td>
                                <td class="px-5 py-4">
                                    <p class="text-[13.5px] font-bold text-ink">{{ $row['student_name'] }}</p>
                                    <p class="mt-1 text-[11.5px] text-muted">{{ $row['nis'] }}</p>
                                </td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['kelas'] }}</td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ ucfirst(str_replace('_', ' ', $row['type'])) }}</td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['description'] }}</td>
                                <td class="px-5 py-4 text-right text-[13.5px] font-bold tabular-nums text-ink">
                                    Rp {{ number_format($row['amount'], 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $badgeClass = match ($row['status']) {
                                            'lunas' => 'bg-forest-100 text-forest-700',
                                            'belum_bayar' => 'bg-ember-100 text-ember-700',
                                            'sebagian' => 'bg-gold text-espresso',
                                            'batal' => 'bg-bark-100 text-bark-700',
                                            default => 'bg-sand text-muted',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $row['status'])) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['due_date'] }}</td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['paid_at'] }}</td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['payment_method'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="border-t border-line px-5 py-4">
            <p class="text-[12px] leading-snug text-muted">
                Menampilkan {{ count($data) }} data keuangan
            </p>
        </div>
    </section>
</div>

@push('scripts')
<script>
    // Konfirmasi sebelum export
    document.addEventListener('DOMContentLoaded', () => {
        const exportLinks = document.querySelectorAll('a[href*="/export"]');
        exportLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                if (!confirm('Unduh laporan keuangan dalam format Excel (CSV)?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush