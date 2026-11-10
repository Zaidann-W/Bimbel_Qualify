@extends('layouts.main')

@section('title', 'Buat Akun Orang Tua & Siswa — Bimbel QUALIFY')

@section('content')
    <section class="relative overflow-hidden bg-cream py-24">
        <div class="relative mx-auto max-w-[1400px] px-6 lg:px-12">
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-[13px] text-muted">
                <a href="{{ url('/') }}" class="hover:text-ink transition-colors">Beranda</a>
                @include('partials.icon', ['name' => 'chevron-right', 'size' => 14, 'class' => 'shrink-0 text-ink/35'])
                <span class="font-semibold text-ink" aria-current="page">Buat Akun</span>
            </nav>

            <div class="mt-8 max-w-[800px] mx-auto">
                <div class="bg-paper border border-line/60 rounded-[24px] p-8 sm:p-12 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                    <h2 class="text-[28px] sm:text-[32px] font-extrabold leading-tight text-ink mb-1">
                        Buat Akun Orang Tua &amp; Siswa
                    </h2>
                    <p class="text-[15px] leading-[1.7] text-muted mb-6">
                        Nomor registrasi: <span class="font-bold text-gold">{{ $application->registration_number }}</span>.
                        Silakan buat akun login untuk orang tua dan siswa.
                    </p>

                    <form action="{{ route('pendaftaran.buatAkun.simpan', $application->registration_number) }}"
                          method="POST">
                        @csrf

                        <div class="space-y-6">
                            <!-- Orang Tua -->
                            <fieldset class="border border-line rounded-xl p-5">
                                <legend class="px-2 text-[14px] font-bold text-ink">Data Akun Orang Tua</legend>
                                <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">Nama Lengkap</label>
                                        <input type="text" name="ortu_name"
                                               value="{{ old('ortu_name') ?? $application->guardian_name }}"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">Email (untuk login)</label>
                                        <input type="email" name="ortu_email"
                                               value="{{ old('ortu_email') }}"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                        <p class="text-[11px] text-muted">Email akan digunakan untuk login orang tua.</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">No. HP / WhatsApp</label>
                                        <input type="text" name="ortu_phone"
                                               value="{{ old('ortu_phone') ?? $application->guardian_phone }}"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">Password</label>
                                        <input type="password" name="ortu_password"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                        <p class="text-[11px] text-muted">Minimal 8 karakter.</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">Konfirmasi Password</label>
                                        <input type="password" name="ortu_password_confirmation"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Siswa -->
                            <fieldset class="border border-line rounded-xl p-5">
                                <legend class="px-2 text-[14px] font-bold text-ink">Data Akun Siswa</legend>
                                <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">Nama Lengkap Siswa</label>
                                        <input type="text" name="siswa_name"
                                               value="{{ old('siswa_name') ?? $application->full_name }}"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">Email Siswa (untuk login)</label>
                                        <input type="email" name="siswa_email"
                                               value="{{ old('siswa_email') }}"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                        <p class="text-[11px] text-muted">Email akan digunakan untuk login siswa.</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">No. HP Siswa (opsional)</label>
                                        <input type="text" name="siswa_phone"
                                               value="{{ old('siswa_phone') ?? $application->phone_wa }}"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">Password Siswa</label>
                                        <input type="password" name="siswa_password"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                        <p class="text-[11px] text-muted">Minimal 8 karakter.</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[12.5px] font-semibold text-muted">Konfirmasi Password Siswa</label>
                                        <input type="password" name="siswa_password_confirmation"
                                               class="block w-full rounded-lg border border-line bg-white px-4 py-2.5 text-ink focus:border-gold focus:ring-1 focus:ring-gold">
                                    </div>
                                </div>
                            </fieldset>

                            <div class="pt-4 flex items-center gap-3">
                                <a href="{{ route('registrasi.konfirmasi') }}"
                                   class="text-[14px] text-muted hover:text-ink underline">
                                    Kembali
                                </a>
                                <button type="submit"
                                        class="flex-1 bg-gold hover:bg-palegold text-espresso font-bold py-3.5 rounded-xl transition-colors">
                                    Buat Akun
                                </button>
                            </div>
                        </div>
                    </form>

                    @if ($errors->any())
                        <div class="mt-4 p-4 rounded-xl bg-ember-50 border border-ember-100 text-ember-700">
                            <ul class="list-disc list-inside text-[13px]">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
