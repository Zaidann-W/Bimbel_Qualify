@extends('layouts.admin')

@section('title', 'Keuangan & Transaksi')

{{-- Halaman masih memakai data mock UI sesuai rancangan docs/keuangan-admin.png;
     sambungkan ke tabel transaksi + tagihan SPP begitu modul keuangan aktif --}}
@section('content')
@php
    $rupiah = fn (int $nilai): string => 'Rp ' . number_format($nilai, 0, ',', '.');

    $status_pil = [
        'lunas' => ['label' => 'Lunas', 'kelas' => 'bg-forest-100 text-forest-700'],
        'terarsip' => ['label' => 'Terarsip', 'kelas' => 'bg-sand text-muted'],
    ];

    $tabs = [
        ['key' => 'semua', 'label' => 'Semua', 'jumlah' => '6'],
        ['key' => 'masuk', 'label' => 'Pemasukan', 'jumlah' => '4'],
        ['key' => 'keluar', 'label' => 'Pengeluaran', 'jumlah' => '2'],
    ];

    $transaksi = [
        [
            'waktu' => '13:45 WIB',
            'keterangan' => 'SPP Bulan Oktober 2024',
            'pihak' => 'Siti Aliyah • 10 SMA IPA',
            'metode' => 'Bank BCA',
            'nominal' => 850000,
            'arah' => 'masuk',
            'status' => 'lunas',
        ],
        [
            'waktu' => '11:20 WIB',
            'keterangan' => 'Pendaftaran & DP Tryout UTBK',
            'pihak' => 'Ananda Rizky Saputra • 12 SMA',
            'metode' => 'Tunai Kasir',
            'nominal' => 1150000,
            'arah' => 'masuk',
            'status' => 'lunas',
        ],
        [
            'waktu' => '10:05 WIB',
            'keterangan' => 'Fotokopi & Binding Drill Soal',
            'pihak' => 'Percetakan Prima Jaya',
            'metode' => 'Kas Kecil',
            'nominal' => -250000,
            'arah' => 'keluar',
            'status' => 'terarsip',
        ],
        [
            'waktu' => '09:12 WIB',
            'keterangan' => 'SPP Oktober & Paket Klinik PR',
            'pihak' => 'Dimas Wahyu • 9 SMP',
            'metode' => 'QRIS',
            'nominal' => 1500000,
            'arah' => 'masuk',
            'status' => 'lunas',
        ],
        [
            'waktu' => '08:30 WIB',
            'keterangan' => 'Cicilan SPP Semester Ganjil',
            'pihak' => 'Nabila Nuraini • 11 SMA IPS',
            'metode' => 'Bank BCA',
            'nominal' => 1350000,
            'arah' => 'masuk',
            'status' => 'lunas',
        ],
        [
            'waktu' => '08:00 WIB',
            'keterangan' => 'Konsumsi & Air Galon Guru',
            'pihak' => 'Toko Berkah Mandiri',
            'metode' => 'Kas Kecil',
            'nominal' => -135000,
            'arah' => 'keluar',
            'status' => 'terarsip',
        ],
    ];

    $tagihan = [
        ['nama' => 'Fajar Ramadhan', 'kelas' => '12 IPS', 'nominal' => 850000, 'tempo' => 'Lewat 4 hari'],
        ['nama' => 'Kirana Naura', 'kelas' => '9 SMP', 'nominal' => 350000, 'tempo' => 'Jatuh tempo hari ini'],
        ['nama' => 'Muhammad Rafi', 'kelas' => '10 IPA', 'nominal' => 800000, 'tempo' => 'Lewat 2 hari'],
    ];
@endphp

