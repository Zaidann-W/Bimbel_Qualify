@extends('layouts.admin')

@section('title', 'QualiFix')

@section('content')
@php
    $status_pil = [
        'menunggu' => [
            'label' => 'Menunggu Tentor',
            'badge' => 'bg-sandgold text-bronze',
            'aksi' => 'Tugaskan Tentor',
            'aksi_kelas' => 'bg-espresso text-white hover:bg-bark-800',
        ],
        'dibahas' => [
            'label' => 'Sedang Dibahas',
            'badge' => 'bg-shell text-muted ring-1 ring-line',
            'aksi' => 'Lihat Detail',
            'aksi_kelas' => 'border border-line bg-white text-espresso hover:bg-sand',
        ],
        'selesai' => [
            'label' => 'Selesai',
            'badge' => 'bg-sand text-muted',
            'aksi' => 'Lihat Solusi',
            'aksi_kelas' => 'border border-line bg-white text-espresso hover:bg-sand',
        ],
    ];

    $per_halaman = 10;

    $gaya_status = collect($status_pil)->map(fn (array $pil) => [
        'label' => $pil['label'],
        'badge' => $pil['badge'],
        'aksi' => $pil['aksi'],
        'aksiKelas' => $pil['aksi_kelas'],
    ]);
@endphp


<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="max-w-2xl">
        <p class="flex flex-wrap items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.16em] text-bark-700">
            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
            Layanan Akademik
            <span class="text-bark-300" aria-hidden="true">•</span>
            Bimbel Qualify
        </p>
        <h1 class="mt-2.5 text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">
            QualiFix
        </h1>
        <p class="mt-3 max-w-xl text-[13.5px] leading-relaxed text-muted">
            Daftar pertanyaan dan sesi konsultasi tugas sekolah siswa.
        </p>
    </div>
</section>

