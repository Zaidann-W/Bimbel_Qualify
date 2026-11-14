@extends('layouts.admin')

@section('content')
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Laporan Keuangan & SPP
            </h1>
            <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Rekapitulasi tagihan SPP, pembayaran, dan transaksi keuangan bimbel
            </p>
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
                Rp {{ number_format($data['summary']['total_tagihan'] ?? 0, 0, ',', '.') }}
            </p>
        </article>

        <article class="rounded-xl border border-line border-t-[3px] border-t-forest-500 bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'receipt', 'class' => 'h-4 w-4 text-forest-600'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Total Terbayar</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">
                Rp {{ number_format($data['summary']['total_terbayar'] ?? 0, 0, ',', '.') }}
            </p>
        </article>

        <article class="rounded-xl border border-line border-t-[3px] border-t-ember-400 bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-4 w-4 text-ember-600'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Belum Dibayar</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">
                Rp {{ number_format($data['summary']['total_belum_bayar'] ?? 0, 0, ',', '.') }}
            </p>
        </article>

        <article class="rounded-xl border border-espresso border-t-[3px] bg-espresso p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4 w-4 text-gold'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-bark-200">Lunas</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-white">
                {{ $data['summary']['total_lunas'] ?? 0 }}
            </p>
        </article>
    </div>
</div>

<!-- Tabel Data -->
<div class="px-4 py-5 sm:px-6">
    <section class="overflow-hidden rounded-2xl border border-line bg-white">

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
                    @if (empty($data['rows']))
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-muted">
                                Tidak ada data keuangan yang cocok dengan filter.
                            </td>
                        </tr>
                    @else
                        @foreach ($data['rows'] as $row)
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
                Menampilkan {{ count($data['rows'] ?? []) }} data keuangan
            </p>
        </div>
    </section>
</div>
@endsection