{{-- Kepala halaman --}}
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">

    {{-- Status kasir hari ini --}}
    <div class="flex flex-wrap items-center gap-3 rounded-xl border border-line bg-shell px-4 py-3">
        <div class="flex min-w-0 flex-1 items-center gap-2.5">
            <span class="h-2 w-2 shrink-0 rounded-full bg-forest-500"></span>
            <p class="min-w-0 text-[12.5px] leading-snug">
                <span class="font-bold text-ink">Status Kasir: AKTIF</span>
                <span class="mx-1 text-bark-300">•</span>
                <span class="font-medium text-muted">Kasir: Admin Nur &amp; Ust. Farhan</span>
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
                <span class="text-[11.5px] font-semibold text-muted">Bimbel QUALIFY T.A. 2024/2025</span>
            </p>
            <h1 class="mt-3 text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Pencatatan Keuangan Harian
            </h1>
            <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Arus kas bimbel, pembayaran SPP siswa, dan pengeluaran operasional hari ini.
            </p>
        </div>

        <div class="shrink-0 rounded-xl border border-line bg-shell px-4 py-3 xl:text-right">
            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Buku Kas Hari Ini</p>
            <p class="mt-1.5 text-[13.5px] font-bold text-ink">Kamis, 24 Oktober 2024</p>
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
                {{ $rupiah(4850000) }}
            </p>
            <p class="mt-3 text-[11.5px] font-semibold leading-snug text-forest-600">4 transaksi masuk hari ini</p>
        </article>

        <article class="rounded-xl border border-line border-t-[3px] border-t-ember-400 bg-white p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'receipt', 'class' => 'h-4 w-4 text-ember-600'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Pengeluaran Kas</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ember-700">
                {{ $rupiah(385000) }}
            </p>
            <p class="mt-3 text-[11.5px] font-medium leading-snug text-muted">Kas kecil operasional</p>
        </article>

        <article class="rounded-xl border border-espresso border-t-[3px] bg-espresso p-4">
            <div class="flex items-center gap-2">
                @include('layouts.partials.icon', ['name' => 'wallet', 'class' => 'h-4 w-4 text-gold'])
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-bark-200">Saldo Bersih</p>
            </div>
            <p class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-white">
                {{ $rupiah(4465000) }}
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

        {{-- Tab arah kas & pencarian --}}
        <div class="flex flex-col gap-3 px-4 pt-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex flex-wrap items-center gap-2">
                @foreach ($tabs as $tab)
                    <button type="button" data-tab="{{ $tab['key'] }}" data-tab-btn
                            aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                            @class([
                                'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition',
                                'bg-espresso text-white' => $loop->first,
                                'text-muted hover:bg-sand hover:text-espresso' => ! $loop->first,
                            ])>
                        {{ $tab['label'] }}
                        <span @class([
                            'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none',
                            'bg-gold text-espresso' => $loop->first,
                            'bg-sand text-muted' => ! $loop->first,
                        ])>{{ $tab['jumlah'] }}</span>
                    </button>
                @endforeach
            </div>

            <div class="relative w-full sm:w-64">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                    @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                </span>
                <input type="search" id="keuangan-cari" aria-label="Cari transaksi"
                       placeholder="Cari transaksi..."
                       class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
            </div>
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
                    @foreach ($transaksi as $row)
                        @php $status = $status_pil[$row['status']]; @endphp
                        <tr data-baris data-arah="{{ $row['arah'] }}"
                            data-cari="{{ strtolower($row['keterangan'] . ' ' . $row['pihak'] . ' ' . $row['metode']) }}"
                            class="border-t border-line align-top odd:bg-white even:bg-shell">
                            <td class="px-5 py-4">
                                <p class="text-[13px] font-bold leading-snug tabular-nums text-ink">{{ $row['waktu'] }}</p>
                                <p class="mt-1 text-[11.5px] leading-snug text-muted">Hari ini</p>
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
                                    {{ $row['arah'] === 'masuk' ? '+' : '-' }} {{ $rupiah(abs($row['nominal'])) }}
                                </p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $status['kelas'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                    <tr id="keuangan-kosong" class="hidden border-t border-line">
                        <td colspan="5" class="px-5 py-12 text-center">
                            <p class="text-[13.5px] font-bold text-ink">Tidak ada transaksi yang cocok</p>
                            <p class="mt-1.5 text-[12.5px] text-muted">Ubah kata kunci pencarian atau pilih tab Semua untuk melihat seluruh arus kas hari ini.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Kaki tabel --}}
        <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-[12px] leading-snug text-muted">
                Menampilkan <span id="keuangan-dari" class="font-bold text-ink">6</span> transaksi hari ini
            </p>

            <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman buku transaksi">
                <button type="button" disabled
                        class="rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-semibold text-bark-300">
                    Sebelumnya
                </button>
                <button type="button" aria-current="page"
                        class="grid h-8 w-8 place-items-center rounded-lg bg-espresso text-[12.5px] font-bold text-white">1</button>
                <button type="button"
                        class="rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-semibold text-espresso transition hover:bg-sand">
                    Selanjutnya
                </button>
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
            <span class="inline-flex rounded-lg bg-forest-100 px-2.5 py-1.5 text-[11px] font-bold leading-tight text-forest-700">
                Status: Klir / Selisih Rp 0
            </span>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div class="rounded-xl bg-shell p-4">
                <p class="text-[12px] font-semibold leading-snug text-muted">Laci Kas (Fisik)</p>
                <p class="mt-2 text-[22px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $rupiah(1265000) }}</p>
                <p class="mt-2.5 text-[11px] leading-snug text-muted">Shift aktif</p>
            </div>
            <div class="rounded-xl bg-shell p-4">
                <p class="text-[12px] font-semibold leading-snug text-muted">Bank BCA (Mutasi)</p>
                <p class="mt-2 text-[22px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $rupiah(3700000) }}</p>
                <p class="mt-2.5 text-[11px] leading-snug text-muted">Rek. 822-0918-201</p>
            </div>
        </div>

        <p class="mt-4 flex items-start gap-2 border-t border-line pt-4 text-[12px] leading-relaxed text-muted">
            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'mt-0.5 h-3.5 w-3.5 shrink-0 text-forest-600'])
            Semua pencatatan fisik dan perbankan telah sesuai tanpa selisih.
        </p>
    </section>

    {{-- Tagihan SPP Belum Lunas --}}
    <section class="rounded-2xl border border-line bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-[15px] font-bold leading-snug text-ink">Tagihan SPP Belum Lunas</h2>
            <span class="inline-flex rounded-lg bg-ember-100 px-2.5 py-1.5 text-[11px] font-bold leading-tight text-ember-700">
                3 Jatuh Tempo
            </span>
        </div>

        <ul class="mt-2">
            @foreach ($tagihan as $tunggak)
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
            @endforeach
        </ul>

        <div class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
            <p class="text-[12px] leading-snug text-muted">
                Total tertunggak: <span class="font-bold text-ember-700">{{ $rupiah(2000000) }}</span>
            </p>
            <button type="button" data-pengingat
                    class="inline-flex items-center gap-2 rounded-lg bg-espresso px-4 py-2.5 text-[12.5px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'megaphone', 'class' => 'h-4 w-4 text-gold'])
                Broadcast Semua
            </button>
        </div>
    </section>
