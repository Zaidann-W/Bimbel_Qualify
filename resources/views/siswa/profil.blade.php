@extends('layouts.siswa')

@section('title', 'Profil Siswa')

@section('content')
<div class="space-y-5">

    {{-- Breadcrumb --}}
    <ol class="flex flex-wrap items-center gap-1.5 text-[12px] text-bark-400">
        <li><a href="{{ route('siswa.dashboard') }}" class="font-medium hover:text-ember-600">Portal Siswa</a></li>
        <li aria-hidden="true">›</li>
        <li class="font-semibold text-bark-700">Profil Siswa</li>
    </ol>

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[26px] font-bold tracking-tight text-bark-800">Profil Siswa</h1>
            <p class="mt-1.5 text-[13px] leading-relaxed text-bark-400">Kelola data pribadi, kontak orang tua/wali, serta informasi akun belajar Anda.</p>
        </div>
        <button type="button" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-gold px-4 py-2.5 text-[12.5px] font-bold text-espresso shadow-sm transition hover:brightness-95">
            @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-4 w-4'])
            Simpan Perubahan
        </button>
    </div>

    {{-- Kartu profil --}}
    <section class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-bark-100 bg-white px-5 py-4">
        <div class="flex items-center gap-4">
            <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-ember-100 text-[18px] font-bold text-ember-700">
                {{ Str::upper(Str::of($student->nickname ?? $student->full_name ?? 'S')->explode(' ')->take(2)->map(fn($w) => Str::substr($w, 0, 1))->implode('')) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-[17px] font-bold text-bark-800">{{ $student->full_name }}</h2>
                    <span class="rounded-md bg-gold px-2 py-[3px] text-[10px] font-bold text-espresso">{{ $student->status }}</span>
                </div>
                <p class="mt-1 text-[12px] text-bark-400">NISN: <span class="font-semibold text-bark-600">{{ $student->nisn ?? '-' }}</span> • {{ $classGroup ? 'Kelas '.$classGroup->grade.' '.$classGroup->education_level.' '.$classGroup->major : 'Belum ada kelas aktif' }}</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full border border-bark-200 bg-white px-3 py-1.5 text-[11.5px] font-bold text-bark-500">
            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4 w-4 text-emerald-500'])
            Data Terverifikasi Sekolah
        </span>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">

        {{-- Kiri: data diri --}}
        <section class="self-start rounded-2xl border border-bark-100 bg-white px-5 py-5">
            <header class="flex items-center gap-3">
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-bark-800 text-white">
                    @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-[18px] w-[18px]'])
                </div>
                <div>
                    <h2 class="text-[15px] font-bold text-bark-800">Data Diri Siswa</h2>
                    <p class="text-[11.5px] text-bark-400">Informasi lengkap identitas siswa</p>
                </div>
            </header>

            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($dataDiri as $item)
                    <div @class(['sm:col-span-2' => in_array($item['label'], ['Nama Lengkap', 'Alamat Domisili'])] )>
                        <label class="text-[11.5px] font-semibold text-bark-500" for="profil-{{ $item['slug'] }}">{{ $item['label'] }}</label>
                        <input id="profil-{{ $item['slug'] }}" type="text" value="{{ $item['value'] }}"
                               class="mt-1.5 w-full rounded-lg border border-bark-100 bg-bark-50 px-3.5 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-ember-100">
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Kanan: kontak wali + akun --}}
        <div class="space-y-6">

            <section class="rounded-2xl border border-bark-100 bg-white px-5 py-5">
                <header class="flex items-center gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-bark-800 text-white">
                        @include('layouts.partials.icon', ['name' => 'users', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Kontak Orang Tua / Wali</h2>
                        <p class="text-[11.5px] text-bark-400">Untuk laporan berkala siswa</p>
                    </div>
                </header>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="profil-wali-nama" class="text-[11.5px] font-semibold text-bark-500">Nama Orang Tua / Wali</label>
                        <input id="profil-wali-nama" type="text" value="{{ $guardian->name ?? '-' }}"
                               class="mt-1.5 w-full rounded-lg border border-bark-100 bg-bark-50 px-3.5 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-ember-100">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="profil-hubungan" class="text-[11.5px] font-semibold text-bark-500">Hubungan</label>
                            <input id="profil-hubungan" type="text" value="{{ $guardian->pivot->relationship ?? '-' }}"
                                   class="mt-1.5 w-full rounded-lg border border-bark-100 bg-bark-50 px-3.5 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-ember-100">
                        </div>
                        <div>
                            <label for="profil-wali-wa" class="text-[11.5px] font-semibold text-bark-500">No. WhatsApp Orang Tua</label>
                            <input id="profil-wali-wa" type="text" value="{{ $guardian->phone_wa ?? '-' }}"
                                   class="mt-1.5 w-full rounded-lg border border-bark-100 bg-bark-50 px-3.5 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-ember-100">
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-bark-100 bg-white px-5 py-5">
                <header class="flex items-center gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-bark-800 text-white">
                        @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Akun Pembelajaran</h2>
                        <p class="text-[11.5px] text-bark-400">Akses portal & kata sandi</p>
                    </div>
                </header>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="profil-email" class="text-[11.5px] font-semibold text-bark-500">Email Belajar</label>
                        <div class="relative mt-1.5">
                            <input id="profil-email" type="email" value="{{ auth()->user()->email }}" readonly
                                   class="w-full rounded-lg border border-bark-100 bg-bark-50 px-3.5 py-2.5 pr-9 text-[13px] text-bark-700">
                            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-500'])
                        </div>
                    </div>

                    <div>
                        <label class="text-[11.5px] font-semibold text-bark-500">Kata Sandi</label>
                        <div class="mt-1.5 flex items-center gap-3 rounded-lg border border-bark-100 bg-bark-50 px-3.5 py-2">
                            <p class="flex-1 text-[13px] tracking-[0.15em] text-bark-500">••••••••••••</p>
                            <button type="button" class="shrink-0 rounded-lg border border-bark-200 bg-white px-3 py-1.5 text-[11.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                                Ubah Sandi
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection