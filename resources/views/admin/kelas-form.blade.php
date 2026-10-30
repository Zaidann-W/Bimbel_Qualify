@extends('layouts.admin')

@section('title', $classGroup->exists ? 'Edit Kelas' : 'Tambah Kelas')

@section('content')
@php
    $isEdit = $classGroup->exists;
    $action = $isEdit ? route('admin.kelas.update', $classGroup) : route('admin.kelas.store');
    $inputClass = 'mt-1.5 w-full rounded-lg border border-line bg-white px-3 py-2.5 text-[13px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30';
    $labelClass = 'text-[10.5px] font-bold uppercase tracking-[0.1em] text-muted';
@endphp

<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <nav class="flex items-center gap-1.5 text-[11.5px] font-semibold text-bark-400" aria-label="Breadcrumb">
        <a href="{{ route('admin.kelas') }}" class="hover:text-bronze">Kelas &amp; Rombel</a>
        <span aria-hidden="true">/</span>
        <span class="text-ink">{{ $isEdit ? 'Edit' : 'Tambah' }}</span>
    </nav>
    <h1 class="mt-2 text-[26px] font-extrabold tracking-tight text-ink">{{ $isEdit ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h1>
    <p class="mt-2 max-w-xl text-[13px] leading-relaxed text-muted">Atur kode rombel, periode akademik, dan kapasitas kelas.</p>
</section>

<div class="px-4 py-5 sm:px-6">
    <form method="POST" action="{{ $action }}" class="space-y-5">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        @if ($classGroup->exists && $classGroup->enrollments()->where('status', 'aktif')->exists())
            <div class="rounded-lg border border-gold/40 bg-sandgold px-4 py-3 text-[12.5px] font-semibold text-bronze">
                Kelas ini masih memiliki siswa aktif. Perubahan kode dan periode akan memengaruhi data siswa.
            </div>
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

        <section class="rounded-2xl border border-line bg-white p-5">
            <h2 class="text-[14.5px] font-bold text-ink">Informasi Kelas</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">Kode Kelas *</span>
                    <input type="text" name="code" value="{{ old('code', $classGroup->code) }}" required placeholder="Contoh: 12-IPA-1" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Periode Akademik *</span>
                    <select name="academic_period_id" required class="{{ $inputClass }}">
                        <option value="">Pilih periode</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected((int) old('academic_period_id', $classGroup->academic_period_id) === $period->id)>
                                {{ $period->academic_year }} — {{ $period->semester }}{{ $period->is_active ? ' (aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Jenjang</span>
                    <select name="education_level" class="{{ $inputClass }}">
                        <option value="">Tidak ditentukan</option>
                        @foreach (['SD', 'SMP', 'SMA'] as $level)
                            <option value="{{ $level }}" @selected(old('education_level', $classGroup->education_level) === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Tingkat</span>
                    <input type="text" name="grade" value="{{ old('grade', $classGroup->grade) }}" placeholder="Contoh: 12" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Jurusan</span>
                    <input type="text" name="major" value="{{ old('major', $classGroup->major) }}" placeholder="Contoh: IPA" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Kurikulum</span>
                    <input type="text" name="curriculum" value="{{ old('curriculum', $classGroup->curriculum) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Kapasitas</span>
                    <input type="number" name="capacity" min="1" max="1000" value="{{ old('capacity', $classGroup->capacity) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Status *</span>
                    <select name="status" required class="{{ $inputClass }}">
                        @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $classGroup->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-end gap-2.5">
            <a href="{{ route('admin.kelas') }}"
               class="rounded-lg border border-line bg-white px-4 py-2.5 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-espresso px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kelas' }}
            </button>
        </div>
    </form>
</div>
@endsection
