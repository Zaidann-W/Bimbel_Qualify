@extends('layouts.owner')

@section('title', 'Laporan Absensi')

@section('content')
@php
    $rekapJenjang = $rekapJenjang ?? [];
    $rekapTentor = $rekapTentor ?? [];
    $summary = $summary ?? [];
    $filters = $filters ?? ['periode' => 'Bulan Ini', 'jenjang' => 'Semua'];
    $periodeAktif = request()->query('periode', 'Bulan Ini');

    $statusBadge = [
        'TINGGI' => ['bg-sandgold text-bronze', 'star'],
        'BAIK' => ['bg-sand text-muted', 'check-circle'],
        'PERLU PERHATIAN' => ['bg-ember-100 text-ember-600', 'info'],
        'BELUM ADA DATA' => ['bg-sand text-muted', 'info'],
        'TEPAT' => ['bg-sandgold text-bronze', 'check-circle'],
    ];
@endphp

{{-- Kepala halaman + filter cepat --}}
<section class="rounded-b-2xl bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-xl">
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1.5">
                <span class="inline-flex items-center gap-1.5 rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold text-bronze">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                    PRD 5.1 • Modul Presensi Terpadu
                </span>
                <span class="text-[11px] text-muted">Pembaruan Real-Time 15 Menit Lalu</span>
            </div>
            <h1 class="mt-2.5 text-[26px] font-bold leading-tight tracking-tight text-ink sm:text-[30px]">Laporan Kehadiran & Rekap Absensi</h1>
            <p class="mt-1.5 max-w-md text-[13px] leading-relaxed text-muted">Pemantauan tingkat kehadiran siswa dan ketepatan mengajar tentor seluruh cabang secara komprehensif.</p>
        </div>

        <form method="GET" action="{{ route('owner.laporanAbsensi') }}" class="flex flex-col gap-2.5 lg:items-end">
            <div class="relative w-full lg:w-[240px]">
                @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300'])
                <select aria-label="Pilih tingkat kelas" name="jenjang" onchange="this.form.submit()"
                        class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-9 pr-9 text-[13px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                    <option value="Semua" {{ $filters['jenjang'] === 'Semua' ? 'selected' : '' }}>Semua Kelas</option>
                    <option value="SMA" {{ $filters['jenjang'] === 'SMA' ? 'selected' : '' }}>SMA (Kelas 10, 11 & 12)</option>
                    <option value="SMP" {{ $filters['jenjang'] === 'SMP' ? 'selected' : '' }}>SMP</option>
                    <option value="INTENSIF" {{ $filters['jenjang'] === 'INTENSIF' ? 'selected' : '' }}>Program Intensif & Garansi</option>
                </select>
                @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-400'])
            </div>

            <div class="relative w-full lg:w-[240px]">
                @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300'])
                <select aria-label="Pilih rentang periode" name="periode" onchange="this.form.submit()"
                        class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-9 pr-9 text-[13px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                    <option value="Bulan Ini" {{ $periodeAktif === 'Bulan Ini' ? 'selected' : '' }}>Bulan Ini ({{ now()->locale('id')->translatedFormat('F Y') }})</option>
                    <option value="{{ now()->subMonth()->format('F Y') }}" {{ $periodeAktif === now()->subMonth()->format('F Y') ? 'selected' : '' }}>{{ now()->subMonth()->locale('id')->translatedFormat('F Y') }}</option>
                    <option value="{{ now()->subMonths(2)->format('F Y') }}" {{ $periodeAktif === now()->subMonths(2)->format('F Y') ? 'selected' : '' }}>{{ now()->subMonths(2)->locale('id')->translatedFormat('F Y') }}</option>
                    <option value="TA" {{ $periodeAktif === 'TA' ? 'selected' : '' }}>Tahun Ajaran {{ now()->year }}/{{ now()->year + 1 }}</option>
                </select>
                @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-400'])
            </div>

            <a href="{{ route('owner.laporanAbsensi', array_merge(request()->query(), ['export' => 'csv', 'tab' => 'jenjang'])) }}"
               class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[13px] font-bold text-espresso transition hover:bg-gold/80 lg:w-auto">
                @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4'])
                Unduh Rekap CSV
            </a>
        </form>
    </div>

    {{-- Tiga angka pokok periode berjalan --}}
    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- Kehadiran siswa --}}
        <article class="rounded-xl border-x border-b border-x-line border-b-line border-t-[3px] border-t-gold bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Tingkat Disiplin Belajar</p>
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'users', 'class' => 'h-4 w-4'])
                </span>
            </div>
            <p class="mt-1.5 text-[13.5px] font-bold text-ink">Kehadiran Siswa</p>
            <div class="mt-2.5 flex items-baseline gap-2">
                <p class="text-[30px] font-bold leading-none tracking-tight tabular-nums text-ink">{{ $summary['siswaRate'] ?? 0 }}%</p>
                @if (($summary['siswaDelta'] ?? null) !== null)
                    @php $deltaNaik = $summary['siswaDelta'] >= 0; @endphp
                    <span @class(['inline-flex items-center gap-0.5 text-[11px] font-bold', $deltaNaik ? 'text-forest-600' : 'text-ember-600'])>
                        @include('layouts.partials.icon', ['name' => $deltaNaik ? 'arrow-up-right' : 'arrow-down-right', 'class' => 'h-3.5 w-3.5'])
                        {{ ($deltaNaik ? '+' : '').number_format($summary['siswaDelta'], 1, ',', '') }}%
                    </span>
                @endif
            </div>
            <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-muted">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                <span class="font-bold text-ink">{{ $summary['siswaDetail'] ?? '0 Sesi Hadir' }}</span>
            </p>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full rounded-full bg-gold" style="width: {{ $summary['siswaRate'] ?? 0 }}%"></div>
            </div>
        </article>

        {{-- Ketepatan waktu tentor --}}
        <article class="rounded-xl border-x border-b border-x-line border-b-line border-t-[3px] border-t-espresso bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Integritas Akademik</p>
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'users-check', 'class' => 'h-4 w-4'])
                </span>
            </div>
            <p class="mt-1.5 text-[13.5px] font-bold text-ink">Ketepatan Waktu Tentor</p>
            <div class="mt-2.5 flex items-baseline gap-2">
                <p class="text-[30px] font-bold leading-none tracking-tight tabular-nums text-ink">{{ $summary['tentorRate'] ?? 0 }}%</p>
                <span class="inline-flex items-center gap-0.5 text-[11px] font-bold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5 text-gold'])
                    {{ $summary['tentorStatus'] ?? 'Optimal' }}
                </span>
            </div>
            <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-muted">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-espresso"></span>
                <span class="font-bold text-ink">{{ $summary['tentorDetail'] ?? '0 Tentor Aktif' }}</span>
            </p>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full rounded-full bg-espresso" style="width: {{ $summary['tentorRate'] ?? 0 }}%"></div>
            </div>
        </article>

        {{-- Total sesi terlaksana --}}
        <article class="rounded-xl border-x border-b border-x-line border-b-line border-t-[3px] border-t-gold bg-white p-4 shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Realisasi Operasional</p>
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'h-4 w-4'])
                </span>
            </div>
            <p class="mt-1.5 text-[13.5px] font-bold text-ink">Total Sesi Terlaksana</p>
            <div class="mt-2.5 flex items-baseline gap-1.5">
                <p class="text-[30px] font-bold leading-none tracking-tight tabular-nums text-ink">{{ $summary['totalSesi'] ?? 0 }}</p>
                <p class="text-[13px] font-bold text-muted">Sesi Kelas</p>
            </div>
            <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-muted">
                <span class="inline-flex items-center gap-1">
                    @include('layouts.partials.icon', ['name' => 'sun', 'class' => 'h-3.5 w-3.5 text-gold'])
                    <span><span class="font-bold text-ink">Sesi Sore:</span> {{ $summary['soreCount'] ?? 0 }} sesi</span>
                </span>
                <span class="text-bark-300">&</span>
                <span class="inline-flex items-center gap-1">
                    @include('layouts.partials.icon', ['name' => 'moon', 'class' => 'h-3.5 w-3.5 text-bark-400'])
                    <span><span class="font-bold text-ink">Malam:</span> {{ $summary['malamCount'] ?? 0 }} sesi</span>
                </span>
            </p>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full rounded-full bg-gold" style="width: {{ ($summary['totalSesi'] ?? 0) > 0 ? round(($summary['soreCount'] / $summary['totalSesi']) * 100) : 0 }}%"></div>
            </div>
        </article>
    </div>
