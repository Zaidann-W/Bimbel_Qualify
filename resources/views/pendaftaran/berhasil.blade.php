@extends('layouts.main')

@section('title', 'Pendaftaran Berhasil — Bimbel QUALIFY')

@section('content')
    <section class="relative overflow-hidden bg-cream py-24">
        <div class="relative mx-auto max-w-[600px] px-6 lg:px-12">
            <div class="max-w-[400px] mx-auto">
                <div class="bg-paper border border-line/60 rounded-[24px] p-8 sm:p-12 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gold text-espresso mb-6">
                        @include('partials.icon', ['name' => 'check-circle', 'size' => 32, 'stroke' => 1.9])
                    </span>

                    <h2 class="text-[28px] sm:text-[32px] font-extrabold leading-tight text-ink mb-4">
                        Pendaftaran Selesai
                    </h2>

                    <p class="text-[16px] sm:text-[17px] leading-[1.75] text-muted mb-8">
                        Akun orang tua dan siswa untuk pendaftaran
                        <span class="font-bold text-gold">{{ $application->registration_number }}</span>
                        berhasil dibuat.
                    </p>

                    <div class="mt-8 pt-8 border-t border-line/60">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center bg-espresso hover:bg-[#3a2515] text-paper font-bold text-[15px] px-6 py-3.5 rounded-xl transition-colors w-full sm:w-auto justify-center">
                            Masuk ke Halaman Login
                        </a>
                        <div class="mt-4">
                            <a href="{{ url('/') }}"
                               class="inline-flex items-center text-[14px] text-muted hover:text-ink underline">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