</div>

<script>
    (() => {
        const baris = Array.from(document.querySelectorAll('[data-baris]'));
        const tombolTab = Array.from(document.querySelectorAll('[data-tab-btn]'));
        const inputCari = document.getElementById('keuangan-cari');
        const barisKosong = document.getElementById('keuangan-kosong');
        const labelDari = document.getElementById('keuangan-dari');

        let tabAktif = 'semua';

        const terapkan = () => {
            const kata = inputCari.value.trim().toLowerCase();
            let tampil = 0;

            baris.forEach((barisData) => {
                const cocokTab = tabAktif === 'semua' || barisData.dataset.arah === tabAktif;
                const cocokKata = !kata || barisData.dataset.cari.includes(kata);
                const terlihat = cocokTab && cocokKata;

                barisData.classList.toggle('hidden', !terlihat);
                if (terlihat) {
                    tampil += 1;
                }
            });

            barisKosong.classList.toggle('hidden', tampil > 0);
            labelDari.textContent = String(tampil);
        };

        tombolTab.forEach((tombol) => {
            tombol.addEventListener('click', () => {
                tabAktif = tombol.dataset.tab;

                tombolTab.forEach((lain) => {
                    const aktif = lain === tombol;
                    const lencana = lain.querySelector('span');

                    lain.setAttribute('aria-pressed', String(aktif));
                    lain.className = aktif
                        ? 'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition bg-espresso text-white'
                        : 'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition text-muted hover:bg-sand hover:text-espresso';
                    lencana.className = aktif
                        ? 'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none bg-gold text-espresso'
                        : 'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none bg-sand text-muted';
                });

                terapkan();
            });
        });

        inputCari.addEventListener('input', terapkan);

        // Umpan balik instan saat pengingat tagihan dikirim
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
