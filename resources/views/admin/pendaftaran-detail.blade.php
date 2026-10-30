@extends('layouts.admin')

@section('title', 'Detail Calon Siswa')

@section('content')
@php
    $pendaftar = $pendaftar ?? null;

    if (! $pendaftar) {
        abort(404, 'Calon siswa tidak ditemukan.');
    }

    $kode = $pendaftar->registration_number;
    $batch = $pendaftar->batch;
    $createdAt = $pendaftar->created_at;

    $bulanId = [
        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr',
        'Mei' => 'Mei', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Agu' => 'Agu',
        'Sep' => 'Sep', 'Okt' => 'Okt', 'Nov' => 'Nov', 'Des' => 'Des',
    ];

    $tglDaftar = $createdAt
        ? $createdAt->format('d') . ' ' . ($bulanId[$createdAt->format('M')] ?? $createdAt->format('M')) . ' ' . $createdAt->format('Y')
        : '-';

    $statusLabel = match ($pendaftar->status) {
        'menunggu_review' => 'Menunggu Review',
        'perlu_perbaikan' => 'Perlu Perbaikan Berkas',
        'diterima' => 'Diterima Menjadi Siswa',
        'ditolak' => 'Tolak Pendaftaran',
        'batal' => 'Tolak Pendaftaran',
        default => 'Menunggu Review',
    };

    $statusKelas = match ($pendaftar->status) {
        'menunggu_review' => 'bg-sandgold text-bronze',
        'perlu_perbaikan' => 'bg-ember-100 text-ember-700',
        'diterima' => 'bg-sand text-muted',
        'ditolak', 'batal' => 'bg-white text-ember-700 ring-1 ring-ember-200',
        default => 'bg-sandgold text-bronze',
    };

    $statusAksi = [
        [
            'key' => 'review',
            'label' => 'Menunggu Review',
            'ket' => 'Pemeriksaan kelengkapan dokumen masih berlangsung.',
            'aktif' => $pendaftar->status === 'menunggu_review',
        ],
        [
            'key' => 'perbaikan',
            'label' => 'Perlu Perbaikan Berkas',
            'ket' => 'Kirim notifikasi untuk upload ulang scan KK / Rapor.',
            'aktif' => $pendaftar->status === 'perlu_perbaikan',
        ],
        [
            'key' => 'diterima',
            'label' => 'Diterima Menjadi Siswa',
            'ket' => 'Generate ID QLF & kirim akun via WhatsApp.',
            'aktif' => $pendaftar->status === 'diterima',
        ],
        [
            'key' => 'ditolak',
            'label' => 'Tolak Pendaftaran',
            'ket' => 'Kuota cabang penuh / nilai kualifikasi tidak mencukupi.',
            'aktif' => in_array($pendaftar->status, ['ditolak', 'batal']),
        ],
    ];

    $jenjangKurikulum = $pendaftar->education_level
        ? ($pendaftar->education_level . ' - ' . ($pendaftar->curriculum ?: '-'))
        : '-';

    $tglLahir = $pendaftar->birth_date
        ? $pendaftar->birth_date->format('d M Y') . ', ' . $pendaftar->birth_place
        : '-';
@endphp

