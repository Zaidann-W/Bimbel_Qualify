@extends('layouts.admin')

@section('title', 'Edit Materi Ajar')

@section('content')
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <nav class="flex items-center gap-1.5 text-[11.5px] font-semibold text-bark-400" aria-label="Breadcrumb">
        <a href="{{ route('admin.manajemenMateri') }}" class="hover:text-bronze">Manajemen Materi Ajar</a>
        <span aria-hidden="true">/</span>
        <span class="text-ink">Edit</span>
    </nav>
    <h1 class="mt-2 text-[26px] font-extrabold tracking-tight text-ink">Edit Materi Pelajaran</h1>
    <p class="mt-2 max-w-xl text-[13px] leading-relaxed text-muted">
        Perbarui judul, deskripsi, mata pelajaran, kelas sasaran, dan hak akses siswa untuk modul ini.
    </p>
</section>

<div class="px-4 py-5 sm:px-6">
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-ember-500/30 bg-ember-100 px-4 py-3 text-[12.5px] font-semibold text-ember-600">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-2xl border border-line bg-white p-5">
        <header class="flex items-start gap-3">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-ink">
                @include('layouts.partials.icon', ['name' => 'file-text', 'class' => 'h-4.5 w-4.5'])
            </span>
            <div>
                <h2 class="text-[15px] font-bold leading-snug text-ink">Ubah Detail Materi</h2>
                <p class="mt-0.5 text-[12px] leading-snug text-muted">{{ $materi->title }}</p>
            </div>
        </header>

        <x-admin.materi-form :materi="$materi" :subjects="$subjects" :kelas="$kelas">
            <a href="{{ route('admin.manajemenMateri') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-line bg-white px-5 py-3 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-espresso px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-4 w-4 text-gold'])
                Simpan Perubahan
            </button>
        </x-admin.materi-form>
    </section>
</div>
@endsection
