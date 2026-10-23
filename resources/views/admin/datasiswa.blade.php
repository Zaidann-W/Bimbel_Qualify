@extends('layouts.admin')

@section('title', 'Data Siswa & Kelas')

{{-- Seluruh baris di halaman ini masih mock UI sesuai rancangan docs/datasiswa-admin.png;
     sambungkan ke modul siswa + rombel kelas begitu tersedia --}}
@section('content')
@php
    $status_pil = [
        'aktif' => ['label' => 'Aktif', 'kelas' => 'bg-sandgold text-bronze'],
        'cuti' => ['label' => 'Cuti', 'kelas' => 'bg-sand text-muted'],
    ];

    $kpis = [
        [
            'label' => 'Total Siswa Aktif',
            'nilai' => '342',
            'satuan' => 'Siswa',
            'nilai_kelas' => 'text-ink',
            'ikon' => 'users',
            'tile' => 'bg-sand text-ink',
            'catatan' => 'Terdaftar semester ini',
        ],
        [
            'label' => 'Siswa Baru',
            'nilai' => '4',
            'satuan' => 'Siswa',
            'nilai_kelas' => 'text-bronze',
            'ikon' => 'user-plus',
            'tile' => 'bg-gold text-espresso',
            'catatan' => 'Menunggu verifikasi berkas',
        ],
        [
            'label' => 'Distribusi Jenjang',
            'nilai' => '3',
            'satuan' => 'Tingkatan',
            'nilai_kelas' => 'text-ink',
            'ikon' => 'graduation-cap',
            'tile' => 'bg-sand text-ink',
            'catatan' => 'SD (64) • SMP (128) • SMA (150)',
        ],
    ];

    $siswa = [
        [
            'inisial' => 'AZ',
            'avatar' => 'bg-espresso text-gold',
            'nama' => 'Ahmad Zulfikar Ramadhan',
            'nis' => 'QLF-2026-089',
            'sekolah' => 'SMAN 1 Teladan',
            'wali' => 'Drs. Hendro Wibowo',
            'telepon' => '0812-3456-7890',
            'jenjang' => 'sma',
            'status' => 'aktif',
        ],
        [
            'inisial' => 'CN',
            'avatar' => 'bg-espresso text-gold',
            'nama' => 'Clarissa Nabila Putri',
            'nis' => 'QLF-2026-092',
            'sekolah' => 'SMP 3 Kota Baru',
            'wali' => 'Ibu Ratna Dewi',
            'telepon' => '0813-8699-1122',
            'jenjang' => 'smp',
            'status' => 'aktif',
        ],
        [
            'inisial' => 'MF',
            'avatar' => 'bg-espresso text-gold',
            'nama' => 'M. Fadhil Ar-Rayyan',
            'nis' => 'QLF-2026-104',
            'sekolah' => 'SMA Al-Azhar 1',
            'wali' => 'Bpk. Bambang Soleh',
            'telepon' => '0856-7788-9900',
            'jenjang' => 'sma',
            'status' => 'aktif',
        ],
        [
            'inisial' => 'DS',
            'avatar' => 'bg-sand text-bark-400',
            'nama' => 'Devina Safitri',
            'nis' => 'QLF-2026-045',
            'sekolah' => 'SMP Negeri 5',
            'wali' => 'Ibu Wahyuni Indah',
            'telepon' => '0877-2233-4455',
            'jenjang' => 'smp',
            'status' => 'aktif',
        ],
        [
            'inisial' => 'RA',
            'avatar' => 'bg-sand text-bark-400',
            'nama' => 'Rah Arya Pradana',
            'nis' => 'QLF-2026-012',
            'sekolah' => 'SMAN 8 Favorit',
            'wali' => 'Bpk. Agus Santoso',
            'telepon' => '0819-3344-5566',
            'jenjang' => 'sma',
            'status' => 'cuti',
        ],
    ];

    $distribusi = [
        ['jenjang' => 'Jenjang SMA', 'jumlah' => '150 Siswa (44%)', 'lebar' => 'w-[44%]'],
        ['jenjang' => 'Jenjang SMP', 'jumlah' => '128 Siswa (37%)', 'lebar' => 'w-[37%]'],
        ['jenjang' => 'Jenjang SD', 'jumlah' => '64 Siswa (19%)', 'lebar' => 'w-[19%]'],
    ];
