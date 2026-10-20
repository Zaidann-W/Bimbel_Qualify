@extends('layouts.tentor')

@section('title', 'Qualifix')

{{-- shortcut: tab filter, pilih antrean, draf, unggah foto, dan kirim jawaban masih mock UI; sambungkan ke backend kalau model Q&A sudah ada --}}
@section('content')
@php
    $stats = [
        ['Perlu Dijawab Segera', '1', 'Pertanyaan', 'Antrean menunggu respons', 'clock', 'text-ember-600', 'bg-ember-50 text-ember-600'],
        ['Terjawab Pekan Ini', '6', 'Soal', '+2 dari pekan lalu', 'check-circle', 'text-emerald-600', 'bg-emerald-50 text-emerald-600'],
    ];

    $tabs = [
        ['Semua', 25, false],
        ['Menunggu Respons', 4, true],
        ['Selesai', 24, false],
    ];

    // [initial, nama, badgeStatus, meta, topik, preview, tags, active]
    $antrean = [
        ['AP', 'Aisyah Putri Azzahra', 'Menunggu', '12 IPA Super Intensif • 4 mnt lalu', 'Matematika Saintek • Deret Geometri',
         'Kak Hendra, mohon bantuannya untuk soal nomor 6 berikut: Diketahui sebuah deret geometri tak hingga memiliki jumlah tak hingga S = 12…',
         ['1 Lampiran Foto', '#Praktikum UTBK'], true],
        ['FM', 'Farhan Madania', 'Selesai', '13 IPA Regular A • 1 jam lalu', 'Fisika: Suku Cadang Mesin & Eksternal Termodynamika', null, null, false],
        ['DB', 'Dimas Bagus Pratama', 'Selesai', 'X2 IPA Geometri • 2 jam lalu', 'Matematika: Transformasi Geometri Refleksi', null, null, false],
        ['NS', 'Nadia Syafitri', 'Selesai', '12 IPA Super Intensif • 3 jam lalu', 'Matematika: Limit Trigonometri Tentu Tak Tentu', null, null, false],
    ];
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Qualifix</h1>
        <p class="mt-1 text-[13px] text-bark-400">Respons dan pandu pemahaman tugas siswa secara cepat, terarah, dan berkualitas.</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach ($stats as [$label, $value, $unit, $sub, $icon, $iconTone, $tone])
            <div class="relative overflow-hidden rounded-2xl border border-bark-100 bg-white p-5">
                <div class="absolute inset-x-0 top-0 h-1 bg-ember-400"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="text-[28px] font-bold leading-none tracking-tight text-bark-800">{{ $value }}</span>
                            <span class="text-[12px] font-medium text-bark-400">{{ $unit }}</span>
                        </div>
                    </div>
                    <div @class(['grid h-9 w-9 place-items-center rounded-xl', $tone])>
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-[18px] w-[18px]'])
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-bark-400">{{ $sub }}</p>
            </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($tabs as [$label, $count, $active])
            <button type="button" aria-pressed="{{ $active ? 'true' : 'false' }}"
                    @class([
                        'flex items-center gap-2 rounded-lg px-3.5 py-2 text-[12.5px] font-semibold transition',
                        'bg-bark-800 text-white' => $active,
                        'border border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => ! $active,
                    ])>
                {{ $label }}
                @if ($active)
                    <span class="grid h-5 w-5 place-items-center rounded-full bg-ember-400 text-[10px] font-bold text-bark-900">{{ $count }}</span>
                @else
                    <span class="text-bark-400">({{ $count }})</span>
                @endif
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[340px_minmax(0,1fr)]">

        {{-- Kiri: antrean pertanyaan --}}
        <section class="self-start overflow-hidden rounded-2xl border border-bark-100 bg-white">
            <header class="flex items-center justify-between border-b border-bark-100 px-5 py-3.5">
                <h2 class="text-[11px] font-bold uppercase tracking-wide text-bark-400">Antrean Pertanyaan</h2>
                <span class="text-[11px] text-bark-400">1 dari 4 tampil</span>
            </header>

            <ul class="divide-y divide-bark-100 p-2">
                @foreach ($antrean as [$initial, $nama, $status, $meta, $topik, $preview, $tags, $active])
                    <li>
                        <div @class([
                            'p-3.5 transition',
                            'rounded-xl border border-ember-400 bg-ember-50/60' => $active,
                            'hover:bg-bark-50/60' => ! $active,
                        ])>
                            <div class="flex items-center gap-2.5">
                                <div @class([
                                    'grid h-9 w-9 shrink-0 place-items-center rounded-full text-[12px] font-bold',
                                    'bg-ember-100 text-ember-700' => $active,
                                    'bg-bark-100 text-bark-600' => ! $active,
                                ])>{{ $initial }}</div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[13px] font-bold text-bark-800">{{ $nama }}</p>
                                    <p class="truncate text-[11px] text-bark-400">{{ $meta }}</p>
                                </div>
                                <span @class([
                                    'shrink-0 rounded-full px-2 py-[3px] text-[10px] font-bold',
                                    'bg-ember-100 text-ember-700' => $status === 'Menunggu',
                                    'bg-emerald-50 text-emerald-600' => $status === 'Selesai',
                                ])>{{ $status }}</span>
                            </div>

                            <p class="mt-2.5 text-[12px] font-semibold text-bark-600">{{ $topik }}</p>

                            @if ($preview)
                                <p class="mt-1.5 line-clamp-2 text-[12px] leading-relaxed text-bark-500">{{ $preview }}</p>
                                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                    @foreach ($tags as $i => $tag)
                                        @if ($i === 0)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-ember-100 px-2 py-0.5 text-[10px] font-bold text-ember-700">
                                                @include('layouts.partials.icon', ['name' => 'image', 'class' => 'h-3 w-3'])
                                                {{ $tag }}
                                            </span>
                                        @else
                                            <span class="text-[10px] font-semibold text-ember-600">{{ $tag }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Kanan: detail + ruang solusi --}}
        <div class="space-y-6">

            {{-- Detail pertanyaan --}}
            <section class="rounded-2xl border border-bark-100 bg-white">
                <header class="border-b border-bark-100 px-5 py-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-ember-100 text-[13px] font-bold text-ember-700">AP</div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-[15px] font-bold text-bark-800">Aisyah Putri Azzahra</h2>
                                    <span class="rounded-full bg-bark-100 px-2 py-[3px] text-[10px] font-bold text-bark-500">Kelas 12</span>
                                </div>
                                <p class="mt-0.5 text-[11.5px] text-bark-400">12 IPA Super Intensif • 32 Siswa • QLF-240918 • 13.42 WIB</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-ember-100 px-2.5 py-1 text-[11px] font-bold text-ember-700">
                            @include('layouts.partials.icon', ['name' => 'flame', 'class' => 'h-3.5 w-3.5'])
                            HOTS UTBK
                        </span>
                    </div>
                </header>

                <div class="space-y-4 px-5 py-4">

                    {{-- Judul soal --}}
                    <div class="flex items-start gap-2.5 rounded-xl border border-ember-200 bg-ember-50 px-4 py-3">
                        @include('layouts.partials.icon', ['name' => 'flame', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
                        <p class="text-[12.5px] font-bold text-bark-700">Soal Tryout Mandiri #08 - Barisan &amp; Deret Geometri Tak Hingga</p>
                    </div>

                    {{-- Kutipan pertanyaan --}}
                    <blockquote class="rounded-r-xl border-l-[3px] border-ember-400 bg-bark-50 px-4 py-3 text-[13px] italic leading-relaxed text-bark-600">
                        Kak Hendra, mohon bantuannya untuk soal nomor 6 berikut: Diketahui sebuah deret geometri tak hingga memiliki jumlah tak hingga S = 12. Jika jumlah suku-suku yang bernomor genap adalah -4, tentukan rasio (r) deret tersebut dan nilai suku pertamanya a₁?
                    </blockquote>

                    <p class="text-[13px] leading-relaxed text-bark-600">
                        Saya selalu mencoba membuat rasio pertama-suku dan menyusun suku ke-2 dari deret ini, tapi hasilnya selalu tidak cocok dengan kunci. Apakah ada tanda yang saya lewatkan? Bagaimana cara paling cepat mengintepretasi permasalahan tanpa hitungan panjang?
                    </p>

                    {{-- Lampiran siswa --}}
                    <div>
                        <h3 class="text-[12px] font-bold text-bark-700">Lampiran Siswa (1 File)</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-3 rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-white text-ember-500 ring-1 ring-bark-100">
                                @include('layouts.partials.icon', ['name' => 'file-image', 'class' => 'h-5 w-5'])
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13px] font-bold text-bark-800">Foto_Soal_Deret_Aisyah.jpg</p>
                                <p class="text-[11px] text-bark-400">1.4 MB • Diantar langsung oleh siswa</p>
                            </div>
                            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                                @include('layouts.partials.icon', ['name' => 'image', 'class' => 'h-3.5 w-3.5'])
                                Buka Foto
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Ruang solusi tentor --}}
            <section class="rounded-2xl border border-bark-100 bg-white">
                <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-bark-800 text-white">
                            @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                        </div>
                        <div>
                            <h2 class="text-[15px] font-bold text-bark-800">Ruang Solusi Tentor</h2>
                            <p class="text-[11px] text-bark-400">Jawaban langsung ke portal belajar Aisyah</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-600">
                        @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                        Draft tersimpan
                    </span>
                </header>

                <div class="space-y-5 px-5 py-4">

                    {{-- Teks jawaban --}}
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <label for="solusi-teks" class="text-[12.5px] font-bold text-bark-700">Teks Penjelasan / Langkah Jawaban</label>
                            <span class="text-[11px] text-bark-400">Bisa salin &amp; tempel kontenmu</span>
                        </div>
                        <textarea id="solusi-teks" rows="8"
                                  class="mt-2 w-full rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] leading-relaxed text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">Halo Aisyah, pertanyaan yang sangat bagus sekali! Kuncinya ada di S_genap, dan ini sengaja dirancang supaya kamu bisa menangkap struktur deret tanpa hitungan panjang.

