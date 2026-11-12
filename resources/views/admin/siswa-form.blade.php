@extends('layouts.admin')

@section('title', $student->exists ? 'Edit Data Siswa' : 'Tambah Siswa')

@section('content')
@php
    $isEdit = $student->exists;
    $action = $isEdit ? route('admin.dataSiswa.update', $student) : route('admin.dataSiswa.store');
    $statusPil = ['aktif' => 'Aktif', 'cuti' => 'Cuti', 'nonaktif' => 'Nonaktif', 'lulus' => 'Lulus'];
    $inputClass = 'mt-1.5 w-full rounded-lg border border-line bg-white px-3 py-2.5 text-[13px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30';
    $waClass = 'w-full min-w-0 bg-transparent px-3 py-2.5 text-[13px] text-ink focus:outline-none';
    $waWrap = 'mt-1.5 flex items-stretch overflow-hidden rounded-lg border border-line bg-white transition focus-within:border-gold focus-within:ring-2 focus-within:ring-gold/30';
    $labelClass = 'text-[10.5px] font-bold uppercase tracking-[0.1em] text-muted';
    $errorClass = 'mt-1.5 text-[11.5px] font-semibold text-ember-600';
@endphp

<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <nav class="flex items-center gap-1.5 text-[11.5px] font-semibold text-bark-400" aria-label="Breadcrumb">
        <a href="{{ route('admin.dataSiswa') }}" class="hover:text-bronze">Data Siswa</a>
        <span aria-hidden="true">/</span>
        <span class="text-ink">{{ $isEdit ? 'Edit' : 'Tambah' }}</span>
    </nav>
    <h1 class="mt-2 text-[26px] font-extrabold tracking-tight text-ink">{{ $isEdit ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h1>
    <p class="mt-2 max-w-xl text-[13px] leading-relaxed text-muted">
        Form ini mengikuti Formulir Pendaftaran Siswa Baru, dilengkapi data internal admin (NIS, status, dan penempatan kelas bimbel).
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

        {{-- Bagian 1 — data calon siswa (sama dengan formulir pendaftaran) --}}
        <section class="rounded-2xl border border-line bg-white p-5">
            <header class="flex items-start gap-3.5">
                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-espresso text-[14px] font-bold text-gold" aria-hidden="true">1</span>
                <div>
                    <h2 class="text-[14.5px] font-bold text-ink">Data Calon Siswa</h2>
                    <p class="mt-1 text-[12px] leading-[1.6] text-muted">Isi data dasar siswa untuk registrasi rombel belajar.</p>
                </div>
            </header>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">Nama Lengkap Siswa *</span>
                    <input type="text" name="full_name" value="{{ old('full_name', $student->full_name) }}" required class="{{ $inputClass }}">
                    @error('full_name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Nama Panggilan Siswa *</span>
                    <input type="text" name="nickname" value="{{ old('nickname', $student->nickname) }}" required class="{{ $inputClass }}">
                    @error('nickname') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Tanggal Lahir Siswa *</span>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}" required class="{{ $inputClass }}">
                    @error('birth_date') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Kelas T.A (Tahun Ajaran) *</span>
                    <select name="kelas" required class="{{ $inputClass }}">
                        <option value="">Pilih Jenjang / Kelas T.A</option>
                        @foreach ($kelasOptions as $jenjang => $opsiKelas)
                            <optgroup label="{{ $jenjang }}">
                                @foreach ($opsiKelas as $nilai => $labelKelas)
                                    <option value="{{ $nilai }}" @selected(old('kelas', $kelasValue) === $nilai)>{{ $labelKelas }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('kelas') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Asal Sekolah *</span>
                    <input type="text" name="school_name" value="{{ old('school_name', $student->school_name) }}" required placeholder="Contoh: SMPN 1 Yogyakarta" class="{{ $inputClass }}">
                    @error('school_name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">No. WA Siswa *</span>
                    <div class="{{ $waWrap }}">
                        <span class="inline-flex items-center border-r border-line bg-shell px-3 text-[13px] font-semibold text-muted" aria-hidden="true">+62</span>
                        <input type="tel" name="phone_wa" value="{{ old('phone_wa', $student->phone_wa) }}" required inputmode="tel" autocomplete="tel" placeholder="812-xxxx-xxxx" class="{{ $waClass }}">
                    </div>
                    @error('phone_wa') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block sm:col-span-2">
                    <span class="{{ $labelClass }}">Alamat Tempat Tinggal *</span>
                    <textarea name="address" rows="2" required placeholder="Alamat lengkap tempat tinggal siswa" class="{{ $inputClass }}">{{ old('address', $student->address) }}</textarea>
                    @error('address') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
            </div>
        </section>

        {{-- Bagian 2 — kontak wali & pengaturan grup (sama dengan formulir pendaftaran) --}}
        <section class="rounded-2xl border border-line bg-white p-5">
            <header class="flex items-start gap-3.5">
                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-espresso text-[14px] font-bold text-gold" aria-hidden="true">2</span>
                <div>
                    <h2 class="text-[14.5px] font-bold text-ink">Kontak Orang Tua / Wali &amp; Pengaturan Grup</h2>
                    <p class="mt-1 text-[12px] leading-[1.6] text-muted">Informasi wali siswa, nomor WhatsApp grup bimbingan, dan pilihan sesi belajar.</p>
                </div>
            </header>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">Nama Orang Tua / Wali *</span>
                    <input type="text" name="guardian_name" value="{{ old('guardian_name', $guardian?->name) }}" required autocomplete="name" placeholder="Contoh: Hendra Pratama" class="{{ $inputClass }}">
                    @error('guardian_name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">No. WA Wali *</span>
                    <div class="{{ $waWrap }}">
                        <span class="inline-flex items-center border-r border-line bg-shell px-3 text-[13px] font-semibold text-muted" aria-hidden="true">+62</span>
                        <input type="tel" name="guardian_phone" value="{{ old('guardian_phone', $guardian?->phone_wa) }}" required inputmode="tel" autocomplete="tel" placeholder="812-xxxx-xxxx" class="{{ $waClass }}">
                    </div>
                    @error('guardian_phone') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block sm:col-span-2">
                    <span class="{{ $labelClass }}">No. yang Dimasukkan ke Grup *</span>
                    <div class="{{ $waWrap }}">
                        <span class="inline-flex items-center border-r border-line bg-shell px-3 text-[13px] font-semibold text-muted" aria-hidden="true">+62</span>
                        <input type="tel" name="group_wa" value="{{ old('group_wa', $student->group_wa) }}" required inputmode="tel" placeholder="Nomor WA yang dimasukkan ke grup kelas/bimbel" class="{{ $waClass }}">
                    </div>
                    <p class="mt-1.5 text-[11.5px] leading-[1.6] text-muted">Nomor WhatsApp yang akan didaftarkan ke grup bimbel / WhatsApp group koordinasi rombel.</p>
                    @error('group_wa') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>

                <fieldset class="sm:col-span-2">
                    <legend class="{{ $labelClass }}">Pilihan Sesi Belajar *</legend>
                    <div class="mt-2 grid gap-3 sm:grid-cols-2">
                        @foreach ($sesiOptions as $nilaiSesi => $opsiSesi)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-line bg-white px-4 py-3.5 transition has-[:checked]:border-gold has-[:checked]:bg-sandgold/60">
                                <input type="radio" name="sesi" value="{{ $nilaiSesi }}" @checked(old('sesi', $sesiValue) === $nilaiSesi) required class="peer sr-only">
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-line bg-sandgold text-espresso" aria-hidden="true">
                                    @include('layouts.partials.icon', ['name' => $nilaiSesi === 'sore' ? 'sun' : 'moon', 'class' => 'h-5 w-5'])
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-[13px] font-bold text-ink">{{ $opsiSesi['label'] }}</span>
                                    <span class="block text-[11.5px] font-semibold text-bronze">{{ $opsiSesi['waktu'] }}</span>
                                </span>
                                <span class="relative grid h-5 w-5 shrink-0 place-items-center rounded-full border-2 border-line bg-white transition-colors before:absolute before:left-1/2 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-x-1/2 before:-translate-y-1/2 before:rounded-full before:bg-gold before:opacity-0 before:transition-opacity peer-checked:border-espresso peer-checked:bg-espresso peer-checked:before:opacity-100 peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true"></span>
                            </label>
                        @endforeach
                    </div>
                    @error('sesi') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </fieldset>
            </div>
        </section>

        {{-- Data internal admin — tidak ada di formulir pendaftaran --}}
        <section class="rounded-2xl border border-line bg-white p-5">
            <header class="flex items-start gap-3.5">
                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sand text-muted" aria-hidden="true">
                    @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-4 w-4'])
                </span>
                <div>
                    <h2 class="text-[14.5px] font-bold text-ink">Data Internal Admin</h2>
                    <p class="mt-1 text-[12px] leading-[1.6] text-muted">Field ini hanya dipakai untuk kebutuhan internal bimbel (tidak ada di formulir pendaftaran).</p>
                </div>
            </header>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">NIS</span>
                    <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" class="{{ $inputClass }}">
                    @error('nis') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">NISN</span>
                    <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" class="{{ $inputClass }}">
                    @error('nisn') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Tempat Lahir</span>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" class="{{ $inputClass }}">
                    @error('birth_place') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Email</span>
                    <input type="email" name="email" value="{{ old('email', $student->email) }}" class="{{ $inputClass }}">
                    @error('email') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Jurusan</span>
                    <input type="text" name="major" value="{{ old('major', $student->major) }}" placeholder="Contoh: IPA" class="{{ $inputClass }}">
                    @error('major') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Kurikulum</span>
                    <input type="text" name="curriculum" value="{{ old('curriculum', $student->curriculum) }}" placeholder="Contoh: Kurikulum Merdeka" class="{{ $inputClass }}">
                    @error('curriculum') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Status Siswa *</span>
                    <select name="status" required class="{{ $inputClass }}">
                        @foreach ($statusPil as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $student->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Kelas Bimbel</span>
                    <select name="class_group_id" class="{{ $inputClass }}">
                        <option value="">Belum ditempatkan</option>
                        @foreach ($classGroups as $group)
                            <option value="{{ $group->id }}" @selected((int) old('class_group_id', $classGroupId) === $group->id)>
                                {{ $group->code }} — {{ $group->education_level }} {{ $group->grade }} ({{ $group->status }})
                            </option>
                        @endforeach
                    </select>
                    @error('class_group_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
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
