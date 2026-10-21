@extends('layouts.owner')

@section('title', 'Laporan Absensi')

{{-- Seluruh angka di halaman ini masih mock UI sesuai rancangan docs/laporanabsensi-owner.png;
     sambungkan ke data presensi nyata (filter jenjang & periode, cetak PDF, ekspor CSV) begitu modul absensi tersedia --}}
@section('content')
@php
    $rekapJenjang = [
        ['kode' => '12S', 'nama' => '12 SMA Super Intensif', 'rincian' => 'Target UTBK SNBT Top PTN', 'siswa' => 14, 'pertemuan' => 24, 'hadir' => 330, 'izin' => 5, 'alpa' => 1, 'status' => 'TINGGI', 'avatar' => 'bg-gold text-espresso'],
        ['kode' => 'FK', 'nama' => '12 SMA Garansi FK', 'rincian' => 'Program Unggulan Kedokteran', 'siswa' => 12, 'pertemuan' => 24, 'hadir' => 281, 'izin' => 6, 'alpa' => 1, 'status' => 'TINGGI', 'avatar' => 'bg-sandgold text-bronze'],
        ['kode' => '11R', 'nama' => '11 SMA Reguler A', 'rincian' => 'Semester Genap Kurikulum Merdeka', 'siswa' => 14, 'pertemuan' => 20, 'hadir' => 266, 'izin' => 11, 'alpa' => 3, 'status' => 'BAIK', 'avatar' => 'bg-sand text-muted'],
        ['kode' => '10R', 'nama' => '10 SMA Reguler', 'rincian' => 'Penguatan Pondasi Sains & Sosio', 'siswa' => 15, 'pertemuan' => 20, 'hadir' => 284, 'izin' => 12, 'alpa' => 4, 'status' => 'BAIK', 'avatar' => 'bg-sand text-muted'],
        ['kode' => 'SMP', 'nama' => 'Kelas SMP Persiapan', 'rincian' => 'Persiapan Masuk SMA Favorit', 'siswa' => 12, 'pertemuan' => 18, 'hadir' => 207, 'izin' => 7, 'alpa' => 2, 'status' => 'TINGGI', 'avatar' => 'bg-sandgold text-bronze'],
    ];

    $rekapTentor = [
        ['inisial' => 'AR', 'nama' => 'Arif Setiawan, S.Pd.', 'rincian' => 'Matematika • Unit SMA', 'sesi' => 22, 'hadir' => 22, 'telat' => 0, 'ketepatan' => '100%', 'status' => 'TEPAT', 'avatar' => 'bg-gold text-espresso'],
        ['inisial' => 'DN', 'nama' => 'Dewi Anggraini, S.Pd.', 'rincian' => 'B. Indonesia • Unit SMA & SMP', 'sesi' => 20, 'hadir' => 19, 'telat' => 1, 'ketepatan' => '95,0%', 'status' => 'TEPAT', 'avatar' => 'bg-sandgold text-bronze'],
        ['inisial' => 'RP', 'nama' => 'Rizky Pratama, S.Kom.', 'rincian' => 'Informatika & Sains', 'sesi' => 18, 'hadir' => 17, 'telat' => 2, 'ketepatan' => '94,4%', 'status' => 'PERLU PERHATIAN', 'avatar' => 'bg-ember-100 text-ember-600'],
        ['inisial' => 'MS', 'nama' => 'Maya Sartika, S.Pd.', 'rincian' => 'B. Inggris • Semua Unit', 'sesi' => 21, 'hadir' => 21, 'telat' => 0, 'ketepatan' => '100%', 'status' => 'TEPAT', 'avatar' => 'bg-gold text-espresso'],
        ['inisial' => 'HK', 'nama' => 'Hendra Kusuma, S.Si.', 'rincian' => 'Kimia & Fisika • Unit SMA', 'sesi' => 19, 'hadir' => 19, 'telat' => 0, 'ketepatan' => '100%', 'status' => 'TEPAT', 'avatar' => 'bg-sandgold text-bronze'],
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
            <h1 class="mt-2.5 text-[26px] font-bold leading-tight tracking-tight text-ink sm:text-[30px]">Laporan Kehadiran &amp; Rekap Absensi</h1>
            <p class="mt-1.5 max-w-md text-[13px] leading-relaxed text-muted">Pemantauan tingkat kehadiran siswa dan ketepatan mengajar tentor seluruh cabang secara komprehensif.</p>
        </div>

        <div class="flex flex-col gap-2.5 lg:items-end">
            <div class="relative w-full lg:w-[240px]">
                @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300'])
                <select aria-label="Pilih tingkat kelas"
                        class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-9 pr-9 text-[13px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                    <option>Semua Kelas</option>
                    <option>SMA (Kelas 10, 11 &amp; 12)</option>
                    <option>SMP</option>
                    <option>Program Intensif &amp; Garansi</option>
                </select>
                @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-400'])
            </div>

            <div class="relative w-full lg:w-[240px]">
                @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-300'])
                <select aria-label="Pilih rentang periode"
                        class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-9 pr-9 text-[13px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                    <option>Bulan Ini (Mei 2025)</option>
                    <option>April 2025</option>
                    <option>Maret 2025</option>
                    <option>Februari 2025</option>
                    <option>Januari 2025</option>
                    <option>Tahun Ajaran 2024/2025</option>
                </select>
                @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-400'])
            </div>

            <button type="button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[13px] font-bold text-espresso transition hover:bg-gold/80 lg:w-auto">
                @include('layouts.partials.icon', ['name' => 'printer', 'class' => 'h-4 w-4'])
                Cetak Rekap Absensi
            </button>
        </div>
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
                <p class="text-[30px] font-bold leading-none tracking-tight tabular-nums text-ink">96.4%</p>
                <span class="inline-flex items-center gap-0.5 text-[11px] font-bold text-gold">
                    @include('layouts.partials.icon', ['name' => 'arrow-up-right', 'class' => 'h-3.5 w-3.5'])
                    +0.8%
                </span>
            </div>
            <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-muted">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                <span class="font-bold text-ink">1.339 Sesi Hadir</span>
                <span>Total 1.392 Jadwal</span>
            </p>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full w-[96.4%] rounded-full bg-gold"></div>
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
                <p class="text-[30px] font-bold leading-none tracking-tight tabular-nums text-ink">99.1%</p>
                <span class="inline-flex items-center gap-0.5 text-[11px] font-bold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5 text-gold'])
                    Optimal
                </span>
            </div>
            <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-muted">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-espresso"></span>
                <span class="font-bold text-ink">24 Tentor Aktif Bertugas</span>
                <span>0 Sesi Tanpa Pengganti</span>
            </p>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full w-[99.1%] rounded-full bg-espresso"></div>
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
                <p class="text-[30px] font-bold leading-none tracking-tight tabular-nums text-ink">148</p>
                <p class="text-[13px] font-bold text-muted">Sesi Kelas</p>
            </div>
            <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-muted">
                <span class="inline-flex items-center gap-1">
                    @include('layouts.partials.icon', ['name' => 'sun', 'class' => 'h-3.5 w-3.5 text-gold'])
                    <span><span class="font-bold text-ink">Sesi Sore:</span> 82 sesi</span>
                </span>
                <span class="text-bark-300">&amp;</span>
                <span class="inline-flex items-center gap-1">
                    @include('layouts.partials.icon', ['name' => 'moon', 'class' => 'h-3.5 w-3.5 text-bark-400'])
                    <span><span class="font-bold text-ink">Malam:</span> 66 sesi</span>
                </span>
            </p>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-bark-100">
                <div class="h-full w-[62%] rounded-full bg-gold"></div>
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
                    <span class="shrink-0 rounded-full bg-sandgold px-2 py-1 text-[10px] font-bold text-bronze">5 Kelas</span>
                </button>

                <button type="button" role="tab" id="tab-tentor" aria-controls="panel-tentor" aria-selected="false" data-tab-button="tentor"
                        class="group flex flex-1 items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-left transition hover:bg-white/60 aria-selected:bg-white aria-selected:shadow-[0_1px_3px_rgba(20,20,20,0.08)] lg:flex-none">
                    <span class="text-bark-400 group-aria-selected:text-bronze">
                        @include('layouts.partials.icon', ['name' => 'user-check', 'class' => 'h-4 w-4'])
                    </span>
                    <span class="flex-1 text-[12.5px] font-bold leading-tight text-muted group-aria-selected:text-ink lg:max-w-[130px]">Rekap Mengajar Tentor</span>
                    <span class="shrink-0 rounded-full bg-sand px-2 py-1 text-[10px] font-bold text-muted">24 Pengajar</span>
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
                                    <span @class([
                                        'inline-flex items-center gap-1 whitespace-nowrap rounded-md px-2.5 py-1 text-[10px] font-bold',
                                        'bg-sandgold text-bronze' => $baris['status'] === 'TINGGI',
                                        'bg-sand text-muted' => $baris['status'] === 'BAIK',
                                    ])>
                                        @include('layouts.partials.icon', ['name' => $baris['status'] === 'TINGGI' ? 'star' : 'check-circle', 'class' => 'h-3 w-3'])
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
                                    <span @class([
                                        'inline-flex items-center gap-1 whitespace-nowrap rounded-md px-2.5 py-1 text-[10px] font-bold',
                                        'bg-sandgold text-bronze' => $tentor['status'] === 'TEPAT',
                                        'bg-ember-100 text-ember-600' => $tentor['status'] === 'PERLU PERHATIAN',
                                    ])>
                                        @include('layouts.partials.icon', ['name' => $tentor['status'] === 'TEPAT' ? 'check-circle' : 'info', 'class' => 'h-3 w-3'])
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
            <button type="button" class="inline-flex items-center gap-2 text-[12px] font-bold text-bronze transition hover:text-espresso">
                @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4'])
                <span data-csv-label>Unduh Format CSV Kelas</span>
            </button>
            <p class="text-[11px] text-muted">Menampilkan <span data-row-count>{{ count($rekapJenjang) }}</span> dari <span>{{ count($rekapJenjang) }}</span> data rekap</p>
        </footer>
    </section>

    {{-- Catatan audit --}}
    <section class="mt-5 flex flex-col gap-2 rounded-2xl border border-line bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-muted">
            @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'mt-[1px] h-4 w-4 shrink-0 text-gold'])
            Kehadiran siswa &amp; tentor dicatat otomatis melalui modul presensi terpadu • Rekap diperbarui setiap 15 menit
        </p>
        <p class="shrink-0 text-[11px] text-muted sm:text-right">Terakhir disinkronkan: Hari ini, 09:15 WIB</p>
    </section>
</div>

<script>
    (() => {
        const buttons = document.querySelectorAll('[data-tab-button]');
        const panels = document.querySelectorAll('[data-tab-panel]');
        const search = document.querySelector('[data-table-search]');
        const csvLabel = document.querySelector('[data-csv-label]');
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

                applySearch();
            });
        });

        search?.addEventListener('input', applySearch);
    })();
</script>
@endsection