<div class="space-y-5 px-4 py-5 sm:px-6">

    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <article class="rounded-xl border border-line bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Total Masuk</p>
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-bark-500">
                    @include('layouts.partials.icon', ['name' => 'help-circle', 'class' => 'h-4.5 w-4.5'])
                </span>
            </div>
            <p data-kpi="total" class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $kpis['total'] }}</p>
            <p class="mt-2 text-[12.5px] leading-snug text-muted">Pertanyaan</p>
        </article>

        <article class="rounded-xl border border-line bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Menunggu Tentor</p>
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                    @include('layouts.partials.icon', ['name' => 'hourglass', 'class' => 'h-4.5 w-4.5'])
                </span>
            </div>
            <p data-kpi="menunggu" class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-bronze">{{ $kpis['menunggu'] }}</p>
            <p class="mt-2 text-[12.5px] leading-snug text-muted">Perlu Respon</p>
        </article>

        <article class="rounded-xl border border-line bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[10.5px] font-bold uppercase leading-snug tracking-[0.12em] text-muted">Selesai</p>
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-palegold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4.5 w-4.5'])
                </span>
            </div>
            <p data-kpi="selesai" class="mt-3 text-[27px] font-extrabold leading-none tracking-tight tabular-nums text-ink">{{ $kpis['selesai'] }}</p>
            <p class="mt-2 text-[12.5px] leading-snug text-muted">Terselesaikan</p>
        </article>
    </div>

    <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-3">

        
        <div class="space-y-5 xl:col-span-2">

            
            <section class="rounded-2xl border border-line bg-white p-3">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <div class="flex min-w-0 flex-1 flex-wrap gap-1.5" role="group" aria-label="Saring tiket berdasarkan status">
                        @php
                            $tab_pil = [
                                ['semua', 'Semua', $kpis['total']],
                                ['menunggu', 'Menunggu', $kpis['menunggu']],
                                ['selesai', 'Selesai', $kpis['selesai']],
                            ];
                        @endphp
                        @foreach ($tab_pil as [$key, $label, $jumlah])
                            @php
                                $isActive = $filters['status'] === $key;
                                $tabQuery = array_filter(array_merge($filters, ['status' => $key, 'page' => null]));
                                $tabUrl = request()->path().'?'.http_build_query($tabQuery);
                            @endphp
                            <a href="{{ $tabUrl }}"
                                aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                @class([
                                    'inline-flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-[12.5px] font-bold leading-none transition',
                                    'bg-espresso text-white' => $isActive,
                                    'text-muted hover:bg-sand hover:text-espresso' => ! $isActive,
                                ])>
                                {{ $label }}
                                <span data-tab-jumlah="{{ $key }}" @class([
                                    'rounded-md px-1.5 py-0.5 text-[10px] font-bold leading-none',
                                    'bg-gold text-espresso' => $isActive,
                                    'bg-sand text-muted' => ! $isActive,
                                ])>{{ $jumlah }}</span>
                            </a>
                        @endforeach
                    </div>

                    <div class="relative min-w-0 shrink-0 lg:w-64">
                        <form method="GET" action="{{ route('admin.qualifix') }}" class="flex">
                            <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-bark-300">
                                @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                            </span>
                            <input type="hidden" name="status" value="{{ $filters['status'] }}">
                            <input type="search" name="q" aria-label="Cari tiket, siswa, atau mata pelajaran"
                                   placeholder="Cari tiket, siswa, mapel..."
                                   value="{{ $filters['q'] }}"
                                   class="w-full rounded-lg border border-line bg-shell py-2.5 pl-9 pr-3 text-[12.5px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold/30">
                            <button type="submit" class="absolute inset-y-0 right-0 grid w-9 place-items-center pr-3 text-bark-300 hover:text-gold">
                                @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            
            <section id="daftar-tiket" aria-label="Daftar tiket konsultasi QualiFix" class="space-y-3">
                @if ($tiket->isNotEmpty())
                    @foreach ($tiket as $t)
                        @php $pil = $status_pil[$t->view_status ?? 'menunggu']; @endphp
                        <article data-tiket
                                 data-kode="{{ $t->ticket_number }}"
                                 data-status="{{ $t->view_status }}"
                                 class="rounded-xl border border-line bg-white p-4 sm:p-5">
                            <div class="flex h-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-5">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                        <span class="text-[11.5px] font-bold tracking-[0.04em] text-muted">#{{ $t->ticket_number }}</span>
                                        <span class="inline-flex rounded-md bg-sandgold px-2 py-1 text-[11px] font-bold leading-none text-bronze">{{ $t->mapel }}</span>
                                    </div>

                                    <h2 class="mt-2.5 text-[17px] font-extrabold leading-snug tracking-tight text-ink">{{ $t->title }}</h2>

                                    <p data-meta class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-[12.5px] leading-snug text-muted">
                                        @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3.5 w-3.5 text-bark-300'])
                                        <span>{{ $t->student->full_name }}</span>
                                        @if ($t->tentor_nama)
                                            <span class="text-bark-300" aria-hidden="true">•</span>
                                            <span>Tentor: {{ $t->tentor_nama }}</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="flex shrink-0 flex-wrap items-center gap-2 sm:justify-end">
                                    <span data-badge @class(['rounded-md px-2.5 py-1.5 text-[11px] font-bold leading-none', $pil['badge']])>
                                        {{ $pil['label'] }}
                                    </span>
                                    <button type="button" data-tiket-aksi
                                            @class(['inline-flex items-center justify-center rounded-lg px-3.5 py-2.5 text-[12px] font-bold leading-none transition', $pil['aksi_kelas']])>
                                        {{ $pil['aksi'] }}
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @else
                    <div id="tiket-kosong" class="rounded-xl border border-dashed border-line bg-white px-5 py-12 text-center">
                        @include('layouts.partials.icon', ['name' => 'search', 'class' => 'mx-auto h-6 w-6 text-bark-300'])
                        <p class="mt-2.5 text-[13.5px] font-bold text-ink">Tidak ada tiket yang cocok</p>
                        <p class="mt-1.5 text-[12.5px] leading-relaxed text-muted">Ubah kata kunci pencarian atau pilih tab Semua.</p>
                    </div>
                @endif
            </section>

            
            <section class="flex flex-col gap-3 rounded-2xl border border-line bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    @php
                        $dari = $tiket->firstItem() ?? 0;
                        $sampai = $tiket->lastItem() ?? 0;
                    @endphp
                    <p data-hitung aria-live="polite" class="text-[12px] leading-snug text-muted">
                        Menampilkan <span class="font-bold text-ink">{{ $dari }}–{{ $sampai }}</span>
                        dari <span class="font-bold text-ink">{{ $kpis['total'] }}</span> Tiket Konsultasi
                    </p>
                </div>

                <nav class="flex shrink-0 items-center gap-1.5" aria-label="Navigasi halaman daftar tiket">
                    {{ $tiket->links() }}
                </nav>
            </section>
        </div>

        
        <div class="space-y-5">
            <section class="rounded-2xl border border-line bg-white p-5">
                <header class="flex flex-wrap items-center gap-2.5">
                    <h2 class="min-w-0 text-[17px] font-extrabold leading-snug tracking-tight text-ink">Ketersediaan Tentor</h2>
                    <span data-standby class="ml-auto shrink-0 rounded-md bg-sandgold px-2 py-1 text-[10.5px] font-bold leading-none text-bronze">
                        {{ collect($tentor)->where('kondisi', 'tersedia')->count() }} Standby
                    </span>
                </header>
                <p class="mt-1 text-[12px] leading-snug text-muted">Tentor Pendamping Klinik PR</p>

                <div class="mt-4 space-y-3">
                    @foreach ($tentor as $tent)
                        <article data-tentor data-kondisi="{{ $tent['kondisi'] }}"
                                 class="flex items-center gap-3 rounded-xl bg-cream px-4 py-3.5">
                            <span data-tentor-ikon class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sandgold text-bronze">
                                @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-4.5 w-4.5'])
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13.5px] font-bold leading-snug text-ink">{{ $tent['nama'] }}</p>
                                <p class="truncate text-[11.5px] leading-snug text-muted">{{ $tent['bidang'] }}</p>
                            </div>
                            <span data-tentor-status @class([
                                'flex shrink-0 items-center gap-1.5 text-[11.5px] font-bold leading-none',
                                'text-bronze' => $tent['kondisi'] === 'membimbing',
                                'text-muted' => $tent['kondisi'] === 'tersedia',
                            ])>
                                <span data-tentor-titik @class([
                                    'h-1.5 w-1.5 shrink-0 rounded-full',
                                    'bg-gold' => $tent['kondisi'] === 'membimbing',
                                    'bg-bark-300' => $tent['kondisi'] === 'tersedia',
                                ])></span>
                                <span data-tentor-label>{{ $tent['kondisi'] === 'membimbing' ? 'Membimbing' : 'Tersedia' }}</span>
                            </span>
                        </article>
                    @endforeach
                </div>

                <div data-tentor-kosong class="{{ collect($tentor)->where('kondisi', 'tersedia')->isNotEmpty() ? 'hidden' : '' }} rounded-xl border border-dashed border-line bg-cream px-4 py-6 text-center">
                    @include('layouts.partials.icon', ['name' => 'users', 'class' => 'mx-auto h-6 w-6 text-bark-300'])
                    <p class="mt-2.5 text-[13px] font-bold text-ink">Semua pendamping sedang mengajar</p>
                    <p class="mt-1.5 text-[12px] leading-relaxed text-muted">Tugaskan tiket setelah ada tentor yang kembali standby.</p>
                </div>
            </section>

            <section class="rounded-2xl border border-line bg-white px-5 py-4">
                <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-muted">
                    @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'mt-[1px] h-4 w-4 shrink-0 text-gold'])
                    Tiket Klinik PR hanya terbaca oleh siswa pengirim, tentor pendamping, dan pengelola cabang.
                </p>
            </section>
        </div>
    </div>
</div>
@endsection
