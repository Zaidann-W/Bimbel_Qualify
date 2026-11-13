@extends('layouts.admin')

@section('title', 'Manajemen Jadwal')

@section('content')
@php
    $inputClass = 'mt-1.5 w-full rounded-lg border border-line bg-white px-3 py-2.5 text-[13px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30';
    $labelClass = 'text-[10.5px] font-bold uppercase tracking-[0.1em] text-muted';
@endphp

<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <nav class="flex items-center gap-1.5 text-[11.5px] font-semibold text-bark-400" aria-label="Breadcrumb">
        <a href="{{ route('admin.absensiJadwal') }}" class="hover:text-bronze">Absensi &amp; Jadwal</a>
        <span aria-hidden="true">/</span>
        <span class="text-ink">Manajemen Jadwal</span>
    </nav>

    <div class="mt-2 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[26px] font-extrabold tracking-tight text-ink">Manajemen Jadwal Sesi</h1>
            <p class="mt-2 max-w-xl text-[13px] leading-relaxed text-muted">
                Susun aturan jadwal mingguan, lalu generate sesi terjadwal per tanggal. Sesi yang sudah dibuat
                masih bisa diubah atau dibatalkan satu per satu.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @if ($tab === 'aturan')
                <a href="{{ route('admin.manajemenJadwal.aturan.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-espresso px-4 py-2.5 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                    @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4 text-gold'])
                    Tambah Aturan
                </a>
            @else
                <form method="GET" action="{{ route('admin.manajemenJadwal') }}" class="flex items-center gap-2.5">
                    <input type="hidden" name="tab" value="sesi">
                    <label class="flex items-center gap-2 rounded-lg border border-line bg-white px-3 py-2.5 text-[12.5px] font-semibold text-ink">
                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4 text-bronze'])
                        <input type="date" name="dari" value="{{ $dari->toDateString() }}"
                               onchange="this.form.submit()"
                               aria-label="Tanggal awal rentang sesi"
                               class="bg-transparent text-[12.5px] font-semibold text-ink focus:outline-none">
                    </label>
                    <label class="flex items-center gap-2 rounded-lg border border-line bg-white px-3 py-2.5 text-[12.5px] font-semibold text-ink">
                        <span class="text-bark-300" aria-hidden="true">s.d.</span>
                        <input type="date" name="sampai" value="{{ $sampai->toDateString() }}"
                               onchange="this.form.submit()"
                               aria-label="Tanggal akhir rentang sesi"
                               class="bg-transparent text-[12.5px] font-semibold text-ink focus:outline-none">
                    </label>
                </form>
            @endif
        </div>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    @if (session('status'))
        <div class="rounded-lg border border-forest-500/30 bg-forest-50 px-4 py-3 text-[12.5px] font-semibold text-forest-600">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-ember-500/30 bg-ember-100 px-4 py-3 text-[12.5px] font-semibold text-ember-600">
            {{ session('error') }}
        </div>
    @endif

    {{-- Hasil simulasi generate (tanpa menyimpan sesi) --}}
    @php($simulasi = session('simulasi'))
    @if ($simulasi)
        <section class="rounded-2xl border border-gold/40 bg-sandgold px-4 py-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[13.5px] font-bold text-bronze">
                        Simulasi: {{ $simulasi['dibuat'] }} sesi akan dibuat dari aturan jadwal.
                    </p>
                    <p class="mt-1 text-[12px] font-medium text-bark-700">
                        Belum ada sesi yang disimpan. Tekan Generate Sesi untuk membetulkan.
                        @if ($simulasi['dilewati'] > 0)
                            {{ $simulasi['dilewati'] }} dilewati karena sesinya sudah ada.
                        @endif
                        @if ($simulasi['bentrok'] > 0)
                            {{ $simulasi['bentrok'] }} dilewati karena bentrok jadwal.
                        @endif
                    </p>
                </div>

                @if ($simulasi['butuh_aturan'])
                    <a href="{{ route('admin.manajemenJadwal.aturan.create') }}"
                       class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-espresso px-3.5 py-2 text-[12px] font-semibold text-white transition hover:bg-bark-800">
                        @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4 text-gold'])
                        Buat Aturan Jadwal
                    </a>
                @endif
            </div>

            @if ($simulasi['detail'] !== [])
                <details class="mt-3">
                    <summary class="cursor-pointer text-[12px] font-bold text-bronze">Lihat rincian ({{ count($simulasi['detail']) }} sesi dilewati)</summary>
                    <ul class="mt-2 space-y-1.5 border-t border-bronze/20 pt-2.5">
                        @foreach ($simulasi['detail'] as $baris)
                            <li class="text-[11.5px] font-medium leading-snug text-bark-700">{{ $baris }}</li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </section>
    @endif

    {{-- Ringkasan hasil generate sesi --}}
    @php($ringkasan = session('generate'))
    @if ($ringkasan)
        <section @class([
            'rounded-2xl border px-4 py-4',
            'border-forest-500/30 bg-forest-50' => $ringkasan['dibuat'] > 0,
            'border-gold/40 bg-sandgold' => $ringkasan['dibuat'] === 0,
        ])>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <p @class([
                        'text-[13.5px] font-bold',
                        'text-forest-700' => $ringkasan['dibuat'] > 0,
                        'text-bronze' => $ringkasan['dibuat'] === 0,
                    ])>
                        {{ $ringkasan['dibuat'] }} sesi terjadwal dibuat dari aturan jadwal.
                    </p>
                    <p class="mt-1 text-[12px] font-medium text-bark-700">
                        @if ($ringkasan['dilewati'] > 0)
                            {{ $ringkasan['dilewati'] }} dilewati karena sesinya sudah ada.
                        @endif
                        @if ($ringkasan['bentrok'] > 0)
                            {{ $ringkasan['bentrok'] }} dilewati karena bentrok jadwal.
                        @endif
                        @if ($ringkasan['dilewati'] === 0 && $ringkasan['bentrok'] === 0 && $ringkasan['dibuat'] > 0)
                            Tidak ada sesi yang dilewati.
                        @endif
                    </p>
                </div>

                @if ($ringkasan['butuh_aturan'])
                    <a href="{{ route('admin.manajemenJadwal.aturan.create') }}"
                       class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-espresso px-3.5 py-2 text-[12px] font-semibold text-white transition hover:bg-bark-800">
                        @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4 text-gold'])
                        Buat Aturan Jadwal
                    </a>
                @endif
            </div>

            @if ($ringkasan['detail'] !== [])
                <details class="mt-3">
                    <summary class="cursor-pointer text-[12px] font-bold text-bronze">Lihat rincian ({{ count($ringkasan['detail']) }} sesi dilewati)</summary>
                    <ul class="mt-2 space-y-1.5 border-t border-bronze/20 pt-2.5">
                        @foreach ($ringkasan['detail'] as $baris)
                            <li class="text-[11.5px] font-medium leading-snug text-bark-700">{{ $baris }}</li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </section>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-ember-500/30 bg-ember-100 px-4 py-3 text-[12.5px] font-semibold text-ember-600">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Pemilih tab --}}
    <nav class="flex gap-1 rounded-xl bg-sand p-1" aria-label="Bagian manajemen jadwal">
        @foreach ([
            'sesi' => 'Sesi Terjadwal',
            'aturan' => 'Aturan Mingguan',
            'ruang' => 'Ruang Belajar',
        ] as $key => $label)
            <a href="{{ route('admin.manajemenJadwal', ['tab' => $key]) }}"
               @class([
                   'flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-[12.5px] transition',
                   'border border-line bg-white font-bold text-ink shadow-[0_1px_2px_rgba(20,20,20,0.06)]' => $tab === $key,
                   'font-semibold text-bark-400 hover:bg-white/60' => $tab !== $key,
               ])>
                {{ $label }}
            </a>
        @endforeach
    </nav>

    @if ($tab === 'aturan')
        {{-- Daftar aturan jadwal mingguan --}}
        <section class="overflow-hidden rounded-2xl border border-line bg-white">
            <header class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                <div class="flex items-center gap-2.5">
                    <span class="h-2 w-2 shrink-0 rounded-full bg-bronze"></span>
                    <h2 class="text-[15px] font-bold leading-snug text-ink">Aturan Jadwal Mingguan</h2>
                    <span class="rounded-full bg-sandgold px-2 py-0.5 text-[10.5px] font-bold text-bronze">{{ $aturan->count() }} Aturan</span>
                </div>
                <p class="text-[12px] font-medium text-muted">Aturan ini jadi template saat sesi digenerate.</p>
            </header>

            <div class="overflow-x-auto border-t border-line">
                <table class="w-full min-w-[860px] text-left">
                    <thead>
                        <tr class="bg-shell">
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Hari</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Jam</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Rombel</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Mata Pelajaran</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Tentor</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Ruang</th>
                            <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aturan as $rule)
                            <tr class="border-t border-line odd:bg-white even:bg-shell">
                                <td class="px-5 py-4 text-[12.5px] font-bold text-ink">{{ ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$rule->weekday - 1] }}</td>
                                <td class="px-5 py-4 text-[12.5px] font-semibold tabular-nums text-bark-500">{{ substr($rule->start_time, 0, 5) }} - {{ substr($rule->end_time, 0, 5) }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-md border border-line bg-white px-2 py-1 text-[11.5px] font-bold text-ink">{{ $rule->classGroup?->code ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-4 text-[12.5px] font-semibold text-bark-500">{{ $rule->subject?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-[12.5px] font-medium text-bark-500">{{ $rule->staff?->full_name_with_title ?? '-' }}</td>
                                <td class="px-5 py-4 text-[12.5px] font-medium text-bark-500">{{ $rule->room?->name ?? 'Tanpa ruang' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.manajemenJadwal.aturan.edit', $rule) }}" aria-label="Edit aturan {{ $rule->classGroup?->code }}"
                                           class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                            @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                                        </a>
                                        <form method="POST" action="{{ route('admin.manajemenJadwal.aturan.destroy', $rule) }}"
                                              onsubmit="return confirm('Hapus aturan jadwal ini beserta sesi yang sudah dibuat?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Hapus aturan {{ $rule->classGroup?->code }}"
                                                    class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-ember-100 hover:text-ember-600">
                                                @include('layouts.partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-line">
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <p class="text-[13.5px] font-bold text-ink">Belum ada aturan jadwal</p>
                                    <p class="mt-1.5 text-[12.5px] text-muted">Tambahkan aturan mingguan terlebih dahulu sebelum generates sesi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @elseif ($tab === 'ruang')
        {{-- Ruang belajar --}}
        <section class="rounded-2xl border border-line bg-white p-5">
            <h2 class="text-[14.5px] font-bold text-ink">Tambah Ruang Belajar</h2>
            <p class="mt-1.5 text-[12.5px] leading-relaxed text-muted">
                Ruang dipakai pada aturan jadwal dan sesi terjadwal, sekaligus untuk memonitor ketersediaan ruang.
            </p>

            <form method="POST" action="{{ route('admin.manajemenJadwal.ruang.store') }}" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @csrf
                <label class="block">
                    <span class="{{ $labelClass }}">Nama Ruang *</span>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: R. Bima" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Lantai</span>
                    <input type="text" name="floor" value="{{ old('floor') }}" maxlength="10" placeholder="Contoh: Lt. 2" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Kapasitas</span>
                    <input type="number" name="capacity" value="{{ old('capacity') }}" min="1" max="1000" placeholder="Contoh: 30" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Status *</span>
                    <select name="status" required class="{{ $inputClass }}">
                        @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', 'aktif') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="sm:col-span-2 lg:col-span-4">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-espresso px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                        @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4 text-gold'])
                        Tambah Ruang
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-line bg-white p-5">
            <header class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="h-2 w-2 shrink-0 rounded-full bg-bronze"></span>
                    <h2 class="text-[15px] font-bold leading-snug text-ink">Daftar Ruang Belajar</h2>
                    <span class="rounded-full bg-sandgold px-2 py-0.5 text-[10.5px] font-bold text-bronze">{{ $ruangan->count() }} Ruang</span>
                </div>
                <p class="text-[12px] font-medium text-muted">Ubah data ruang pada kartu, lalu simpan.</p>
            </header>

            <div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-2">
                @forelse ($ruangan as $ruang)
                    <article class="rounded-xl border border-line bg-shell p-4">
                        <form method="POST" action="{{ route('admin.manajemenJadwal.ruang.update', $ruang) }}">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <label class="block">
                                    <span class="{{ $labelClass }}">Nama Ruang *</span>
                                    <input type="text" name="name" value="{{ $ruang->name }}" required maxlength="100" class="{{ $inputClass }}">
                                </label>
                                <label class="block">
                                    <span class="{{ $labelClass }}">Lantai</span>
                                    <input type="text" name="floor" value="{{ $ruang->floor }}" maxlength="10" class="{{ $inputClass }}">
                                </label>
                                <label class="block">
                                    <span class="{{ $labelClass }}">Kapasitas</span>
                                    <input type="number" name="capacity" value="{{ $ruang->capacity }}" min="1" max="1000" class="{{ $inputClass }}">
                                </label>
                                <label class="block">
                                    <span class="{{ $labelClass }}">Status *</span>
                                    <select name="status" required class="{{ $inputClass }}">
                                        @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)
                                            <option value="{{ $value }}" @selected($ruang->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                                <p class="text-[11.5px] font-medium text-muted">Dipakai {{ $ruang->class_sessions_count }} sesi terjadwal.</p>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-espresso px-3.5 py-2 text-[12px] font-semibold text-white transition hover:bg-bark-800">
                                    @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-4 w-4 text-gold'])
                                    Simpan
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('admin.manajemenJadwal.ruang.destroy', $ruang) }}" class="mt-2 text-right"
                              onsubmit="return confirm('Hapus ruang {{ $ruang->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-white px-3 py-1.5 text-[11.5px] font-semibold text-bark-500 transition hover:bg-ember-100 hover:text-ember-600">
                                @include('layouts.partials.icon', ['name' => 'trash', 'class' => 'h-3.5 w-3.5'])
                                Hapus ruang
                            </button>
                        </form>
                    </article>
                @empty
                    <p class="col-span-full rounded-xl border border-dashed border-line bg-cream px-4 py-8 text-center text-[12.5px] text-muted">
                        Belum ada ruang belajar. Tambahkan ruang lewat formulir di atas.
                    </p>
                @endforelse
            </div>
        </section>
    @else
        {{-- Formulir generate sesi --}}
        <section class="rounded-2xl border border-line bg-white p-5">
            <h2 class="text-[14.5px] font-bold text-ink">Generate Sesi Terjadwal</h2>
            <p class="mt-1.5 text-[12.5px] leading-relaxed text-muted">
                Setiap aturan mingguan akan menghasilkan satu sesi pada tiap tanggal yang cocok. Sesi yang sudah ada
                dan sesi yang bentrok dengan jadwal lain akan dilewati.
            </p>

            <form method="POST" action="{{ route('admin.manajemenJadwal.sesi.generate') }}" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @csrf
                <label class="block">
                    <span class="{{ $labelClass }}">Dari Tanggal *</span>
                    <input type="date" id="generate-dari" name="from_date" value="{{ old('from_date', $dari->toDateString()) }}" required class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Sampai Tanggal *</span>
                    <input type="date" id="generate-sampai" name="to_date" value="{{ old('to_date', $sampai->toDateString()) }}" required class="{{ $inputClass }}">
                </label>
                <label class="block lg:col-span-2">
                    <span class="{{ $labelClass }}">Rombel (kosongkan untuk semua)</span>
                    <select name="class_group_ids[]" multiple size="4" class="{{ $inputClass }}">
                        @foreach ($kelas as $item)
                            <option value="{{ $item->id }}" @selected(in_array($item->id, old('class_group_ids', [])))>{{ $item->code }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="flex flex-wrap items-center gap-2.5 sm:col-span-2 lg:col-span-4">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-espresso px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                        @include('layouts.partials.icon', ['name' => 'refresh', 'class' => 'h-4 w-4 text-gold'])
                        Generate Sesi
                    </button>
                    <button type="submit" name="preview" value="1"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-line bg-white px-5 py-2.5 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                        @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4 text-bronze'])
                        Simulasi Dulu
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:col-span-2 lg:col-span-4">
                    <span class="text-[10.5px] font-bold uppercase tracking-[0.1em] text-muted">Preset</span>
                    <button type="button" data-preset="harian"
                            class="rounded-lg border border-line bg-white px-3 py-1.5 text-[11.5px] font-semibold text-espresso transition hover:bg-sand">
                        Harian ini
                    </button>
                    <button type="button" data-preset="minggu"
                            class="rounded-lg border border-line bg-white px-3 py-1.5 text-[11.5px] font-semibold text-espresso transition hover:bg-sand">
                        Minggu ini
                    </button>
                    <button type="button" data-preset="bulan"
                            class="rounded-lg border border-line bg-white px-3 py-1.5 text-[11.5px] font-semibold text-espresso transition hover:bg-sand">
                        Bulan ini
                    </button>
                    <p class="text-[11.5px] font-medium text-muted">Rentang maksimal 92 hari (1 kuartal) per proses.</p>
                </div>
            </form>
        </section>

        {{-- Daftar sesi terjadwal pada rentang aktif --}}
        <section class="overflow-hidden rounded-2xl border border-line bg-white">
            <header class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                <div class="flex items-center gap-2.5">
                    <span class="h-2 w-2 shrink-0 rounded-full bg-bronze"></span>
                    <h2 class="text-[15px] font-bold leading-snug text-ink">Sesi Terjadwal</h2>
                    <span class="rounded-full bg-sandgold px-2 py-0.5 text-[10.5px] font-bold text-bronze">{{ $sesi->total() }} Sesi</span>
                </div>
                <p class="text-[12px] font-medium text-muted">
                    {{ $dari->locale('id')->translatedFormat('d M Y') }} — {{ $sampai->locale('id')->translatedFormat('d M Y') }}
                </p>
            </header>

            {{-- Filter status sesi --}}
            <div class="flex flex-wrap items-center gap-2 border-t border-line px-5 py-3">
                <span class="shrink-0 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</span>
                @foreach ([
                    null => 'Semua',
                    'terjadwal' => 'Terjadwal',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ] as $nilai => $label)
                    <a href="{{ route('admin.manajemenJadwal', array_filter([
                        'tab' => 'sesi',
                        'dari' => $dari->toDateString(),
                        'sampai' => $sampai->toDateString(),
                        'status' => $nilai,
                    ], fn ($item) => $item !== null)) }}"
                       @class([
                           'rounded-lg px-3 py-1.5 text-[11.5px] font-semibold transition',
                           'bg-espresso text-white' => $status === $nilai,
                           'border border-line bg-white text-espresso hover:bg-sand' => $status !== $nilai,
                       ])>
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="overflow-x-auto border-t border-line">
                <table class="w-full min-w-[960px] text-left">
                    <thead>
                        <tr class="bg-shell">
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Tanggal</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Jam</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Rombel</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Mata Pelajaran</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Tentor</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Ruang</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</th>
                            <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sesi as $item)
                            <tr class="border-t border-line odd:bg-white even:bg-shell">
                                <td class="px-5 py-4">
                                    <span class="text-[12.5px] font-bold text-ink">{{ $item->tanggal->locale('id')->translatedFormat('l, d M Y') }}</span>
                                </td>
                                <td class="px-5 py-4 text-[12.5px] font-bold tabular-nums text-ink">{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-md border border-line bg-white px-2 py-1 text-[11.5px] font-bold text-ink">{{ $item->classGroup?->code ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-4 text-[12.5px] font-semibold text-bark-500">{{ $item->subject?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-[12.5px] font-medium text-bark-500">{{ $item->staff?->full_name_with_title ?? '-' }}</td>
                                <td class="px-5 py-4 text-[12.5px] font-medium text-bark-500">{{ $item->room?->name ?? 'Tanpa ruang' }}</td>
                                <td class="px-5 py-4">
                                    <span @class([
                                        'inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight',
                                        'bg-sandgold text-bronze' => $item->status_efektif === 'terjadwal',
                                        'bg-palegold text-espresso' => $item->status_efektif === 'berlangsung',
                                        'bg-forest-50 text-forest-600' => $item->status_efektif === 'selesai',
                                        'bg-ember-100 text-ember-600' => $item->status_efektif === 'dibatalkan',
                                    ])>{{ ucfirst($item->status_efektif) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.manajemenJadwal.sesi.edit', array_merge(['classSession' => $item], ['dari' => $dari->toDateString(), 'sampai' => $sampai->toDateString()])) }}"
                                           aria-label="Ubah sesi {{ $item->classGroup?->code }}"
                                           class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                            @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                                        </a>
                                        @if ($item->status_efektif !== 'dibatalkan')
                                            <form method="POST" action="{{ route('admin.manajemenJadwal.sesi.batalkan', $item) }}"
                                                  onsubmit="return confirm('Batalkan sesi ini?')">
                                                @csrf
                                                <input type="hidden" name="dari" value="{{ $dari->toDateString() }}">
                                                <input type="hidden" name="sampai" value="{{ $sampai->toDateString() }}">
                                                <button type="submit" aria-label="Batalkan sesi {{ $item->classGroup?->code }}"
                                                        class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-ember-100 hover:text-ember-600">
                                                    @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-4 w-4'])
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-line">
                                <td colspan="8" class="px-5 py-12 text-center">
                                    @if ($jumlahAturan === 0)
                                        <p class="text-[13.5px] font-bold text-ink">Belum ada aturan jadwal mingguan</p>
                                        <p class="mt-1.5 text-[12.5px] text-muted">
                                            Sesi terjadwal dibangun dari aturan mingguan. Buat aturan dulu di tab
                                            <a href="{{ route('admin.manajemenJadwal', ['tab' => 'aturan']) }}" class="font-semibold text-bronze underline underline-offset-2">Aturan Mingguan</a>,
                                            baru tekan Generate Sesi.
                                        </p>
                                    @else
                                        <p class="text-[13.5px] font-bold text-ink">Belum ada sesi pada rentang ini</p>
                                        <p class="mt-1.5 text-[12.5px] text-muted">
                                            Aturan minggu ini tidak jatuh pada rentang
                                            {{ $dari->locale('id')->translatedFormat('d M Y') }} — {{ $sampai->locale('id')->translatedFormat('d M Y') }}.
                                            Ubah rentang tanggal atau tekan Generate Sesi.
                                        </p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sesi->total() > 0)
                <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[12px] leading-snug text-muted">
                        Menampilkan <span class="font-bold text-ink">{{ $sesi->firstItem() ?? 0 }} – {{ $sesi->lastItem() ?? 0 }}</span>
                        dari <span class="font-bold text-ink">{{ $sesi->total() }}</span> sesi terjadwal
                    </p>

                    <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman daftar sesi">
                        @if ($sesi->onFirstPage())
                            <span aria-hidden="true" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-bark-300">
                                @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 rotate-180'])
                            </span>
                        @else
                            <a href="{{ $sesi->previousPageUrl() }}" aria-label="Halaman sebelumnya"
                               class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-espresso transition hover:bg-sand">
                                @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 rotate-180'])
                            </a>
                        @endif

                        <span class="px-2 text-[12.5px] font-semibold text-bark-500">Halaman {{ $sesi->currentPage() }} / {{ $sesi->lastPage() }}</span>

                        @if ($sesi->hasMorePages())
                            <a href="{{ $sesi->nextPageUrl() }}" aria-label="Halaman berikutnya"
                               class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-espresso transition hover:bg-sand">
                                @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4'])
                            </a>
                        @else
                            <span aria-hidden="true" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-white text-bark-300">
                                @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4'])
                            </span>
                        @endif
                    </nav>
                </footer>
            @endif
        </section>
    @endif
</div>

@if ($tab === 'sesi')
    <script>
        (() => {
            const dari = document.getElementById('generate-dari');
            const sampai = document.getElementById('generate-sampai');

            if (! dari || ! sampai) {
                return;
            }

            const format = (tanggal) => {
                const bulan = String(tanggal.getMonth() + 1).padStart(2, '0');
                const hari = String(tanggal.getDate()).padStart(2, '0');

                return `${tanggal.getFullYear()}-${bulan}-${hari}`;
            };

            // Awal minggu berjalan mengikuti kalender Indonesia (Senin-Minggu).
            const awalMinggu = (tanggal) => {
                const hasil = new Date(tanggal);
                const hari = (hasil.getDay() + 6) % 7;

                hasil.setDate(hasil.getDate() - hari);

                return hasil;
            };

            document.querySelectorAll('[data-preset]').forEach((tombol) => {
                tombol.addEventListener('click', () => {
                    const sekarang = new Date();

                    if (tombol.dataset.preset === 'harian') {
                        dari.value = format(sekarang);
                        sampai.value = format(sekarang);

                        return;
                    }

                    if (tombol.dataset.preset === 'minggu') {
                        const awal = awalMinggu(sekarang);
                        const akhir = new Date(awal);
                        akhir.setDate(awal.getDate() + 6);

                        dari.value = format(awal);
                        sampai.value = format(akhir);

                        return;
                    }

                    const akhir = new Date(sekarang.getFullYear(), sekarang.getMonth() + 1, 0);

                    dari.value = format(new Date(sekarang.getFullYear(), sekarang.getMonth(), 1));
                    sampai.value = format(akhir);
                });
            });
        })();
    </script>
@endif
@endsection
