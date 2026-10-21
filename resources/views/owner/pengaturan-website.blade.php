@extends('layouts.owner')

@section('title', 'Pengaturan Website')

{{-- Seluruh data di halaman ini masih mock UI sesuai rancangan docs/pengaturanweb-owner.png;
     sambungkan ke modul CMS (simpan identitas, unggah logo, moderasi testimoni) begitu tersedia --}}
@section('content')
@php
    $identitas = [
        [
            'id' => 'nama-lembaga',
            'label' => 'Nama Lembaga Pendidikan',
            'ikon' => null,
            'nilai' => 'BIMBEL QUALIFY',
            'tipe' => 'text',
            'terkunci' => true,
            'verifikasi' => true,
            'bantuan' => 'Nama paten brand terikat dengan lisensi legalitas pusat.',
            'penuh' => false,
        ],
        [
            'id' => 'slogan',
            'label' => 'Slogan / Tagline Publik',
            'ikon' => null,
            'nilai' => 'Bimbingan Belajar Menuju Kampus Impian & PTN Favorit',
            'tipe' => 'text',
            'terkunci' => false,
            'verifikasi' => false,
            'bantuan' => 'Ditampilkan tepat di bawah logo utama pada hero banner.',
            'penuh' => false,
        ],
        [
            'id' => 'whatsapp',
            'label' => 'Nomor WhatsApp CS Publik',
            'ikon' => 'whatsapp',
            'nilai' => '+62 812-4455-8891',
            'tipe' => 'tel',
            'terkunci' => false,
            'verifikasi' => false,
            'bantuan' => 'Tujuan direct link tombol &ldquo;Konsultasi WhatsApp Sekarang&rdquo;.',
            'penuh' => false,
        ],
        [
            'id' => 'email',
            'label' => 'Email Informasi Publik',
            'ikon' => 'mail',
            'nilai' => 'info@bimbelqualify.com',
            'tipe' => 'email',
            'terkunci' => false,
            'verifikasi' => false,
            'bantuan' => 'Untuk menerima formulir kemitraan dan pertanyaan pendaftaran.',
            'penuh' => false,
        ],
        [
            'id' => 'alamat',
            'label' => 'Alamat Kampus Pusat (Headquarter)',
            'ikon' => 'map-pin',
            'nilai' => 'Jl. Dayang Sumbi No. 14, Coblong, Kota Bandung',
            'tipe' => 'text',
            'terkunci' => false,
            'verifikasi' => false,
            'bantuan' => 'Tercantum pada footer dan peta Google Maps terintegrasi.',
            'penuh' => true,
        ],
    ];

    $testimoni = [
        [
            'inisial' => 'NA',
            'avatar' => 'bg-palegold text-espresso',
            'nama' => 'Nabila Amanda, S.Ked',
            'meta' => 'Alumni 2023 &bull; FK Unpad',
            'rating' => 5,
            'kutipan' => 'Metode bedah soal di QUALIFY sangat terstruktur. Pengajar sangat sabar dan menguasai konsep dasar matematika dan biologi hingga saya bisa tembus FK di pilihan pertama.',
            'diinput' => '12 Juli 2024',
            'kategori' => 'Siswa Sukses',
            'tayang' => true,
            'ekstra' => false,
        ],
        [
            'inisial' => 'BW',
            'avatar' => 'bg-espresso text-white',
            'nama' => 'Bambang Wicaksono',
            'meta' => 'Wali Murid &bull; ITB STEI',
            'rating' => 4,
            'kutipan' => 'Sebagai orang tua, laporan berkala hasil tryout dan konseling peminatan jurusan sangat membantu kami membimbing anak. Transparan dan profesional.',
            'diinput' => '04 Agustus 2024',
            'kategori' => 'Wali Murid',
            'tayang' => true,
            'ekstra' => false,
        ],
        [
            'inisial' => 'RF',
            'avatar' => 'bg-gold text-espresso',
            'nama' => 'Rizky Fauzan',
            'meta' => 'Alumni 2024 &bull; Teknik Sipil UI',
            'rating' => 4,
            'kutipan' => 'Suasana belajar kondusif dengan fasilitas ruang konsultasi VIP. Mentor sangat supportif hingga detik-detik sebelum ujian UTBK dimulai.',
            'diinput' => '29 Agustus 2024',
            'kategori' => 'Siswa Sukses',
            'tayang' => true,
            'ekstra' => false,
        ],
        [
            'inisial' => 'DP',
            'avatar' => 'bg-sand text-bronze',
            'nama' => 'Dewi Puspita',
            'meta' => 'Wali Murid &bull; SMA Negeri 3 Bandung',
            'rating' => 5,
            'kutipan' => 'Administrasinya rapi, jadwal fleksibel, dan pembayaran SPP bisa dicicil tanpa ribet. Sangat membantu keluarga kami.',
            'diinput' => '18 September 2024',
            'kategori' => 'Wali Murid',
            'tayang' => true,
            'ekstra' => true,
        ],
        [
            'inisial' => 'AY',
            'avatar' => 'bg-bark-700 text-white',
            'nama' => 'Aditya Yoga',
            'meta' => 'Alumni 2024 &bull; Universitas Airlangga',
            'rating' => 5,
            'kutipan' => 'Try out SNBT di QUALIFY punya pembahasan paling detail. Skor saya naik 120 poin dalam dua bulan terakhir.',
            'diinput' => '02 Oktober 2024',
            'kategori' => 'Siswa Sukses',
            'tayang' => false,
            'ekstra' => true,
        ],
    ];
