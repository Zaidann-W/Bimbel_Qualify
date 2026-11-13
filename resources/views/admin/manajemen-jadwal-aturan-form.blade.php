@extends('layouts.admin')

@section('title', $scheduleRule->exists ? 'Edit Aturan Jadwal' : 'Tambah Aturan Jadwal')

@section('content')
@php
    $isEdit = $scheduleRule->exists;
    $action = $isEdit ? route('admin.manajemenJadwal.aturan.update', $scheduleRule) : route('admin.manajemenJadwal.aturan.store');
    $inputClass = 'mt-1.5 w-full rounded-lg border border-line bg-white px-3 py-2.5 text-[13px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30';
    $labelClass = 'text-[10.5px] font-bold uppercase tracking-[0.1em] text-muted';
    $nilai = fn (string $key, $default = null) => old($key, $default);
@endphp

<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <nav class="flex items-center gap-1.5 text-[11.5px] font-semibold text-bark-400" aria-label="Breadcrumb">
        <a href="{{ route('admin.absensiJadwal') }}" class="hover:text-bronze">Absensi &amp; Jadwal</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.manajemenJadwal', ['tab' => 'aturan']) }}" class="hover:text-bronze">Manajemen Jadwal</a>
        <span aria-hidden="true">/</span>
        <span class="text-ink">{{ $isEdit ? 'Edit Aturan' : 'Tambah Aturan' }}</span>
    </nav>
    <h1 class="mt-2 text-[26px] font-extrabold tracking-tight text-ink">{{ $isEdit ? 'Edit Aturan Jadwal' : 'Tambah Aturan Jadwal Mingguan' }}</h1>
    <p class="mt-2 max-w-xl text-[13px] leading-relaxed text-muted">
        Aturan ini menjadi template jadwal sesi. Sesi nyata dibuat dari aturan lewat proses generate.
    </p>
</section>

<div class="px-4 py-5 sm:px-6">
    <form method="POST" action="{{ $action }}" class="space-y-5">
        @csrf
        @if ($isEdit)
            @method('PUT')
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
            <h2 class="text-[14.5px] font-bold text-ink">Detail Aturan</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">Rombel *</span>
                    <select name="class_group_id" required class="{{ $inputClass }}">
                        <option value="">Pilih rombel</option>
                        @foreach ($kelas as $item)
                            <option value="{{ $item->id }}" @selected((int) $nilai('class_group_id', $scheduleRule->class_group_id) === $item->id)>{{ $item->code }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Mata Pelajaran *</span>
                    <select name="subject_id" required class="{{ $inputClass }}">
                        <option value="">Pilih mata pelajaran</option>
                        @foreach ($mapel as $item)
                            <option value="{{ $item->id }}" @selected((int) $nilai('subject_id', $scheduleRule->subject_id) === $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Tentor *</span>
                    <select name="staff_id" required class="{{ $inputClass }}">
                        <option value="">Pilih tentor</option>
                        @foreach ($tentor as $item)
                            <option value="{{ $item->id }}" @selected((int) $nilai('staff_id', $scheduleRule->staff_id) === $item->id)>{{ $item->full_name_with_title }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Ruang</span>
                    <select name="room_id" class="{{ $inputClass }}">
                        <option value="">Tanpa ruang</option>
                        @foreach ($ruangan as $item)
                            <option value="{{ $item->id }}" @selected((int) $nilai('room_id', $scheduleRule->room_id) === $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Sesi Belajar</span>
                    <select name="study_shift_id" class="{{ $inputClass }}">
                        <option value="">Tidak ditentukan</option>
                        @foreach ($shift as $item)
                            <option value="{{ $item->id }}" @selected((int) $nilai('study_shift_id', $scheduleRule->study_shift_id) === $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Hari *</span>
                    <select name="weekday" required class="{{ $inputClass }}">
                        @foreach ([1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'] as $value => $label)
                            <option value="{{ $value }}" @selected((int) $nilai('weekday', $scheduleRule->weekday ?? 1) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Jam Mulai *</span>
                    <input type="time" name="start_time" value="{{ $nilai('start_time', $scheduleRule->start_time ? substr($scheduleRule->start_time, 0, 5) : '15:30') }}" required class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Jam Selesai *</span>
                    <input type="time" name="end_time" value="{{ $nilai('end_time', $scheduleRule->end_time ? substr($scheduleRule->end_time, 0, 5) : '17:30') }}" required class="{{ $inputClass }}">
                </label>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-end gap-2.5">
            <a href="{{ route('admin.manajemenJadwal', ['tab' => 'aturan']) }}"
               class="rounded-lg border border-line bg-white px-4 py-2.5 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-espresso px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Aturan' }}
            </button>
        </div>
    </form>
</div>
@endsection
