@extends('layouts.admin')

@section('title', 'Kelas & Rombel')

@section('content')
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[30px] font-extrabold leading-[1.1] tracking-tight text-ink sm:text-[34px]">Kelas &amp; Rombel</h1>
            <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Daftar rombongan belajar per periode akademik beserta kapasitas dan jumlah siswa aktif.
            </p>
        </div>

        <a href="{{ route('admin.kelas.create') }}"
           class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-espresso px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-bark-800">
            @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4 text-gold'])
            Tambah Kelas
        </a>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-lg border border-forest-500/30 bg-forest-50 px-4 py-3 text-[12.5px] font-semibold text-forest-600">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-ember-500/30 bg-ember-100 px-4 py-3 text-[12.5px] font-semibold text-ember-600">
            {{ $errors->first() }}
        </div>
    @endif
</section>

<div class="px-4 py-5 sm:px-6">
    <section class="overflow-hidden rounded-2xl border border-line bg-white">
        <header class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
            <div class="flex items-center gap-2.5">
                <span class="h-2 w-2 shrink-0 rounded-full bg-bronze"></span>
                <h2 class="text-[15px] font-bold leading-snug text-ink">Daftar Kelas</h2>
                <span class="rounded-full bg-sandgold px-2 py-0.5 text-[10.5px] font-bold text-bronze">{{ $kelas->count() }} Kelas</span>
            </div>
            <a href="{{ route('admin.dataSiswa') }}" class="text-[12.5px] font-semibold text-bronze hover:underline">Ke Data Siswa</a>
        </header>

        <div class="overflow-x-auto border-t border-line">
            <table class="w-full min-w-[760px] text-left">
                <thead>
                    <tr class="bg-shell">
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Kode Kelas</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Jenjang / Tingkat</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Periode</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Siswa</th>
                        <th scope="col" class="px-5 py-3 text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Status</th>
                        <th scope="col" class="px-5 py-3 text-right text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $rombel)
                        <tr class="border-t border-line odd:bg-white even:bg-shell">
                            <td class="px-5 py-4">
                                <p class="text-[13.5px] font-bold text-ink">{{ $rombel->code }}</p>
                                <p class="mt-1 text-[11.5px] text-muted">{{ $rombel->major ?? 'Reguler' }} • {{ $rombel->curriculum ?? 'Kurikulum standar' }}</p>
                            </td>
                            <td class="px-5 py-4 text-[12.5px] font-medium text-bark-500">{{ $rombel->education_level ?? '-' }} {{ $rombel->grade ?? '' }}</td>
                            <td class="px-5 py-4 text-[12.5px] font-medium text-bark-500">{{ $rombel->academicPeriod?->academic_year ?? '-' }} ({{ $rombel->academicPeriod?->semester ?? '-' }})</td>
                            <td class="px-5 py-4 text-[12.5px] font-bold text-ink">{{ $rombel->siswa_count }} / {{ $rombel->capacity ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-lg px-2.5 py-1.5 text-[11px] font-bold leading-tight {{ $rombel->status === 'aktif' ? 'bg-sandgold text-bronze' : 'bg-sand text-muted' }}">
                                    {{ ucfirst($rombel->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.kelas.edit', $rombel) }}" aria-label="Edit kelas {{ $rombel->code }}"
                                       class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-sand hover:text-espresso">
                                        @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                                    </a>
                                    <form method="POST" action="{{ route('admin.kelas.destroy', $rombel) }}"
                                          onsubmit="return confirm('Hapus kelas {{ $rombel->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Hapus kelas {{ $rombel->code }}"
                                                class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-white text-bark-400 transition hover:bg-ember-100 hover:text-ember-600">
                                            @include('layouts.partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-line">
                            <td colspan="6" class="px-5 py-12 text-center">
                                <p class="text-[13.5px] font-bold text-ink">Belum ada kelas</p>
                                <p class="mt-1.5 text-[12.5px] text-muted">Tambahkan rombel pertama lewat tombol Tambah Kelas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