@endphp

{{-- Kepala halaman + strip kesehatan domain --}}
<section class="rounded-b-2xl bg-white px-4 pb-5 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <p class="flex flex-wrap items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.14em]">
                <span class="text-bronze">CMS Portal</span>
                <span class="text-bark-300" aria-hidden="true">&bull;</span>
                <span data-status-pratinjau class="font-semibold normal-case tracking-normal text-muted">Live Preview Terhubung</span>
            </p>
            <h1 class="mt-2.5 text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Pengaturan Website Publik
            </h1>
            <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Kelola identitas bimbel, kontak resmi cabang, informasi kelulusan, siswa, dan testimoni yang tampil pada landing page pengunjung.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 lg:shrink-0">
            <a href="{{ url('/') }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-lg border border-line bg-white px-4 py-2.5 text-[13px] font-bold text-espresso transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4 text-bronze'])
                Lihat Website
            </a>
            <button type="submit" form="pengaturan-website" data-simpan
                    class="inline-flex items-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[13px] font-bold text-espresso shadow-[0_1px_2px_rgba(20,20,20,0.08)] transition hover:bg-palegold disabled:cursor-wait disabled:opacity-70">
                @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-4 w-4'])
                <span data-simpan-label>Simpan Semua Perubahan</span>
            </button>
        </div>
    </div>

    <div class="mt-5 flex flex-col gap-3 rounded-xl border border-line bg-shell px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                @include('layouts.partials.icon', ['name' => 'globe', 'class' => 'h-4 w-4'])
            </span>
            <div class="leading-snug">
                <p class="text-[13px] font-bold text-muted">Domain Publik: <span class="font-extrabold text-ink">https://bimbelqualify.com</span></p>
                <p class="mt-1 text-[11.5px] text-muted">Status: Online &amp; Terindeks SEO Google (SSL Aktif)</p>
            </div>
        </div>
        <p class="flex items-center gap-2 whitespace-nowrap text-[11.5px] font-semibold text-bark-500">
            <span class="h-2 w-2 shrink-0 rounded-full bg-gold"></span>
            Sinkronisasi Cloud Aktif
        </p>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    {{-- Bagian 01 — identitas & kontak --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
        <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
            <div class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-espresso text-gold">
                    @include('layouts.partials.icon', ['name' => 'building', 'class' => 'h-5 w-5'])
                </span>
                <div class="min-w-0">
                    <h2 class="text-[16px] font-extrabold leading-snug tracking-tight text-ink">Identitas &amp; Kontak Resmi Bimbel</h2>
                    <p class="mt-1 max-w-md text-[12px] leading-relaxed text-muted">Informasi utama lembaga yang muncul di Header, Kontak, dan Footer landing page.</p>
                </div>
            </div>
            <span class="shrink-0 whitespace-nowrap rounded-md border border-line bg-shell px-2.5 py-1.5 text-[10.5px] font-bold text-bark-500">Bagian 01 / 03</span>
        </div>

        <form id="pengaturan-website" data-pengaturan novalidate class="grid gap-x-6 gap-y-5 border-t border-line px-5 py-5 sm:grid-cols-2">
            @foreach ($identitas as $field)
                <div @class(['sm:col-span-2' => $field['penuh']])>
                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                        <label for="{{ $field['id'] }}" class="flex items-center gap-1.5 text-[12.5px] font-bold leading-snug text-ink">
                            @if ($field['ikon'])
                                @include('layouts.partials.icon', ['name' => $field['ikon'], 'class' => 'h-4 w-4 text-bronze'])
                            @endif
                            {{ $field['label'] }}
                        </label>
                        @if ($field['verifikasi'])
                            <span class="inline-flex items-center gap-1 whitespace-nowrap text-[10.5px] font-bold text-bronze">
                                @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5 text-gold'])
                                Terverifikasi
                            </span>
                        @endif
                    </div>

                    <div class="relative mt-2">
                        <input id="{{ $field['id'] }}" name="{{ $field['id'] }}" type="{{ $field['tipe'] }}"
                               value="{{ $field['nilai'] }}" @readonly($field['terkunci']) data-field
                               @class([
                                   'w-full rounded-lg border border-line px-3.5 py-2.5 pr-10 text-[13.5px] font-medium text-ink transition focus:outline-none focus:ring-2 focus:ring-gold/40',
                                   'bg-sand text-bark-600 cursor-not-allowed' => $field['terkunci'],
                                   'bg-white hover:bg-bark-50 focus:border-gold' => ! $field['terkunci'],
                               ])>
                        @if ($field['terkunci'])
                            @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-bark-400'])
                        @endif
                    </div>

                    <p class="mt-1.5 text-[11px] leading-relaxed text-muted">{!! $field['bantuan'] !!}</p>
                </div>
            @endforeach
        </form>
    </section>

    {{-- Manajemen testimoni --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
        <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
            <div class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-espresso text-gold">
                    @include('layouts.partials.icon', ['name' => 'quote', 'class' => 'h-5 w-5'])
                </span>
                <div class="min-w-0">
                    <h2 class="text-[16px] font-extrabold leading-snug tracking-tight text-ink">Manajemen Testimoni Siswa &amp; Wali Murid</h2>
                    <p class="mt-1 max-w-md text-[12px] leading-relaxed text-muted">Pilih dan aktifkan ulasan terbaik yang akan tayang di landing page publik.</p>
                </div>
            </div>
            <button type="button" data-lebih-lengkap
                    class="inline-flex shrink-0 items-center gap-2 whitespace-nowrap rounded-lg border border-line bg-white px-3 py-2 text-[12px] font-bold text-espresso transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4 text-bronze'])
                <span data-lebih-lengkap-label>Lihat Selengkapnya</span>
            </button>
        </div>

        <div class="space-y-3 border-t border-line px-5 py-5">
            @foreach ($testimoni as $index => $item)
                <article data-testimoni @class(['hidden' => $item['ekstra']]) data-ekstra="{{ $item['ekstra'] ? '1' : '0' }}"
                         class="rounded-xl border border-line bg-shell p-4 transition hover:border-bark-200">
                    <div class="flex flex-col gap-3.5 lg:flex-row lg:items-center lg:gap-5">
                        <div class="flex min-w-0 flex-1 items-start gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full text-[13px] font-extrabold {{ $item['avatar'] }}">{{ $item['inisial'] }}</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $item['nama'] }}</p>
                                    <p class="text-[11.5px] font-semibold leading-snug text-bronze">{!! $item['meta'] !!}</p>
                                    <span class="flex items-center gap-0.5" role="img" aria-label="Rating {{ $item['rating'] }} dari 5">
                                        @for ($bintang = 1; $bintang <= 5; $bintang++)
                                            @include('layouts.partials.icon', [
                                                'name' => 'star',
                                                'class' => $bintang <= $item['rating'] ? 'h-3.5 w-3.5 text-gold' : 'h-3.5 w-3.5 text-bark-200',
                                            ])
                                        @endfor
                                    </span>
                                </div>

                                <blockquote data-kutipan class="mt-2 text-[13px] italic leading-relaxed text-bark-700">
                                    &ldquo;{{ $item['kutipan'] }}&rdquo;
                                </blockquote>

                                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[11px] text-muted">
                                    <span class="inline-flex items-center gap-1.5">
                                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                                        Diinput: {{ $item['diinput'] }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        @include('layouts.partials.icon', ['name' => 'tag', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                                        Kategori: {{ $item['kategori'] }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-wrap items-center gap-3 lg:justify-end">
                            <label class="inline-flex cursor-pointer items-center gap-2.5">
                                <span class="whitespace-nowrap text-[11.5px] font-semibold text-bark-500">Tayang di Web</span>
                                <input type="checkbox" data-tayang @checked($item['tayang']) class="peer sr-only">
                                <span class="relative h-5 w-9 shrink-0 rounded-full bg-bark-200 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform after:content-[''] peer-checked:bg-gold peer-checked:after:translate-x-4 peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true"></span>
                            </label>

                            <button type="button" data-sunting aria-label="Sunting kutipan testimoni {{ $item['nama'] }}" aria-pressed="false"
                                    class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-bark-500 transition hover:border-gold hover:text-espresso">
                                @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                            </button>

                            <button type="button" data-hapus aria-label="Hapus testimoni {{ $item['nama'] }}"
                                    class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-ember-600 transition hover:border-ember-300 hover:bg-ember-50">
                                @include('layouts.partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach

            <div data-kosong hidden class="rounded-xl border border-dashed border-line bg-shell px-5 py-9 text-center">
                <span class="mx-auto grid h-11 w-11 place-items-center rounded-full bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'quote', 'class' => 'h-5 w-5'])
                </span>
                <p class="mt-3 text-[13.5px] font-bold text-ink">Belum ada testimoni di daftar ini</p>
                <p class="mx-auto mt-1 max-w-sm text-[12px] leading-relaxed text-muted">Ulasan siswa dan wali murid yang tayang bisa dipulihkan kembali untuk mulai ditata.</p>
                <button type="button" data-pulihkan
                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[12.5px] font-bold text-espresso transition hover:bg-palegold">
                    @include('layouts.partials.icon', ['name' => 'refresh', 'class' => 'h-4 w-4'])
                    Pulihkan Testimoni Contoh
                </button>
            </div>
        </div>

        <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-line bg-bark-50 px-5 py-3.5">
            <p class="flex items-center gap-2 text-[11.5px] leading-relaxed text-muted">
                @include('layouts.partials.icon', ['name' => 'info', 'class' => 'h-4 w-4 text-gold'])
                Sakelar menentukan testimoni yang tampil di landing page publik.
            </p>
            <p class="text-[11.5px] text-muted" aria-live="polite">
                Menampilkan <span data-hitung-tampil class="font-bold tabular-nums text-ink">3</span> dari
                <span data-hitung-total class="font-bold tabular-nums text-ink">5</span> testimoni
                <span class="text-bark-300">&bull;</span>
                <span data-hitung-tayang class="font-bold tabular-nums text-ink">4</span> aktif tayang
            </p>
        </footer>
    </section>
</div>

{{-- Penegas aksi --}}
<div data-toast hidden role="status" aria-live="polite"
     class="fixed bottom-5 right-5 z-50 flex max-w-[calc(100vw-2.5rem)] items-center gap-3 rounded-xl bg-espresso px-4 py-3.5 shadow-[0_8px_24px_rgba(20,20,20,0.25)]">
    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-gold text-espresso">
        @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4 w-4'])
    </span>
    <p data-toast-teks class="text-[12.5px] font-semibold text-white"></p>
</div>

<script>
    (() => {
        const form = document.querySelector('[data-pengaturan]');
        const tombolSimpan = document.querySelector('[data-simpan]');
        const labelSimpan = document.querySelector('[data-simpan-label]');
        const statusPratinjau = document.querySelector('[data-status-pratinjau]');
        const toast = document.querySelector('[data-toast]');
        const toastTeks = document.querySelector('[data-toast-teks]');
        const daftarTestimoni = document.querySelector('[data-testimoni]')?.parentElement;
        const isiAwalDaftar = daftarTestimoni?.innerHTML;
        const kosong = document.querySelector('[data-kosong]');
        const tombolLebih = document.querySelector('[data-lebih-lengkap]');
        const labelLebih = document.querySelector('[data-lebih-lengkap-label]');
        const hitungTampil = document.querySelector('[data-hitung-tampil]');
        const hitungTotal = document.querySelector('[data-hitung-total]');
        const hitungTayang = document.querySelector('[data-hitung-tayang]');

        const labelAwalSimpan = labelSimpan?.textContent ?? 'Simpan Semua Perubahan';
        let timerToast;

        const beriToast = (pesan) => {
            if (!toast || !toastTeks) {
                return;
            }
            toastTeks.textContent = pesan;
            toast.hidden = false;
            window.clearTimeout(timerToast);
            timerToast = window.setTimeout(() => {
                toast.hidden = true;
            }, 3600);
        };

        const tandaiBerubah = () => {
            if (statusPratinjau) {
                statusPratinjau.textContent = 'Perubahan Belum Disimpan';
                statusPratinjau.classList.replace('text-muted', 'text-bronze');
                statusPratinjau.classList.add('font-bold');
            }
        };

        const tandaiTersimpan = () => {
            if (statusPratinjau) {
                statusPratinjau.textContent = 'Live Preview Terhubung';
                statusPratinjau.classList.replace('text-bronze', 'text-muted');
                statusPratinjau.classList.remove('font-bold');
            }
        };

        const baruiHitungan = () => {
            const kartu = daftarTestimoni ? [...daftarTestimoni.querySelectorAll('[data-testimoni]')] : [];
            const tampil = kartu.filter((kartu) => !kartu.classList.contains('hidden')).length;
            const tayang = kartu.filter((kartu) => kartu.querySelector('[data-tayang]')?.checked).length;

            if (hitungTampil) hitungTampil.textContent = String(tampil);
            if (hitungTotal) hitungTotal.textContent = String(kartu.length);
            if (hitungTayang) hitungTayang.textContent = String(tayang);
            if (kosong) kosong.hidden = kartu.length !== 0;
        };

        const simpan = () => {
            if (!tombolSimpan || tombolSimpan.disabled) {
                return;
            }
            tombolSimpan.disabled = true;
            labelSimpan.textContent = 'Menyimpan...';

            window.setTimeout(() => {
                tombolSimpan.disabled = false;
                labelSimpan.textContent = labelAwalSimpan;
                tandaiTersimpan();
                beriToast('Perubahan tersimpan dan langsung tayang di website publik.');
            }, 650);
        };

        form?.addEventListener('submit', (event) => {
            event.preventDefault();
            simpan();
        });

        form?.addEventListener('input', tandaiBerubah);

        daftarTestimoni?.addEventListener('change', (event) => {
            if (event.target.matches('[data-tayang]')) {
                baruiHitungan();
                tandaiBerubah();
            }
        });        tombolLebih?.addEventListener('click', () => {
            const rangkap = daftarTestimoni ? [...daftarTestimoni.querySelectorAll('[data-testimoni]')] : [];
            const sedangTerbuka = rangkap.some((kartu) => kartu.dataset.ekstra === '1' && !kartu.classList.contains('hidden'));

            rangkap.forEach((kartu) => {
                if (kartu.dataset.ekstra === '1') {
                    kartu.classList.toggle('hidden', sedangTerbuka);
                }
            });

            labelLebih.textContent = sedangTerbuka ? 'Lihat Selengkapnya' : 'Tampilkan Lebih Sedikit';
            baruiHitungan();
        });

        daftarTestimoni?.addEventListener('click', (event) => {
            if (event.target.closest('[data-pulihkan]')) {
                if (!isiAwalDaftar) {
                    return;
                }
                daftarTestimoni.innerHTML = isiAwalDaftar;
                labelLebih.textContent = 'Lihat Selengkapnya';
                baruiHitungan();
                beriToast('Daftar testimoni contoh dipulihkan.');
                return;
            }

            const kartu = event.target.closest('[data-testimoni]');
            if (!kartu) {
                return;
            }

            if (event.target.closest('[data-hapus]')) {
                kartu.remove();
                baruiHitungan();
                tandaiBerubah();
                beriToast('Testimoni dihapus dari daftar tayang.');
                return;
            }

            const tombolSunting = event.target.closest('[data-sunting]');
            if (tombolSunting) {
                const sedangSunting = kartu.dataset.sunting === '1';
                const kutipan = kartu.querySelector('[data-kutipan], [data-kutipan-edit]');

                if (!sedangSunting) {
                    const teks = kutipan.textContent.trim().replace(/^[“”"]+|[”“"]+$/g, '').trim();
                    const area = document.createElement('textarea');
                    area.rows = 3;
                    area.value = teks;
                    area.dataset.kutipanEdit = '';
                    area.setAttribute('aria-label', 'Kutipan testimoni');
                    area.className = 'mt-2 w-full resize-y rounded-lg border border-gold bg-white px-3 py-2 text-[13px] italic leading-relaxed text-bark-700 focus:outline-none focus:ring-2 focus:ring-gold/40';
                    kutipan.replaceWith(area);
                    area.focus();

                    kartu.dataset.sunting = '1';
                    tombolSunting.setAttribute('aria-pressed', 'true');
                    tombolSunting.setAttribute('aria-label', 'Simpan kutipan testimoni');
                    tombolSunting.classList.add('border-gold', 'text-espresso', 'bg-sandgold');
                    return;
                }

                const area = kartu.querySelector('[data-kutipan-edit]');
                const kutipanBaru = document.createElement('blockquote');
                kutipanBaru.dataset.kutipan = '';
                kutipanBaru.className = 'mt-2 text-[13px] italic leading-relaxed text-bark-700';
                const isi = area.value.trim();
                if (isi) {
                    kutipanBaru.append('“' + isi + '”');
                } else {
                    kutipanBaru.textContent = 'Kutipan testimoni belum diisi.';
                }
                area.replaceWith(kutipanBaru);

                kartu.dataset.sunting = '0';
                tombolSunting.setAttribute('aria-pressed', 'false');
                tombolSunting.setAttribute('aria-label', 'Sunting kutipan testimoni');
                tombolSunting.classList.remove('border-gold', 'text-espresso', 'bg-sandgold');
                tandaiBerubah();
                beriToast('Kutipan testimoni diperbarui.');
            }
        });

        baruiHitungan();
    })();
</script>
@endsection
