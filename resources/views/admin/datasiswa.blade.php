@extends('layouts.admin')

@section('title', 'Data Siswa & Kelas')

@section('content')
@php
    $status_pil = [
        'aktif' => ['label' => 'Aktif', 'kelas' => 'bg-sandgold text-bronze'],
        'cuti' => ['label' => 'Cuti', 'kelas' => 'bg-sand text-muted'],
        'nonaktif' => ['label' => 'Nonaktif', 'kelas' => 'bg-white text-ink ring-1 ring-line'],
        'lulus' => ['label' => 'Lulus', 'kelas' => 'bg-espresso text-white'],
    ];

    $jenjangLabel = [
        'SD' => 'Jenjang SD',
        'SMP' => 'Jenjang SMP',
        'SMA' => 'Jenjang SMA',
    ];

    $inisialMap = [];
    foreach ($siswa as $s) {
        $inisialMap[$s->id] = $s->inisial();
    }
@endphp

<?php /*  Kepala halaman */ ?>
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">
                Data Siswa
            </h1>
            <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Database induk siswa terdaftar dan informasi kontak wali bimbel QUALIFY.
            </p>
        </div>

        <div class="flex shrink-0 flex-col gap-2.5 sm:flex-row">
            <button type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-line bg-white px-4 py-3 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4 text-bronze'])
                Ekspor Data
            </button>
            <button type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-espresso px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'user-plus', 'class' => 'h-4 w-4 text-gold'])
                Tambah Siswa
            </button>
        </div>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    <?php /*  Tiga angka pokok data siswa */ ?>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($kpis as $kpi)
            <article class="rounded-xl border border-line bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">{{ $kpi['label'] }}</p>
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ $kpi['tile'] }}">
                        @include('layouts.partials.icon', ['name' => $kpi['ikon'], 'class' => 'h-4.5 w-4.5'])
                    </span>
                </div>
                <p class="mt-3 flex items-baseline gap-2">
                    <span class="text-[27px] font-extrabold leading-none tracking-tight tabular-nums {{ $kpi['nilai_kelas'] }}">{{ $kpi['nilai'] }}</span>
                    <span class="text-[12.5px] font-semibold text-muted">{{ $kpi['satuan'] }}</span>
                </p>
                <p class="mt-3.5 text-[11.5px] font-medium leading-snug text-bark-700">{{ $kpi['catatan'] }}</p>
            </article>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        <?php /*  Daftar siswa */ ?>
        <section class="overflow-hidden rounded-2xl border border-line bg-white xl:col-span-2">

            <?php /*  Pencarian & filter */ ?>
            <div class="flex flex-col gap-2.5 px-4 py-4 sm:flex-row sm:items-center sm:px-5">
                <div class="relative min-w-0 flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                        @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                    </span>
                    <input type="search" id="datasiswa-cari" aria-label="Cari nama siswa, NIS, atau nama wali"
                           placeholder="Cari nama siswa, NIS, atau nama wali..."
                           class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                </div>

                <div class="relative shrink-0 sm:w-44">
                    <select id="datasiswa-jenjang" aria-label="Filter jenjang sekolah"
                            class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-3 pr-9 text-[12.5px] font-semibold text-espresso transition hover:bg-sand focus:outline-none focus:ring-2 focus:ring-gold/30">
                        <option value="semua">Semua Jenjang</option>
                        <option value="sd">Jenjang SD</option>
                        <option value="smp">Jenjang SMP</option>
                        <option value="sma">Jenjang SMA</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 grid w-9 place-items-center text-bark-400">
                        @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                    </span>
                </div>

                <div class="relative shrink-0 sm:w-40">
                    <select id="datasiswa-status" aria-label="Filter status siswa"
                            class="w-full appearance-none rounded-lg border border-line bg-white py-2.5 pl-3 pr-9 text-[12.5px] font-semibold text-espresso transition hover:bg-sand focus:outline-none focus:ring-2 focus:ring-gold/30">
                        <option value="semua">Semua Status</option>
                        @foreach ($status_pil as $key => $info)
                            <option value="{{ $key }}">{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 grid w-9 place-items-center text-bark-400">
                        @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                    </span>
                </div>
            </div>

            <?php /*  Tabel */ ?>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] border-collapse text-left">
                    <thead>
                        <tr class="bg-shell">
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Nama Siswa &amp; NIS</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Orang Tua / Wali</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</th>
                            <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="datasiswa-body">
                        @forelse ($siswa as $row)
                            @php
                                $jenjang = $row->education_level ? strtolower($row->education_level) : 'sd';
                                $status = $status_pil[$row->status] ?? $status_pil['nonaktif'];
                                $wali = $row->guardians->where('pivot.is_primary_contact', true)->first()
                                    ?? $row->guardians->first();
                                $inisial = $inisialMap[$row->id] ?? strtoupper(substr($row->full_name, 0, 1) . substr($row->full_name, strrpos($row->full_name, ' ') + 1, 1));
                                $avatarKelas = $row->status === 'aktif' ? 'bg-espresso text-gold' : 'bg-sand text-bark-400';
                            @endphp
                            <tr data-baris data-jenjang="{{ $jenjang }}" data-status="{{ $row->status }}"
                                data-cari="{{ strtolower($row->full_name . ' ' . $row->nis . ' ' . $row->school_name . ' ' . ($wali ? $wali->name : '') . ' ' . $row->phone_wa) }}"
                                class="border-t border-line align-top odd:bg-white even:bg-shell">
                                <td class="px-5 py-4">
                                    <div class="flex items-start gap-3">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-[12.5px] font-bold {{ $avatarKelas }}" aria-hidden="true">
                                            {{ $inisial }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $row->full_name }}</p>
                                            <p class="mt-1.5 text-[11.5px] leading-snug text-muted">
                                                {{ $row->nis ?: '-' }} <span class="text-bark-300">•</span> {{ $row->school_name ?: '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[13px] font-medium leading-snug text-ink">{{ $wali ? $wali->name : '-' }}</p>
                                    <p class="mt-1.5 text-[11.5px] leading-snug text-muted">{{ $row->phone_wa ?: '-' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $status['kelas'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="#"
                                           class="inline-flex shrink-0 items-center rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand">
                                            Detail
                                        </a>
                                        <a href="#" aria-label="Edit data {{ $row->full_name }}"
                                           class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                            @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="datasiswa-kosong" class="hidden border-t border-line">
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <p class="text-[13.5px] font-bold text-ink">Tidak ada siswa yang cocok</p>
                                    <p class="mt-1.5 text-[12.5px] text-muted">Ubah kata kunci pencarian, pilih Semua Jenjang, atau pilih Semua Status.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <?php /*  Kaki tabel */ ?>
            <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-[12px] leading-snug text-muted">
                    Menampilkan <span id="datasiswa-dari" class="font-bold text-ink">{{ $siswa->firstItem() ?? 0 }} – {{ $siswa->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-ink">{{ $siswa->total() }}</span> Siswa Terdaftar
                </p>

                <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman daftar siswa">
                    {{ $siswa->links() }}
                </nav>
            </footer>
        </section>

        <?php /*  Distribusi jenjang */ ?>
        <section class="rounded-2xl border border-line bg-white p-5 self-start">
            <header class="flex items-center gap-2.5">
                <span class="h-2 w-2 shrink-0 rounded-full bg-gold"></span>
                <h2 class="text-[15px] font-bold leading-snug text-ink">Siswa per Jenjang</h2>
            </header>

            <div class="mt-4 space-y-4">
                @php
                    $sd = App\Models\Student::where('education_level', 'SD')->count();
                    $smp = App\Models\Student::where('education_level', 'SMP')->count();
                    $sma = App\Models\Student::where('education_level', 'SMA')->count();
                    $totalJenjang = $sd + $smp + $sma;
                    $bars = [
                        ['jenjang' => 'Jenjang SD', 'jumlah' => $sd . ' Siswa (' . ($totalJenjang ? round($sd / $totalJenjang * 100) : 0) . '%)', 'lebar' => $totalJenjang ? 'w-' . round($sd / $totalJenjang * 100) . '%' : 'w-0%'],
                        ['jenjang' => 'Jenjang SMP', 'jumlah' => $smp . ' Siswa (' . ($totalJenjang ? round($smp / $totalJenjang * 100) : 0) . '%)', 'lebar' => $totalJenjang ? 'w-' . round($smp / $totalJenjang * 100) . '%' : 'w-0%'],
                        ['jenjang' => 'Jenjang SMA', 'jumlah' => $sma . ' Siswa (' . ($totalJenjang ? round($sma / $totalJenjang * 100) : 0) . '%)', 'lebar' => $totalJenjang ? 'w-' . round($sma / $totalJenjang * 100) . '%' : 'w-0%'],
                    ];
                @endphp
                @foreach ($bars as $bar)
                    <div>
                        <div class="flex items-baseline justify-between gap-3">
                            <p class="text-[12.5px] font-semibold leading-snug text-muted">{{ $bar['jenjang'] }}</p>
                            <p class="shrink-0 text-[12px] font-bold leading-snug tabular-nums text-ink">{{ $bar['jumlah'] }}</p>
                        </div>
                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-sand"
                             role="img" aria-label="{{ $bar['jenjang'] }} {{ $bar['jumlah'] }}">
                            <span class="block h-full rounded-full bg-gold {{ $bar['lebar'] }}"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>

<script>
    (() => {
        const baris = Array.from(document.querySelectorAll('[data-baris]'));
        const inputCari = document.getElementById('datasiswa-cari');
        const selectJenjang = document.getElementById('datasiswa-jenjang');
        const selectStatus = document.getElementById('datasiswa-status');
        const barisKosong = document.getElementById('datasiswa-kosong');
        const labelDari = document.getElementById('datasiswa-dari');

        const terapkan = () => {
            const kata = inputCari.value.trim().toLowerCase();
            const jenjang = selectJenjang.value;
            const status = selectStatus.value;
            let tampil = 0;

            baris.forEach((barisData) => {
                const cocokKata = !kata || barisData.dataset.cari.includes(kata);
                const cocokJenjang = jenjang === 'semua' || barisData.dataset.jenjang === jenjang;
                const cocokStatus = status === 'semua' || barisData.dataset.status === status;
                const terlihat = cocokKata && cocokJenjang && cocokStatus;

                barisData.classList.toggle('hidden', !terlihat);
                if (terlihat) {
                    tampil += 1;
                }
            });

            barisKosong.classList.toggle('hidden', tampil > 0);
            labelDari.textContent = tampil === 0 ? '0' : `1 – ${tampil}`;
        };

        inputCari.addEventListener('input', terapkan);
        selectJenjang.addEventListener('change', terapkan);
        selectStatus.addEventListener('change', terapkan);
    })();
</script>
@endsection
