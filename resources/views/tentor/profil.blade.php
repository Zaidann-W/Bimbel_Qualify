@extends('layouts.tentor')

@section('title', 'Profil Saya')

@section('content')
@php
    $staff = $staffProfile;
    $user = $user;
    $initials = $staff
        ? \Illuminate\Support\Str::of($staff->full_name_with_title)
            ->replaceMatches('/[.,]/', '')
            ->explode(' ')
            ->reject(fn (string $word): bool => in_array(\Illuminate\Support\Str::lower($word), ['kak', 'dr', 'drs', 'ir', 'prof'], true))
            ->filter()
            ->take(2)
            ->map(fn (string $word): string => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($word, 0, 1)))
            ->implode('')
        : 'T';
    $keahlian = $staff?->job_title ? explode(',', $staff->job_title) : [];
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Profil Pengajar & Akun Tentor</h1>
            <p class="mt-1 text-[13px] text-bark-400">Kelola identitas pengajar, kontak konsultasi siswa, preferensi mengajar, dan keamanan akun.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-[11.5px] font-bold text-emerald-600 ring-1 ring-emerald-100">
            @include('layouts.partials.icon', ['name' => 'shield-check', 'class' => 'h-3.5 w-3.5'])
            Terverifikasi Akademik
        </span>
    </div>

    {{-- Kartu identitas --}}
    <section class="rounded-2xl border border-ember-200 bg-ember-50/60 p-5">
        <div class="flex flex-wrap items-start gap-5">
            <div class="relative">
                <div class="grid h-20 w-20 place-items-center rounded-xl bg-bark-800 text-[22px] font-bold text-white">{{ $initials }}</div>
                <span class="absolute -bottom-1 -right-1 grid h-6 w-6 place-items-center rounded-full bg-ember-400 text-bark-900 ring-2 ring-ember-50">
                    @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3.5 w-3.5'])
                </span>
            </div>

            <div class="min-w-0 flex-1">
                <h2 class="text-[19px] font-bold text-bark-800">{{ $staff->full_name_with_title ?? $user->name }}</h2>
                <span class="mt-1 inline-block rounded-md bg-ember-100 px-2 py-[3px] text-[10px] font-bold text-ember-700">
                    Tentor Aktif {{ $staff?->status ?? 'Semester Genap 2024/2025' }}
                </span>
                <p class="mt-2 text-[13.5px] font-semibold text-bark-600">{{ $staff->job_title ?? 'Master Trainer Matematika & Fisika Medika' }}</p>
                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11.5px] text-bark-400">
                    <span class="inline-flex items-center gap-1.5">
                        @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-3.5 w-3.5'])
                        NIP: {{ $staff->employee_number ?? '1-06812' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-3.5 w-3.5'])
                        Bergabung sejak {{ $staff?->joined_on?->format('M Y') ?? 'Jul 2021' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-3.5 w-3.5'])
                        {{ $staff->address ?? 'Kampus Pusat Dipati Ukur Bandung' }}
                    </span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($keahlian as $k)
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-ember-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-ember-700">
                            @include('layouts.partials.icon', ['name' => 'flame', 'class' => 'h-3 w-3'])
                            {{ trim($k) }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="flex shrink-0 flex-col gap-2">
                <button type="button" id="editProfileBtn" class="inline-flex items-center justify-center gap-2 rounded-lg bg-ember-500 px-4 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-ember-600">
                    @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-3.5 w-3.5'])
                    Edit Profil
                </button>
                <button type="button" id="changePasswordBtn" class="inline-flex items-center justify-center gap-2 rounded-lg border border-bark-200 bg-white px-4 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                    @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-3.5 w-3.5'])
                    Ubah Kata Sandi
                </button>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Kiri: informasi pribadi --}}
        <section class="rounded-2xl border border-bark-100 bg-white lg:col-span-2">
            <header class="border-b border-bark-100 px-5 py-4">
                <div class="flex items-center gap-2.5">
                    <div class="grid h-9 w-9 place-items-center rounded-xl bg-ember-50 text-ember-500">
                        @include('layouts.partials.icon', ['name' => 'user', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Informasi Pribadi & Kontak</h2>
                        <p class="text-[11px] text-bark-400">Data kontak tentor untuk keperluan koordinasi dan koordinasi akademik.</p>
                    </div>
                </div>
            </header>

            {{-- VIEW MODE --}}
            <div id="profileView" class="px-5 py-4">
                <div class="grid grid-cols-1 gap-x-5 gap-y-4 sm:grid-cols-2">
                    <div>
                        <p class="text-[12px] font-medium text-bark-400">Nama Lengkap & Gelar</p>
                        <p class="text-[13px] font-semibold text-bark-800">{{ $staff->full_name_with_title ?? $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-bark-400">Pendidikan Terakhir</p>
                        <p class="text-[13px] font-semibold text-bark-800">{{ $staff->education ?? 'S2 Magister Matematika - ITB' }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-bark-400">Email Pengajar Qualify</p>
                        <p class="text-[13px] font-semibold text-bark-800">{{ $user->email }}</p>
                        <p class="mt-1 text-[10.5px] text-bark-400">Digunakan untuk akses Google Workspace & SSO Akademik</p>
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-bark-400">Nomor WhatsApp Aktif</p>
                        <p class="text-[13px] font-semibold text-bark-800">{{ $user->phone ?? '+62 813-9821-7740' }}</p>
                        <p class="mt-1 text-[10.5px] text-bark-400">Terhubung ke Portal Siswa & Layanan Wali Murid</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-[12px] font-medium text-bark-400">Alamat Domisili Mengajar</p>
                        <p class="text-[13px] font-semibold text-bark-800">{{ $staff->address ?? 'Jl. Dayang Sumbi No. 14, Coblong, Kota Bandung, Jawa Barat' }}</p>
                    </div>
                </div>

                <div class="mt-5 flex items-start gap-2.5 rounded-xl border border-ember-200 bg-ember-50 px-4 py-3">
                    @include('layouts.partials.icon', ['name' => 'info', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
                    <p class="text-[11.5px] leading-relaxed text-bark-500">
                        Untuk memperbarui nomor kontak aktif, NIK, atau izin transfer honorarium, silakan mengajukan permohonan ke
                        <span class="font-bold text-bark-700">Bio Administrasi Kepegawaian (HR)</span>.
                    </p>
                </div>
            </div>

            {{-- EDIT MODE --}}
            <form id="profileEditForm" action="{{ route('tentor.profil.update') }}" method="POST" class="px-5 py-4 hidden">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-x-5 gap-y-4 sm:grid-cols-2">
                    <div>
                        <label class="text-[12px] font-bold text-bark-700">Nama Lengkap & Gelar <span class="text-ember-500">*</span></label>
                        <input type="text" name="full_name_with_title" value="{{ $staff->full_name_with_title ?? $user->name }}"
                               class="mt-1.5 w-full rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100" required>
                    </div>
                    <div>
                        <label class="text-[12px] font-bold text-bark-700">Pendidikan Terakhir</label>
                        <input type="text" name="education" value="{{ $staff->education }}"
                               class="mt-1.5 w-full rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                    </div>
                    <div>
                        <label class="text-[12px] font-bold text-bark-700">Email</label>
                        <input type="email" value="{{ $user->email }}"
                               class="mt-1.5 w-full rounded-lg border border-bark-200 bg-bark-50 px-3 py-2.5 text-[13px] text-bark-500 cursor-not-allowed" disabled>
                        <p class="mt-1 text-[10.5px] text-bark-400">Email tidak bisa diubah di sini. Hubungi admin untuk perubahan email.</p>
                    </div>
                    <div>
                        <label class="text-[12px] font-bold text-bark-700">Nomor WhatsApp Aktif <span class="text-ember-500">*</span></label>
                        <input type="tel" name="phone" value="{{ $user->phone }}"
                               class="mt-1.5 w-full rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[12px] font-bold text-bark-700">Alamat Domisili Mengajar</label>
                        <input type="text" name="address" value="{{ $staff->address }}"
                               class="mt-1.5 w-full rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[12px] font-bold text-bark-700">Keahlian / Mata Pelajaran</label>
                        <input type="text" name="job_title" value="{{ $staff->job_title }}"
                               class="mt-1.5 w-full rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100"
                               placeholder="Pisahkan dengan koma, cth: Matematika Saintek, TPS Kuantitatif, OSN">
                        <p class="mt-1 text-[10.5px] text-bark-400">Pisahkan dengan koma untuk setiap keahlian.</p>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-ember-500 px-4 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-ember-600">
                        @include('layouts.partials.icon', ['name' => 'save', 'class' => 'h-3.5 w-3.5'])
                        Simpan Perubahan
                    </button>
                    <button type="button" id="cancelEditBtn" class="inline-flex items-center justify-center gap-2 rounded-lg border border-bark-200 bg-white px-4 py-2.5 text-[12.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                        Batal
                    </button>
                </div>
            </form>
        </section>

        {{-- Kanan: preferensi & notifikasi --}}
        <section class="self-start rounded-2xl border border-bark-100 bg-white">
            <header class="border-b border-bark-100 px-5 py-4">
                <div class="flex items-center gap-2.5">
                    <div class="grid h-9 w-9 place-items-center rounded-xl bg-ember-50 text-ember-500">
                        @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Preferensi Mengajar & Notifikasi</h2>
                        <p class="text-[11px] text-bark-400">Atur jadwal operasional & notifikasi jawaban</p>
                    </div>
                </div>
            </header>

            <div class="space-y-5 px-5 py-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">Jadwal Mengajar Aktif</p>
                    <div class="mt-2 rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
                        <p class="text-[14px] font-bold text-bark-800">Senin, Rabu, & Kamis</p>
                        <p class="mt-0.5 text-[12px] text-bark-400">Sesi Sore (15.30 – 18.30)</p>
                    </div>
                </div>

                <div class="flex items-start justify-between gap-3 rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
                    <div>
                        <p class="text-[13px] font-bold text-bark-800">Status Notifikasi Klinik PR Mandiri</p>
                        <p class="mt-0.5 text-[11.5px] leading-relaxed text-bark-400">Dapatkan alert real-time ketika ada siswa masuk di subjek Matematika/Fisika</p>
                    </div>
                    <button type="button" role="switch" aria-checked="true" aria-label="Status notifikasi Klinik PR Mandiri"
                            class="mt-0.5 h-6 w-11 shrink-0 rounded-full bg-ember-500 p-0.5 transition">
                        <span class="block h-5 w-5 translate-x-5 rounded-full bg-white shadow-sm transition"></span>
                    </button>
                </div>
            </div>
        </section>
    </div>

    {{-- Modal Ubah Kata Sandi --}}
    <div id="passwordModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-bark-900/50 transition-opacity" aria-hidden="true"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[17px] font-bold text-bark-800">Ubah Kata Sandi</h3>
                    <button type="button" id="closePasswordModal" class="text-bark-400 hover:text-bark-600">
                        @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-5 w-5'])
                    </button>
                </div>

                <form action="{{ route('tentor.profil.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-[12px] font-bold text-bark-700">Kata Sandi Saat Ini <span class="text-ember-500">*</span></label>
                        <input type="password" name="current_password" required autocomplete="current-password"
                               class="mt-1.5 w-full rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                        @error('current_password')
                            <p class="mt-1 text-[11px] text-ember-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-[12px] font-bold text-bark-700">Kata Sandi Baru <span class="text-ember-500">*</span></label>
                        <input type="password" name="password" required autocomplete="new-password"
                               class="mt-1.5 w-full rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                        @error('password')
                            <p class="mt-1 text-[11px] text-ember-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-[12px] font-bold text-bark-700">Konfirmasi Kata Sandi Baru <span class="text-ember-500">*</span></label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                               class="mt-1.5 w-full rounded-lg border border-bark-200 bg-white px-3 py-2.5 text-[13px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-2">
                        <button type="button" id="cancelPasswordBtn" class="rounded-lg border border-bark-200 bg-white px-4 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-500 px-4 py-2 text-[12px] font-semibold text-white shadow-sm transition hover:bg-ember-600">
                            @include('layouts.partials.icon', ['name' => 'lock', 'class' => 'h-3.5 w-3.5'])
                            Ubah Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileView = document.getElementById('profileView');
    const profileEditForm = document.getElementById('profileEditForm');
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');

    const passwordModal = document.getElementById('passwordModal');
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const closePasswordModal = document.getElementById('closePasswordModal');
    const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');

    // Toggle edit mode
    if (editProfileBtn && profileView && profileEditForm) {
        editProfileBtn.addEventListener('click', function() {
            profileView.classList.add('hidden');
            profileEditForm.classList.remove('hidden');
            editProfileBtn.classList.add('hidden');
        });
    }
    if (cancelEditBtn && profileView && profileEditForm && editProfileBtn) {
        cancelEditBtn.addEventListener('click', function() {
            profileEditForm.classList.add('hidden');
            profileView.classList.remove('hidden');
            editProfileBtn.classList.remove('hidden');
        });
    }

    // Password modal
    function openModal() {
        passwordModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        passwordModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (changePasswordBtn) changePasswordBtn.addEventListener('click', openModal);
    if (closePasswordModal) closePasswordModal.addEventListener('click', closeModal);
    if (cancelPasswordBtn) cancelPasswordBtn.addEventListener('click', closeModal);
    if (passwordModal) {
        passwordModal.addEventListener('click', function(e) {
            if (e.target === passwordModal) closeModal();
        });
    }
    // ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !passwordModal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>
@endpush