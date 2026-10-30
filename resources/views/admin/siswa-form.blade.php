@extends('layouts.admin')

@section('title', $student->exists ? 'Edit Data Siswa' : 'Tambah Siswa')

@section('content')
@php
    $isEdit = $student->exists;
    $action = $isEdit ? route('admin.dataSiswa.update', $student) : route('admin.dataSiswa.store');
    $tingkat = ['SD', 'SMP', 'SMA'];
    $statusPil = ['aktif' => 'Aktif', 'cuti' => 'Cuti', 'nonaktif' => 'Nonaktif', 'lulus' => 'Lulus'];
    $inputClass = 'mt-1.5 w-full rounded-lg border border-line bg-white px-3 py-2.5 text-[13px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30';
    $labelClass = 'text-[10.5px] font-bold uppercase tracking-[0.1em] text-muted';
@endphp

<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <nav class="flex items-center gap-1.5 text-[11.5px] font-semibold text-bark-400" aria-label="Breadcrumb">
        <a href="{{ route('admin.dataSiswa') }}" class="hover:text-bronze">Data Siswa</a>
        <span aria-hidden="true">/</span>
        <span class="text-ink">{{ $isEdit ? 'Edit' : 'Tambah' }}</span>
    </nav>
    <h1 class="mt-2 text-[26px] font-extrabold tracking-tight text-ink">{{ $isEdit ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h1>
    <p class="mt-2 max-w-xl text-[13px] leading-relaxed text-muted">Lengkapi identitas siswa, kontak wali, dan penempatan kelas.</p>
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
            <h2 class="text-[14.5px] font-bold text-ink">Identitas Siswa</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">Nama Lengkap *</span>
                    <input type="text" name="full_name" value="{{ old('full_name', $student->full_name) }}" required class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Nama Panggilan</span>
                    <input type="text" name="nickname" value="{{ old('nickname', $student->nickname) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">NIS</span>
                    <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">NISN</span>
                    <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Tempat Lahir</span>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Tanggal Lahir</span>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Email</span>
                    <input type="email" name="email" value="{{ old('email', $student->email) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Nomor WhatsApp</span>
                    <input type="text" name="phone_wa" value="{{ old('phone_wa', $student->phone_wa) }}" class="{{ $inputClass }}">
                </label>
                <label class="block sm:col-span-2">
                    <span class="{{ $labelClass }}">Alamat</span>
                    <textarea name="address" rows="2" class="{{ $inputClass }}">{{ old('address', $student->address) }}</textarea>
                </label>
            </div>
        </section>

        <section class="rounded-2xl border border-line bg-white p-5">
            <h2 class="text-[14.5px] font-bold text-ink">Sekolah &amp; Kelas</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">Jenjang *</span>
                    <select name="education_level" required class="{{ $inputClass }}">
                        @foreach ($tingkat as $level)
                            <option value="{{ $level }}" @selected(old('education_level', $student->education_level) === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Tingkat Kelas</span>
                    <input type="text" name="school_grade" value="{{ old('school_grade', $student->school_grade) }}" placeholder="Contoh: 12" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Nama Sekolah</span>
                    <input type="text" name="school_name" value="{{ old('school_name', $student->school_name) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Jurusan</span>
                    <input type="text" name="major" value="{{ old('major', $student->major) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Kurikulum</span>
                    <input type="text" name="curriculum" value="{{ old('curriculum', $student->curriculum) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Status *</span>
                    <select name="status" required class="{{ $inputClass }}">
                        @foreach ($statusPil as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $student->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block sm:col-span-2">
                    <span class="{{ $labelClass }}">Kelas Bimbel</span>
                    <select name="class_group_id" class="{{ $inputClass }}">
                        <option value="">Belum ditempatkan</option>
                        @foreach ($classGroups as $group)
                            <option value="{{ $group->id }}" @selected((int) old('class_group_id', $classGroupId) === $group->id)>
                                {{ $group->code }} — {{ $group->education_level }} {{ $group->grade }} ({{ $group->status }})
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>
        </section>

        <section class="rounded-2xl border border-line bg-white p-5">
            <h2 class="text-[14.5px] font-bold text-ink">Orang Tua / Wali</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">Nama Wali</span>
                    <input type="text" name="guardian_name" value="{{ old('guardian_name', $guardian?->name) }}" class="{{ $inputClass }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Telepon Wali</span>
                    <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $guardian?->phone_wa) }}" class="{{ $inputClass }}">
                </label>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-end gap-2.5">
            <a href="{{ route('admin.dataSiswa') }}"
               class="rounded-lg border border-line bg-white px-4 py-2.5 text-[13px] font-semibold text-espresso transition hover:bg-sand">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-espresso px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-bark-800">
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Siswa' }}
            </button>
        </div>
    </form>
</div>
@endsection