<section class="bg-white px-4 pb-5 pt-5 sm:px-6">
    <div class="max-w-2xl">
        <p class="flex flex-wrap items-center gap-2.5">
            <span class="rounded-md bg-sandgold px-2.5 py-1.5 text-[10px] font-bold uppercase leading-none tracking-[0.14em] text-bronze">
                Modul 5.3 Admisi
            </span>
            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
            <span class="text-[11.5px] font-semibold text-muted">Bimbel QUALIFY T.A. 2024/2025</span>
        </p>
        <h1 class="mt-3 text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
            Verifikasi &amp; Pengelolaan Pendaftaran Siswa
        </h1>
        <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
            Pemeriksaan formulir pendaftaran masuk, verifikasi berkas &amp; bukti transfer, penetapan status siswa (Menunggu, Perlu Perbaikan, Diterima, Ditolak), dan aktivasi akun.
        </p>
    </div>

    <?php /*  Remah roti & aksi atas */ ?>
    <div class="mt-5 flex flex-wrap items-center justify-between gap-x-4 gap-y-3 border-t border-line pt-4">
        <p class="flex flex-wrap items-center gap-2 text-[12.5px] leading-snug">
            <a href="{{ route('admin.pendaftaran') }}" class="font-medium text-muted transition hover:text-espresso hover:underline">Pendaftaran Siswa</a>
            <span class="text-bark-300">/</span>
            <span class="font-semibold text-ink">Detail Calon Siswa ({{ $kode }})</span>
        </p>

        <div class="flex flex-wrap items-center gap-2.5">
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-[11.5px] font-semibold leading-none text-muted ring-1 ring-line">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                {{ $batch ? $batch->name : '-' }} {{ $batch ? 'Gelombang ' . $batch->wave_number : '' }}
            </span>
            <button type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand">
                @include('layouts.partials.icon', ['name' => 'printer', 'class' => 'h-4 w-4 text-bronze'])
                Cetak Berkas
            </button>
        </div>
    </div>

    <?php /*  Kartu identitas calon siswa */ ?>
    <section class="mt-4 rounded-2xl border border-line border-t-[3px] border-t-gold bg-white p-5">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex min-w-0 items-start gap-4">
                <span class="relative grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-espresso text-[17px] font-extrabold tracking-wide text-white">
                    AP
                    <span class="absolute -bottom-1.5 -right-1.5 grid h-6 w-6 place-items-center rounded-full bg-gold text-espresso ring-2 ring-white">
                        @include('layouts.partials.icon', ['name' => 'camera', 'class' => 'h-3.5 w-3.5'])
                    </span>
                </span>

                <div class="min-w-0">
                    <p class="flex flex-wrap items-center gap-2.5">
                        <span class="text-[21px] font-extrabold leading-tight tracking-tight text-ink">{{ $pendaftar->full_name }}</span>
                        <span class="inline-flex items-center gap-1.5 rounded-lg {{ $statusKelas }} text-[10.5px] font-bold leading-none">
                            <span class="h-1.5 w-1.5 rounded-full bg-bronze"></span>
                            {{ $statusLabel }}
                        </span>
                    </p>
                    <p class="mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[11.5px] leading-snug text-muted">
                        <span class="inline-flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'clipboard-form', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                            {{ $kode }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                            Tgl Daftar: {{ $tglDaftar }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                            {{ $batch ? $batch->name : 'Cabang' }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 flex-wrap gap-2">
                <button type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand">
                    @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-4 w-4 text-ember-600'])
                    Tolak Pendaftaran
                </button>
                <button type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-gold bg-sandgold px-3.5 py-2.5 text-[12.5px] font-semibold text-bronze transition hover:bg-palegold">
                    @include('layouts.partials.icon', ['name' => 'refresh', 'class' => 'h-4 w-4'])
                    Minta Perbaikan Berkas
                </button>
                <button type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-gold px-3.5 py-2.5 text-[12.5px] font-bold text-espresso transition hover:bg-palegold">
                    @include('layouts.partials.icon', ['name' => 'user-check', 'class' => 'h-4 w-4'])
                    Terima &amp; Terbitkan Akun
                </button>
            </div>
        </div>
    </section>
</section>