</section>

{{-- Rekap detail --}}
<div class="px-4 py-5 sm:px-6">
    <section class="overflow-hidden rounded-2xl border border-line bg-white">
        <div class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">

            {{-- Tab rekap --}}
            <div class="flex gap-1 rounded-xl bg-sand p-1.5" role="tablist" aria-label="Pilih rekap absensi">
                <button type="button" role="tab" id="tab-jenjang" aria-controls="panel-jenjang" aria-selected="true" data-tab-button="jenjang"
                        class="group flex flex-1 items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-left transition aria-selected:bg-white aria-selected:shadow-[0_1px_3px_rgba(20,20,20,0.08)] lg:flex-none">
                    <span class="text-bark-400 group-aria-selected:text-bronze">
                        @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'h-4 w-4'])
                    </span>
                    <span class="flex-1 text-[12.5px] font-bold leading-tight lg:max-w-[130px]">Rekap Kehadiran Siswa per Jenjang</span>
                    <span class="shrink-0 rounded-full bg-sandgold px-2 py-1 text-[10px] font-bold text-bronze">{{ count($rekapJenjang) }} Kelas</span>
                </button>

                <button type="button" role="tab" id="tab-tentor" aria-controls="panel-tentor" aria-selected="false" data-tab-button="tentor"
                        class="group flex flex-1 items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-left transition hover:bg-white/60 aria-selected:bg-white aria-selected:shadow-[0_1px_3px_rgba(20,20,20,0.08)] lg:flex-none">
                    <span class="text-bark-400 group-aria-selected:text-bronze">
                        @include('layouts.partials.icon', ['name' => 'user-check', 'class' => 'h-4 w-4'])
                    </span>
                    <span class="flex-1 text-[12.5px] font-bold leading-tight text-muted group-aria-selected:text-ink lg:max-w-[130px]">Rekap Mengajar Tentor</span>
                    <span class="shrink-0 rounded-full bg-sand px-2 py-1 text-[10px] font-bold text-muted">{{ count($rekapTentor) }} Pengajar</span>
                </button>
            </div>

            {{-- Pencarian --}}
            <div class="relative w-full lg:w-[300px]">
                @include('layouts.partials.icon', ['name' => 'search', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300'])
                <input type="search" data-table-search placeholder="Cari kelas atau nama tentor..." aria-label="Cari kelas atau nama tentor"
                       class="w-full rounded-lg border border-line bg-white py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
            </div>
        </div>

        {{-- Panel: rekap kehadiran siswa per jenjang --}}
        <div id="panel-jenjang" role="tabpanel" aria-labelledby="tab-jenjang" data-tab-panel="jenjang">
            <div class="overflow-x-auto border-t border-line">
                <table class="w-full min-w-[820px] text-left">
                    <thead>
                        <tr class="border-b border-line">
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Jenjang / Kelas</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Jumlah Siswa</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Total Pertemuan</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Hadir</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Izin / Sakit</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Alpa</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Status Disiplin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($rekapJenjang as $baris)
                            <tr class="transition hover:bg-bark-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span @class(['grid h-9 w-9 shrink-0 place-items-center rounded-lg text-[10.5px] font-bold', $baris['avatar']])>{{ $baris['kode'] }}</span>
                                        <div class="min-w-0">
                                            <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $baris['nama'] }}</p>
                                            <p class="mt-0.5 text-[11px] leading-relaxed text-muted">{{ $baris['rincian'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-[12.5px] font-medium whitespace-nowrap text-bark-500">{{ $baris['siswa'] }} Siswa</td>
                                <td class="px-5 py-4 text-[12.5px] font-medium whitespace-nowrap text-bark-500">{{ $baris['pertemuan'] }} Sesi</td>
                                <td class="px-5 py-4 text-[13px] font-bold whitespace-nowrap tabular-nums text-ink">{{ $baris['hadir'] }} Sesi</td>
                                <td class="px-5 py-4 text-[12.5px] font-medium whitespace-nowrap tabular-nums text-bark-500">{{ $baris['izin'] }} Sesi</td>
                                <td class="px-5 py-4 text-[12.5px] font-medium whitespace-nowrap tabular-nums text-bark-500">{{ $baris['alpa'] }} Sesi</td>
                                <td class="px-5 py-4">
                                    @php [$badgeClass, $badgeIcon] = $statusBadge[$baris['status']] ?? ['bg-sand text-muted', 'check-circle']; @endphp
                                    <span @class([
                                        'inline-flex items-center gap-1 whitespace-nowrap rounded-md px-2.5 py-1 text-[10px] font-bold',
                                        $badgeClass,
                                    ])>
                                        @include('layouts.partials.icon', ['name' => $badgeIcon, 'class' => 'h-3 w-3'])
                                        {{ $baris['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-[12.5px] text-muted">Belum ada data rekap kehadiran siswa untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Panel: rekap mengajar tentor --}}
        <div id="panel-tentor" role="tabpanel" aria-labelledby="tab-tentor" data-tab-panel="tentor" hidden>
            <div class="overflow-x-auto border-t border-line">
                <table class="w-full min-w-[820px] text-left">
                    <thead>
                        <tr class="border-b border-line">
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Tentor</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Unit / Mata Ajar</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Sesi Diampu</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Hadir</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Telat</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Ketepatan</th>
                            <th scope="col" class="px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-muted">Status Ketepatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($rekapTentor as $tentor)
                            <tr class="transition hover:bg-bark-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span @class(['grid h-9 w-9 shrink-0 place-items-center rounded-lg text-[10.5px] font-bold', $tentor['avatar']])>{{ $tentor['inisial'] }}</span>
                                        <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $tentor['nama'] }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-[12.5px] font-medium whitespace-nowrap text-bark-500">{{ $tentor['rincian'] }}</td>
                                <td class="px-5 py-4 text-[12.5px] font-medium whitespace-nowrap tabular-nums text-bark-500">{{ $tentor['sesi'] }} Sesi</td>
                                <td class="px-5 py-4 text-[13px] font-bold whitespace-nowrap tabular-nums text-ink">{{ $tentor['hadir'] }} Sesi</td>
                                <td class="px-5 py-4 text-[12.5px] font-medium whitespace-nowrap tabular-nums text-bark-500">{{ $tentor['telat'] }} Kali</td>
                                <td class="px-5 py-4 text-[13px] font-bold whitespace-nowrap tabular-nums text-ink">{{ $tentor['ketepatan'] }}</td>
                                <td class="px-5 py-4">
                                    @php [$badgeClass, $badgeIcon] = $statusBadge[$tentor['status']] ?? ['bg-sand text-muted', 'check-circle']; @endphp
                                    <span @class([
                                        'inline-flex items-center gap-1 whitespace-nowrap rounded-md px-2.5 py-1 text-[10px] font-bold',
                                        $badgeClass,
                                    ])>
                                        @include('layouts.partials.icon', ['name' => $badgeIcon, 'class' => 'h-3 w-3'])
                                        {{ $tentor['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-[12.5px] text-muted">Belum ada data rekap mengajar tentor untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-line bg-bark-50 px-5 py-3.5">
            <a href="{{ route('owner.laporanAbsensi', array_merge(request()->query(), ['export' => 'csv', 'tab' => 'jenjang'])) }}" data-csv-link
               class="inline-flex items-center gap-2 text-[12px] font-bold text-bronze transition hover:text-espresso">
                @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4'])
                <span data-csv-label>Unduh Format CSV Kelas</span>
            </a>
            <p class="text-[11px] text-muted">Menampilkan <span data-row-count>{{ count($rekapJenjang) }}</span> dari <span>{{ count($rekapJenjang) }}</span> data rekap</p>
        </footer>
    </section>

    {{-- Catatan audit --}}
    <section class="mt-5 flex flex-col gap-2 rounded-2xl border border-line bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-muted">
            @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'mt-[1px] h-4 w-4 shrink-0 text-gold'])
            Kehadiran siswa & tentor dicatat otomatis melalui modul presensi terpadu • Rekap diperbarui setiap 15 menit
        </p>
        <p class="shrink-0 text-[11px] text-muted sm:text-right">Terakhir disinkronkan: {{ now()->format('d M Y, H:i') }} WIB</p>
    </section>
</div>

<script>
    (() => {
        const buttons = document.querySelectorAll('[data-tab-button]');
        const panels = document.querySelectorAll('[data-tab-panel]');
        const search = document.querySelector('[data-table-search]');
        const csvLabel = document.querySelector('[data-csv-label]');
        const csvLink = document.querySelector('[data-csv-link]');
        const rowCount = document.querySelector('[data-row-count]');

        const csvLabels = {
            jenjang: 'Unduh Format CSV Kelas',
            tentor: 'Unduh Format CSV Tentor',
        };

        const activePanel = () => document.querySelector('[data-tab-panel]:not([hidden])');

        const applySearch = () => {
            const keyword = (search?.value ?? '').trim().toLowerCase();
            const rows = activePanel()?.querySelectorAll('tbody tr') ?? [];
            let visible = 0;

            rows.forEach((row) => {
                const hidden = keyword !== '' && !row.textContent.toLowerCase().includes(keyword);
                row.hidden = hidden;
                if (!hidden) {
                    visible += 1;
                }
            });

            if (rowCount) {
                rowCount.textContent = String(visible);
            }
        };

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                const tab = button.dataset.tabButton;

                buttons.forEach((item) => item.setAttribute('aria-selected', String(item === button)));
                panels.forEach((panel) => {
                    panel.hidden = panel.dataset.tabPanel !== tab;
                });

                if (csvLabel) {
                    csvLabel.textContent = csvLabels[tab] ?? csvLabels.jenjang;
                }

                if (csvLink) {
                    const url = new URL(csvLink.href, window.location.origin);
                    url.searchParams.set('tab', tab);
                    csvLink.href = url.toString();
                }

                applySearch();
            });
        });

        search?.addEventListener('input', applySearch);
    })();
</script>
@endsection