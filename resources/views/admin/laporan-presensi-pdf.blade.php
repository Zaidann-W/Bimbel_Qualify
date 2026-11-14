@extends('layouts.admin')

@section('content')
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Laporan Presensi & Kehadiran
            </h1>
            <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Rekapitulasi kehadiran siswa per kelas, periode, dan status kehadiran
            </p>
        </div>
    </div>
</section>

<!-- Ringkasan -->
<div class="px-4 py-5 sm:px-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
        <article class="rounded-xl border border-line border-t-[3px] border-t-gold bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'users', 'class' => 'h-4 w-4 text-bronze'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Total Data</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">
                {{ $data['summary']['total'] ?? 0 }}
            </p>
        </article>

        <article class="rounded-xl border border-line border-t-[3px] border-t-forest-500 bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4 w-4 text-forest-600'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Hadir</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">
                {{ $data['summary']['hadir'] ?? 0 }}
            </p>
        </article>

        <article class="rounded-xl border border-line border-t-[3px] border-t-ember-400 bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-4 w-4 text-ember-600'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Alpa</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">
                {{ $data['summary']['alpa'] ?? 0 }}
            </p>
        </article>

        <article class="rounded-xl border border-espresso border-t-[3px] bg-espresso p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'activity', 'class' => 'h-4 w-4 text-gold'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-bark-200">Tingkat Kehadiran</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-white">
                {{ $data['summary']['rate_hadir'] ?? 0 }}%
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
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Tanggal</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Waktu</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Kelas</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Nama Siswa</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">NIS</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status Kehadiran</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Catatan</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($data['rows']))
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-muted">
                                Tidak ada data presensi yang cocok dengan filter.
                            </td>
                        </tr>
                    @else
                        @foreach ($data['rows'] as $row)
                            <tr class="border-t border-line align-top odd:bg-white even:bg-shell">
                                <td class="px-5 py-4 text-[13px] font-bold text-ink">{{ $row['tanggal'] }}</td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['waktu'] }}</td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['kelas'] }}</td>
                                <td class="px-5 py-4 text-[13.5px] font-bold text-ink">{{ $row['nama_siswa'] }}</td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['nis'] }}</td>
                                <td class="px-5 py-4">
                                    @php
                                        $badgeClass = match ($row['status']) {
                                            'Hadir' => 'bg-forest-100 text-forest-700',
                                            'Izin' => 'bg-gold text-espresso',
                                            'Sakit' => 'bg-ember-100 text-ember-700',
                                            'Alpha' => 'bg-bark-100 text-bark-700',
                                            default => 'bg-sand text-muted',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $badgeClass }}">
                                        {{ $row['status'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['catatan'] }}</td>
                                <td class="px-5 py-4 text-[13px] text-ink">{{ $row['dicatat_oleh'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="border-t border-line px-5 py-4">
            <p class="text-[12px] leading-snug text-muted">
                Menampilkan {{ count($data['rows'] ?? []) }} data presensi
            </p>
        </div>
    </section>
</div>
@endsection