<div class="px-4 py-5 sm:px-6">
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        <?php /*  Kolom utama */ ?>
        <div class="space-y-5 xl:col-span-2">

            <?php /*  Info calon siswa */ ?>
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-ink">
                            @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-4.5 w-4.5'])
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-[15.5px] font-bold leading-snug text-ink">Informasi Calon Siswa &amp; Kontak</h2>
                            <p class="mt-0.5 text-[11.5px] leading-snug text-muted">Data demografis tervalidasi dari rapor &amp; dokumen asli</p>
                        </div>
                    </div>
                    <span class="shrink-0 rounded-md bg-sand px-2.5 py-1.5 text-[10.5px] font-bold leading-none text-muted">
                        {{ $pendaftar->education_level }} {{ $pendaftar->school_grade }}
                    </span>
                </header>

                <dl class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="min-w-0">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">NISN</dt>
                        <dd class="mt-1.5 break-words text-[13.5px] font-semibold leading-snug text-ink">{{ $pendaftar->nisn ?: '-' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">Asal Sekolah</dt>
                        <dd class="mt-1.5 break-words text-[13.5px] font-semibold leading-snug text-ink">{{ $pendaftar->school_name ?: '-' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">Jenjang &amp; Kurikulum</dt>
                        <dd class="mt-1.5 break-words text-[13.5px] font-semibold leading-snug text-ink">{{ $jenjangKurikulum }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">No. WhatsApp Siswa</dt>
                        <dd class="mt-1.5 break-words text-[13.5px] font-semibold leading-snug text-bronze">
                            <span class="inline-flex items-center gap-1.5">
                                @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-3.5 w-3.5'])
                                {{ $pendaftar->phone_wa ?: '-' }}
                            </span>
                        </dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">Alamat Email</dt>
                        <dd class="mt-1.5 break-words text-[13.5px] font-semibold leading-snug text-ink">{{ $pendaftar->email ?: '-' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">Tempat, Tanggal Lahir</dt>
                        <dd class="mt-1.5 break-words text-[13.5px] font-semibold leading-snug text-ink">{{ $tglLahir }}</dd>
                    </div>

                    <div class="min-w-0 sm:col-span-2 lg:col-span-3">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">Alamat Domisili Siswa</dt>
                        <dd class="mt-1.5 text-[13.5px] font-semibold leading-snug text-ink">
                            {{ $pendaftar->address ?: '-' }}
                        </dd>
                    </div>
                </dl>
            </section>

            <?php /*  Orang tua / wali */ ?>
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex min-w-0 items-start gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'users', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-[15.5px] font-bold leading-snug text-ink">Data Orang Tua / Wali</h2>
                        <p class="mt-0.5 text-[11.5px] leading-snug text-muted">Penanggung jawab keuangan &amp; siswa</p>
                    </div>
                </header>

                <dl class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-3">
                    <div class="min-w-0">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">Nama Ayah / Wali</dt>
                        <dd class="mt-1.5 text-[13.5px] font-semibold leading-snug text-ink">{{ $pendaftar->guardian_name ?: '-' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">Pekerjaan</dt>
                        <dd class="mt-1.5 text-[13.5px] font-semibold leading-snug text-ink">{{ $pendaftar->guardian_occupation ?: '-' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-muted">No. WhatsApp Wali</dt>
                        <dd class="mt-1.5 text-[13.5px] font-semibold leading-snug text-bronze">
                            <span class="inline-flex items-center gap-1.5">
                                @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-3.5 w-3.5'])
                                {{ $pendaftar->guardian_phone ?: '-' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </section>

            <?php /*  Berkas */ ?>
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-ink">
                            @include('layouts.partials.icon', ['name' => 'file-text', 'class' => 'h-4.5 w-4.5'])
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-[15.5px] font-bold leading-snug text-ink">Berkas Persyaratan &amp; Verifikasi Dokumen</h2>
                            <p class="mt-0.5 text-[11.5px] leading-snug text-muted">Tinjau keabsahan dokumen fisik yang diunggah oleh pendaftar</p>
                        </div>
                    </div>
                    <span class="shrink-0 text-[11.5px] font-bold leading-none text-bronze">
                        {{ $pendaftar->documents->count() }} Dari 4 Diunggah
                    </span>
                </header>

                <ul class="mt-5 space-y-3">
                    @forelse ($pendaftar->documents as $berkas)
                        @php
                            $labelMap = [
                                'rapor' => 'Scan Rapor Terakhir',
                                'pas_foto' => 'Pas Foto 3x4 Siswa',
                                'kk_ktp' => 'Scan Kartu Keluarga &amp; KTP Wali',
                                'surat_komitmen' => 'Surat Komitmen Belajar &amp; Tata Tertib',
                            ];
                            $label = $labelMap[$berkas->type] ?? ucfirst($berkas->type);
                            $fileName = $berkas->file ? $berkas->file->original_name : '-';
                        @endphp
                        <li class="flex flex-wrap items-center gap-x-4 gap-y-3 rounded-xl border border-line bg-cream px-4 py-3.5">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg border border-line bg-white text-[10px] font-extrabold tracking-wide text-espresso">
                                @php echo strtoupper(substr($berkas->type, 0, 3)); @endphp
                            </span>

                            <div class="min-w-0 flex-1 basis-56">
                                <p class="flex flex-wrap items-center gap-2">
                                    <span class="text-[13.5px] font-bold leading-snug text-ink">{{ $label }}</span>
                                    <span class="rounded-md px-2 py-1 text-[10px] font-bold leading-none bg-sandgold text-bronze">{{ ucfirst($berkas->status) }}</span>
                                </p>
                                <p class="mt-1.5 truncate text-[11.5px] leading-snug text-muted">{{ $fileName }}</p>
                            </div>

                            <button type="button"
                                    class="ml-auto inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-line bg-white px-3.5 py-2.5 text-[12.5px] font-semibold text-espresso transition hover:bg-sand">
                                @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4'])
                                Lihat
                            </button>
                        </li>
                    @empty
                        <li class="rounded-xl border border-line bg-cream px-4 py-3.5 text-center text-[12.5px] text-muted">
                            Belum ada berkas yang diunggah.
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>

        <?php /*  Kolom kanan */ ?>
        <div class="space-y-5">

            <?php /*  Keuangan */ ?>
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex min-w-0 items-start gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                        @include('layouts.partials.icon', ['name' => 'wallet', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-[15px] font-bold leading-snug text-ink">Keuangan &amp; Tagihan</h2>
                        <p class="mt-0.5 text-[11.5px] leading-snug text-muted">Rincian invoice gelombang 1</p>
                    </div>
                </header>

                <div class="mt-4 space-y-3">
                    <div class="rounded-xl border border-line bg-cream px-3.5 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <p class="min-w-0 text-[11.5px] font-medium leading-snug text-muted">Biaya Formulir &amp; Tes</p>
                            <span class="shrink-0 rounded-md bg-sandgold px-2 py-1 text-[10px] font-bold leading-none text-bronze">Belum Dibayar</span>
                        </div>
                        <p class="mt-1.5 text-[17px] font-extrabold leading-none tracking-tight tabular-nums text-ink">-</p>
                        <p class="mt-2 text-[11px] leading-snug text-muted">Invoice akan terbit setelah verifikasi disetujui.</p>
                    </div>
                </div>
            </section>

            <?php /*  Aksi status */ ?>
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex min-w-0 items-start gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-ink">
                        @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-4.5 w-4.5'])
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-[15px] font-bold leading-snug text-ink">Aksi Cepat Status Pendaftaran</h2>
                        <p class="mt-0.5 text-[11.5px] leading-snug text-muted">Update langsung status calon siswa</p>
                    </div>
                </header>

                <fieldset class="mt-4 space-y-2.5">
                    <legend class="sr-only">Pilih status pendaftaran</legend>
                    @foreach ($statusAksi as $opsi)
                        <label @class([
                            'flex cursor-pointer items-start gap-3 rounded-xl border px-3.5 py-3 transition',
                            'border-gold bg-sandgold' => $opsi['aktif'],
                            'border-line bg-white hover:bg-cream' => ! $opsi['aktif'],
                        ])>
                            <input type="radio" name="status_pendaftaran" value="{{ $opsi['key'] }}" @checked($opsi['aktif'])
                                   class="mt-0.5 h-4 w-4 shrink-0 accent-gold">
                            <span class="min-w-0">
                                <span class="block text-[12.5px] font-bold leading-snug text-ink">{{ $opsi['label'] }}</span>
                                <span class="mt-1 block text-[11.5px] leading-relaxed text-muted">{{ $opsi['ket'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </fieldset>

                <button type="button"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg bg-gold py-3 text-[12.5px] font-bold text-espresso transition hover:bg-palegold">
                    @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-4 w-4'])
                    Terapkan Perubahan Status
                </button>
            </section>
        </div>
    </div>
</div>
@endsection
