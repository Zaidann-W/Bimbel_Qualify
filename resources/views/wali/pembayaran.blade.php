@extends('layouts.wali')

@section('title', 'Pembayaran - Orang tua')

@section('content')
@php
    $selected = $selected_anak ?? null;
    $tagihanList = $tagihan ?? [];
    $ringkasan = $ringkasan ?? [];
    $periodeOptions = $periode_options ?? [];
    $anakList = $anak ?? [];
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[24px] font-bold tracking-tight text-bark-800">Pembayaran &amp; Tagihan Bimbingan</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Pantau status tagihan SPP rutin dan riwayat pembayaran putra-putri Anda secara transparan.</p>
        </div>
        @if (count($anakList) > 1)
            <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                {{ $selected['nama'] ?? '-' }} <span class="font-medium text-bark-400">({{ count($anakList) }} Anak)</span>
                @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5'])
            </button>
        @else
            <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50" disabled>
                {{ $selected['nama'] ?? '-' }}
                @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5'])
            </button>
        @endif
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        {{-- Sisa tagihan --}}
        <section class="rounded-2xl border border-gold/60 bg-gold/10 px-5 py-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-espresso/80">Sisa Tagihan yang Perlu Dibayar</p>
                    <p class="mt-1.5 text-[26px] font-bold leading-none tracking-tight text-bark-800">{{ $ringkasan['sisa_tagihan'] ?? 'Rp 0' }}</p>
                    @php
                        $terdekat = null;
                        foreach ($tagihanList as $t) {
                            if ($t['status'] === 'belum_lunas') {
                                $terdekat = $t;
                                break;
                            }
                        }
                    @endphp
                    @if ($terdekat)
                        <p class="mt-2 inline-flex items-center gap-1.5 text-[11.5px] font-semibold text-rose-600">
                            @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-3.5 w-3.5'])
                            Jatuh tempo: {{ $terdekat['tanggal_jatuh_tempo'] }}
                        </p>
                    @else
                        <p class="mt-2 inline-flex items-center gap-1.5 text-[11.5px] font-semibold text-emerald-600">
                            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                            Semua tagihan sudah lunas
                        </p>
                    @endif
                </div>
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'hourglass', 'class' => 'h-5 w-5'])
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                @if ($terdekat)
                    <p class="text-[11.5px] text-bark-600">Tagihan berjalan: <span class="font-bold text-bark-800">{{ $terdekat['nama'] }}</span></p>
                @else
                    <p class="text-[11.5px] text-bark-600">Tagihan berjalan: <span class="font-bold text-bark-800">-</span></p>
                @endif
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-500 px-3.5 py-2 text-[12px] font-bold text-white transition hover:bg-ember-600 {{ $terdekat ? '' : 'opacity-50 cursor-not-allowed' }}">
                    @include('layouts.partials.icon', ['name' => 'zap', 'class' => 'h-3.5 w-3.5'])
                    Bayar Instan Gateway
                </button>
            </div>
        </section>

        {{-- Total terbayar --}}
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50/60 px-5 py-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-emerald-700/80">Total Sudah Terbayar (Lunas)</p>
                    <p class="mt-1.5 text-[26px] font-bold leading-none tracking-tight text-emerald-700">{{ $ringkasan['total_terbayar'] ?? 'Rp 0' }}</p>
                    <p class="mt-2 text-[11.5px] text-bark-500">Seluruh pembayaran yang telah diverifikasi</p>
                </div>
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-emerald-500 text-white">
                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-5 w-5'])
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                <p class="text-[11.5px] text-bark-600">Status Pelunasan</p>
                <span class="rounded-md bg-emerald-500 px-2.5 py-1 text-[10.5px] font-bold text-white">{{ $ringkasan['status_pelunasan'] ?? '0% Lunas' }}</span>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">

        {{-- Kiri: daftar tagihan --}}
        <section class="self-start overflow-hidden rounded-2xl border border-bark-100 bg-white">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-5 py-4">
                <div>
                    <h2 class="text-[15.5px] font-bold text-bark-800">Daftar Tagihan &amp; Riwayat Transaksi</h2>
                    <p class="mt-0.5 text-[11.5px] text-bark-400">Menampilkan tagihan rutin SPP dan kegiatan akademik.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                        {{ $selected['kelas'] ?? 'Semua Periode' }}
                        @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5'])
                    </button>
                    @if (count($periodeOptions) > 0)
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[12px] font-bold text-bark-600 transition hover:bg-bark-50">
                            @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-3.5 w-3.5'])
                            Filter Periode
                        </button>
                    @else
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[12px] font-bold text-bark-600 transition hover:bg-bark-50">
                            @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-3.5 w-3.5'])
                            Unduh Rek PDF
                        </button>
                    @endif
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left">
                    <thead>
                        <tr class="border-b border-bark-100 bg-bark-50/60 text-[10px] font-bold uppercase tracking-wide text-bark-400">
                            <th class="px-5 py-3">Tagihan</th>
                            <th class="px-4 py-3">Nominal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-bark-100">
                        @if (count($tagihanList) > 0)
                            @foreach ($tagihanList as $tagihan)
                                @php
                                    $lunas = $tagihan['status'] === 'lunas';
                                @endphp
                                <tr class="transition hover:bg-bark-50/60">
                                    <td class="px-5 py-3.5">
                                        <p class="text-[13.5px] font-bold text-bark-800">{!! $tagihan['nama'] !!}</p>
                                        <p @class(['mt-0.5 text-[11.5px] font-medium', 'text-rose-600' => ! $lunas, 'text-bark-400' => $lunas])>{{ $tagihan['jadwal'] }}</p>
                                        <p class="mt-0.5 text-[10px] text-bark-400">{{ $tagihan['invoice_number'] }} • {{ $tagihan['periode'] }}</p>
                                    </td>
                                    <td class="px-4 py-3.5 text-[13.5px] font-bold text-bark-800">{{ $tagihan['nominal'] }}</td>
                                    <td class="px-4 py-3.5">
                                        <span @class([
                                            'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold',
                                            'bg-gold/30 text-espresso' => ! $lunas,
                                            'bg-emerald-50 text-emerald-600' => $lunas,
                                        ])>
                                            @if ($lunas)
                                                @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                                            @endif
                                            {{ $tagihan['status_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($lunas)
                                            <button type="button" class="inline-flex items-center gap-1.5 text-[12px] font-bold text-bark-600 transition hover:text-bark-800">
                                                @include('layouts.partials.icon', ['name' => 'receipt', 'class' => 'h-3.5 w-3.5'])
                                                Kwitansi
                                            </button>
                                        @else
                                            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-500 px-3 py-2 text-[11.5px] font-bold text-white transition hover:bg-ember-600">
                                                @include('layouts.partials.icon', ['name' => 'zap', 'class' => 'h-3.5 w-3.5'])
                                                Bayar Instan
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-bark-400">
                                    Belum ada data tagihan.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-bark-100 px-5 py-3.5">
                <p class="text-[11.5px] text-bark-400">Menampilkan seluruh riwayat transaksi untuk akun wali {{ optional($guardian)['name'] ?? '-' }}.</p>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3 py-1.5 text-[11.5px] font-semibold text-bark-500 transition hover:bg-bark-50">
                    @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-3.5 w-3.5'])
                    Filter Periode
                </button>
            </footer>
        </section>

        {{-- Kanan: ringkasan + bantuan --}}
        <div class="space-y-6">

            <section class="self-start rounded-2xl border border-bark-100 bg-white px-4 py-4">
                <header class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="text-[14.5px] font-bold text-bark-800">Tagihan Perlu Dibayar</h2>
                        <p class="mt-0.5 text-[11.5px] text-bark-400">{{ $selected['nama'] ?? '-' }} • Tagihan belom lunas</p>
                    </div>
                    <span class="shrink-0 rounded-md bg-gold px-2.5 py-1.5 text-[11.5px] font-bold text-espresso">{{ $ringkasan['total_sisa'] ?? 'Rp 0' }}</span>
                </header>

                {{-- Bagian payment gateway di-skip per kebutuhan; hanya tampilkan opsi tunai --}}
                <div class="mt-3 flex items-center justify-between gap-2">
                    <p class="text-[12px] font-bold text-bark-700">Metode Pembayaran</p>
                </div>

                <label class="mt-2.5 flex cursor-pointer items-start gap-2.5 rounded-xl border border-bark-100 bg-white px-3 py-2.5 transition hover:bg-bark-50">
                    <input type="radio" name="metode" checked class="mt-0.5 h-3.5 w-3.5 shrink-0 accent-ember-500">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'building', 'class' => 'h-3.5 w-3.5 text-ember-600'])
                            <p class="text-[12.5px] font-bold text-bark-800">Tunai / Cash di Bimbel</p>
                            <span class="rounded-md bg-bark-100 px-1.5 py-[2px] text-[9px] font-bold uppercase text-bark-500">Offline</span>
                        </div>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-bark-400">Bayar langsung di kasir/resepsionis saat pertemuan berikutnya di Bimbel QUALIFY. Kuitansi resmi &amp; digital akan langsung diberikan oleh staf kasir.</p>
                    </div>
                </label>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-bark-100 pt-3 text-[10.5px] text-bark-400">
                    <span class="inline-flex items-center gap-1 font-semibold">
                        @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'h-3.5 w-3.5 text-emerald-500'])
                        256-bit Enkripsi Aman
                    </span>
                    <a href="#" class="font-bold text-bark-500 hover:text-bark-700">Bantuan Keuangan</a>
                </div>
            </section>

            <section class="rounded-2xl border border-bark-100 bg-white px-4 py-4">
                <div class="flex items-start gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ember-50 text-ember-600">
                        @include('layouts.partials.icon', ['name' => 'phone', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-[13.5px] font-bold text-bark-800">Butuh Layanan Keuangan?</h3>
                        <p class="mt-1 text-[11.5px] leading-relaxed text-bark-500">Bisa hubungi: <span class="font-bold text-bark-700">+62 812-4455-8891 (WhatsApp)</span></p>
                        <p class="mt-0.5 text-[11px] text-bark-400">Layanan: Senin – Sabtu, 08.00 – 18.00 WIB</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