Berikut langkah eliminasi cepatnya:
1. Ingat sifat deret tak hingga: S = a₁ / (1 - r), dengan |r| < 1.
2. S_genap = (a₁·r) / (1 - r²) = [a₁ / (1 - r)] · r = S · r.
3. Dari dua syarat: S = 12 dan S_genap = -4, maka r = -4 / 12 = -1/3.
4. Karena a₁ = S·(1 - r) = 12·(1 + 1/3) = 16.

Jadi rasio r = -1/3 dan suku pertama a₁ = 16. Coba cocokkan lagi langkah 2 dengan catatanmu ya!</textarea>
                    </div>

                    {{-- Unggah foto jawaban --}}
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-[12.5px] font-bold text-bark-700">Unggah Foto Jawaban / Contoh Pembahasan (Opsional)</span>
                            <span class="text-[11px] text-bark-400">JPG, PNG maks 5MB</span>
                        </div>
                        <div class="mt-2 rounded-xl border-2 border-dashed border-bark-200 px-4 py-6 text-center">
                            @include('layouts.partials.icon', ['name' => 'image', 'class' => 'mx-auto h-6 w-6 text-bark-300'])
                            <p class="mt-2 text-[12px] text-bark-400">Tarik &amp; letakkan foto di sini atau klik dari berkas dan perangkat</p>
                            <button type="button" class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                                @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-3.5 w-3.5 rotate-180'])
                                Pilih Foto
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Aksi --}}
                <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-bark-100 px-5 py-4">
                    <div class="flex items-center gap-2">
                        <button type="button" class="rounded-lg border border-bark-200 bg-white px-3.5 py-2.5 text-[12px] font-bold text-bark-600 transition hover:bg-bark-50">
                            Simpan sebagai Draf
                        </button>
                        <button type="button" class="rounded-lg px-3 py-2.5 text-[12px] font-bold text-bark-400 transition hover:bg-bark-50">
                            Batal
                        </button>
                    </div>
                    <button type="button" class="rounded-lg bg-ember-500 px-4 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-ember-600">
                        Kirim Jawaban ke Siswa
                    </button>
                </footer>
            </section>
        </div>
    </div>
</div>
@endsection