@endphp

{{-- Kepala halaman --}}
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

    {{-- Tiga angka pokok data siswa --}}
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

        {{-- Daftar siswa --}}
        <section class="overflow-hidden rounded-2xl border border-line bg-white xl:col-span-2">

            {{-- Pencarian & filter --}}
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
                        <option value="aktif">Aktif</option>
                        <option value="cuti">Cuti</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 grid w-9 place-items-center text-bark-400">
                        @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                    </span>
                </div>
            </div>

            {{-- Tabel --}}
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
                        @foreach ($siswa as $row)
                            @php $status = $status_pil[$row['status']]; @endphp
                            <tr data-baris data-jenjang="{{ $row['jenjang'] }}" data-status="{{ $row['status'] }}"
                                data-cari="{{ strtolower($row['nama'] . ' ' . $row['nis'] . ' ' . $row['sekolah'] . ' ' . $row['wali'] . ' ' . $row['telepon']) }}"
                                class="border-t border-line align-top odd:bg-white even:bg-shell">
                                <td class="px-5 py-4">
                                    <div class="flex items-start gap-3">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-[12.5px] font-bold {{ $row['avatar'] }}" aria-hidden="true">
                                            {{ $row['inisial'] }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $row['nama'] }}</p>
                                            <p class="mt-1.5 text-[11.5px] leading-snug text-muted">
                                                {{ $row['nis'] }} <span class="text-bark-300">•</span> {{ $row['sekolah'] }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[13px] font-medium leading-snug text-ink">{{ $row['wali'] }}</p>
                                    <p class="mt-1.5 text-[11.5px] leading-snug text-muted">{{ $row['telepon'] }}</p>
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
                                        <a href="#" aria-label="Edit data {{ $row['nama'] }}"
                                           class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                            @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr id="datasiswa-kosong" class="hidden border-t border-line">
                            <td colspan="4" class="px-5 py-12 text-center">
                                <p class="text-[13.5px] font-bold text-ink">Tidak ada siswa yang cocok</p>
                                <p class="mt-1.5 text-[12.5px] text-muted">Ubah kata kunci pencarian, pilih Semua Jenjang, atau pilih Semua Status.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Kaki tabel --}}
            <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-[12px] leading-snug text-muted">
                    Menampilkan <span id="datasiswa-dari" class="font-bold text-ink">1 – 5</span>
                    dari <span class="font-bold text-ink">342</span> Siswa Terdaftar
                </p>

                <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman daftar siswa">
                    <button type="button" aria-label="Halaman sebelumnya" disabled
                            class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-bark-300">
                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 rotate-180'])
                    </button>
                    <button type="button" aria-current="page"
                            class="grid h-8 w-8 place-items-center rounded-lg bg-espresso text-[12.5px] font-bold text-white">1</button>
                    <button type="button"
                            class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-[12.5px] font-semibold text-espresso transition hover:bg-sand">2</button>
                    <button type="button"
                            class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-[12.5px] font-semibold text-espresso transition hover:bg-sand">3</button>
                    <span class="px-1 text-[12.5px] text-bark-300">…</span>
                    <button type="button"
                            class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-[12.5px] font-semibold text-espresso transition hover:bg-sand">69</button>
                    <button type="button" aria-label="Halaman berikutnya"
                            class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-espresso transition hover:bg-sand">
                        @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4'])
                    </button>
                </nav>
            </footer>
        </section>

        {{-- Distribusi jenjang --}}
        <section class="rounded-2xl border border-line bg-white p-5 self-start">
            <header class="flex items-center gap-2.5">
                <span class="h-2 w-2 shrink-0 rounded-full bg-gold"></span>
                <h2 class="text-[15px] font-bold leading-snug text-ink">Siswa per Jenjang</h2>
            </header>

            <div class="mt-4 space-y-4">
                @foreach ($distribusi as $bar)
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
