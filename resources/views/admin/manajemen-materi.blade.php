@extends('layouts.admin')

@section('title', 'Manajemen Materi Ajar')

@section('content')

<x-flash-toast />
@php
    $query = request()->only(['q', 'status', 'jenjang']);
@endphp

    {{-- Kepala halaman --}}
    <section class="bg-white px-4 pb-6 pt-5 sm:px-6">
        <div class="max-w-2xl">
            <h1 class="text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">
                Manajemen Materi Ajar
            </h1>
            <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Kelola repositori materi pelajaran, modul ajar, dan distribusi berkas untuk kelas siswa bimbel QUALIFY.
                Semua data ditampilkan langsung dari database.
            </p>
        </div>
    </section>

    <div class="space-y-5 px-4 py-5 sm:px-6">

        {{-- Tiga angka pokok repositori materi (dari DB) --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($kpis as $kpi)
                <article class="rounded-xl border border-line bg-white p-4">
                    <div class="flex items-center gap-3.5">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg {{ $kpi['tile'] }}">
                            @include('layouts.partials.icon', ['name' => $kpi['ikon'], 'class' => 'h-5 w-5'])
                        </span>
                        <div class="min-w-0">
                            <p class="flex items-baseline gap-1.5">
                                <span class="text-[24px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $kpi['nilai'] }}</span>
                                @if ($kpi['satuan'] !== '')
                                    <span class="text-[13px] font-bold leading-none text-ink">{{ $kpi['satuan'] }}</span>
                                @endif
                            </p>
                            <p class="mt-1.5 text-[11.5px] font-medium leading-snug text-muted">{{ $kpi['label'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Unggah materi baru --}}
        <section class="rounded-2xl border border-line bg-white p-5">
            <header class="flex items-start gap-3">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-ink">
                    @include('layouts.partials.icon', ['name' => 'file-text', 'class' => 'h-4.5 w-4.5'])
                </span>
                <div>
                    <h2 class="text-[15px] font-bold leading-snug text-ink">Unggah Materi Pelajaran</h2>
                    <p class="mt-0.5 text-[12px] leading-snug text-muted">Lampirkan berkas dan tentukan kelas sasaran siswa. Data disimpan ke database.</p>
                </div>
            </header>

            <x-admin.materi-form :subjects="$subjects" :kelas="$kelas">
                <button type="submit" name="status" value="draf"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-line bg-white px-5 py-3 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                    @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-4 w-4 text-bronze'])
                    Simpan Draf
                </button>
                <button type="submit" name="status" value="terbit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-espresso px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                    @include('layouts.partials.icon', ['name' => 'upload', 'class' => 'h-4 w-4 text-gold'])
                    Unggah Materi
                </button>
            </x-admin.materi-form>
        </section>

        {{-- Daftar materi terunggah (dari DB) --}}
        <section class="overflow-hidden rounded-2xl border border-line bg-white">

            {{-- Tab jenjang --}}
            <div class="flex flex-wrap items-center gap-2 px-4 pt-4 sm:px-5">
                @foreach ($tabs as $tab)
                    <a href="{{ request()->fullUrlWithQuery(['jenjang' => $tab['key']]) }}"
                       @class([
                           'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition',
                           'bg-espresso text-white' => request('jenjang') === $tab['key'] || (request('jenjang') === null && $tab['key'] === 'semua'),
                           'text-muted hover:bg-sand hover:text-espresso' => ! (request('jenjang') === $tab['key'] || (request('jenjang') === null && $tab['key'] === 'semua')),
                       ])>
                        {{ $tab['label'] }}
                        <span @class([
                            'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none',
                            'bg-gold text-espresso' => request('jenjang') === $tab['key'] || (request('jenjang') === null && $tab['key'] === 'semua'),
                            'bg-sand text-muted' => ! (request('jenjang') === $tab['key'] || (request('jenjang') === null && $tab['key'] === 'semua')),
                        ])>{{ $tab['jumlah'] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Pencarian & filter status --}}
            <form method="GET" action="{{ route('admin.manajemenMateri') }}" class="flex items-center gap-2.5 px-4 py-4 sm:px-5">
                <input type="hidden" name="jenjang" value="{{ request('jenjang') }}">
                <div class="relative min-w-0 flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                        @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                    </span>
                    <input type="search" name="q" aria-label="Cari judul materi, mata pelajaran, atau pengunggah"
                           value="{{ request('q') }}"
                           placeholder="Cari judul materi, topik soal, atau pengunggah..."
                           class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                </div>

                <select name="status" aria-label="Filter status materi"
                        class="rounded-lg border border-line bg-white px-3 py-2.5 text-[12.5px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                    <option value="semua" {{ request('status') === 'semua' || ! request('status') ? 'selected' : '' }}>Semua Status</option>
                    <option value="draf" {{ request('status') === 'draf' ? 'selected' : '' }}>Draf</option>
                    <option value="terbit" {{ request('status') === 'terbit' ? 'selected' : '' }}>Terbit</option>
                </select>

                <button type="submit"
                        class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                    @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                </button>
            </form>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] border-collapse text-left">
                    <thead>
                        <tr class="bg-shell">
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Judul &amp; Berkas</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Mata Pelajaran &amp; Kelas</th>
                            <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status Akses</th>
                            <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="materi-body">
                        @if ($materi->isNotEmpty())
                            @foreach ($materi as $materiItem)
                                <tr data-baris
                                    data-jenjang="{{ implode(',', $materiItem->jenjang_list) }}"
                                    class="border-t border-line align-top odd:bg-white even:bg-shell">
                                    <td class="px-5 py-4">
                                        <div class="flex items-start gap-3">
                                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg {{ $materiItem->file_type === 'PDF' ? 'bg-ember-100 text-ember-700' : ($materiItem->file_type === 'PPTX' ? 'bg-sandgold text-bronze' : 'bg-sand text-bark-400') }}" aria-hidden="true">
                                                @include('layouts.partials.icon', ['name' => $materiItem->file_icon, 'class' => 'h-4.5 w-4.5'])
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-[13.5px] font-bold leading-snug text-ink">{{ $materiItem->title }}</p>
                                                @if ($materiItem->description)
                                                    <p class="mt-0.5 text-[11.5px] leading-snug text-muted">{{ $materiItem->description }}</p>
                                                @endif
                                                <p class="mt-1.5 text-[11.5px] leading-snug text-muted">
                                                    {{ $materiItem->file_type }} <span class="text-bark-300">•</span> {{ $materiItem->formatted_size }}
                                                    <span class="text-bark-300">•</span> Diunggah {{ $materiItem->formatted_date }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="text-[13px] font-bold leading-snug text-ink">{{ optional($materiItem->subject)->name ?? '-' }}</p>
                                        <p class="mt-1.5 text-[11.5px] leading-snug text-muted">{{ $materiItem->class_codes }}</p>
                                        <p class="mt-0.5 text-[11.5px] leading-snug text-muted">
                                            Pengunggah: {{ optional($materiItem->uploader)->name ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $materiItem->access_class }}">
                                            @include('layouts.partials.icon', ['name' => 'eye', 'class' => 'h-3.5 w-3.5'])
                                            {{ $materiItem->access_label }}
                                        </span>
                                        <p class="mt-1.5 text-[11px] leading-snug text-muted">
                                            {{ $materiItem->status === 'terbit' ? 'Aktif' : 'Draf' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.manajemenMateri.edit', $materiItem->id) }}"
                                               aria-label="Edit materi {{ $materiItem->title }}"
                                               class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                                @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-4 w-4'])
                                            </a>
                                            <form action="{{ route('admin.manajemenMateri.destroy', $materiItem->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" aria-label="Hapus materi {{ $materiItem->title }}"
                                                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-ember-100 text-ember-700 transition hover:bg-ember-200"
                                                        onclick="return confirm('Yakin ingin menghapus materi \"{{ $materiItem->title }}\"?')">
                                                    @include('layouts.partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="materi-kosong">
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <p class="text-[13.5px] font-bold text-ink">Tidak ada materi yang cocok</p>
                                    <p class="mt-1.5 text-[12.5px] text-muted">Ubah kata kunci pencarian atau pilih tab Semua.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Kaki tabel + paginasi --}}
            <footer class="flex flex-col gap-3 border-t border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <p class="text-[12px] leading-snug text-muted">
                        Menampilkan <span class="font-bold text-ink">{{ $materi->firstItem() ?? 0 }}</span>
                        dari <span class="font-bold text-ink">{{ $materi->total() }}</span> materi pelajaran terdaftar di sistem
                    </p>

                    @if ($materi->isNotEmpty())
                        <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman daftar materi ajar">
                            @for ($page = 1; $page <= $materi->lastPage(); $page++)
                                <?php $isActive = $page === $materi->currentPage(); ?>
                                <a href="{{ $materi->url($page) }}"
                                   @class([
                                       'grid h-8 w-8 place-items-center rounded-lg text-[12.5px] font-bold transition',
                                       'bg-espresso text-white' => $isActive,
                                       'border border-line bg-white text-espresso hover:bg-sand' => ! $isActive,
                                   ])>{{ $page }}</a>
                            @endfor
                        </nav>
                    @endif
                </div>

            </footer>
        </section>
    </div>
@endsection
