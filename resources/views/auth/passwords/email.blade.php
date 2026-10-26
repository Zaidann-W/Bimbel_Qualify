@extends('layouts.main')

@section('title', 'Lupa Kata Sandi — Bimbel QUALIFY')
@section('meta_description', 'Minta tautan reset kata sandi untuk akun Bimbel QUALIFY Anda.')

@section('content')
    <section class="relative overflow-hidden bg-cream">
        {{-- Hangat khas QUALIFY: wash radial emas --}}
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -top-40 left-1/2 h-[560px] w-[820px] -translate-x-1/2 rounded-full bg-[radial-gradient(closest-side,rgba(245,196,84,0.26),rgba(245,196,84,0))]"></div>
            <div class="absolute -bottom-56 -right-32 h-[520px] w-[680px] rounded-full bg-[radial-gradient(closest-side,rgba(238,216,172,0.5),rgba(238,216,172,0))]"></div>
            <div class="absolute -top-24 -left-40 h-[440px] w-[560px] rounded-full bg-[radial-gradient(closest-side,rgba(245,196,84,0.14),rgba(245,196,84,0))]"></div>
        </div>

        <div class="relative mx-auto max-w-[1400px] px-6 lg:px-12 pt-12 lg:pt-16 pb-16 lg:pb-24">
            {{-- Logo --}}
            <div class="flex justify-center">
                @include('partials.logo', [
                    'size' => 84,
                    'variant' => 'gold',
                    'uid' => 'forgot-password',
                    'stack' => true,
                    'titleSize' => 'text-[30px]',
                    'sub' => 'Bimbel yang selalu Berqualitas',
                    'subWrap' => 'max-w-none',
                ])
            </div>

            {{-- Kartu lupa password --}}
            <div class="mx-auto mt-8 w-full max-w-[440px] rounded-[24px] border border-line/60 bg-paper p-7 sm:p-9 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <h1 class="text-center text-[22px] sm:text-[24px] font-extrabold leading-tight text-ink">
                    Lupa Kata Sandi?
                </h1>
                <p class="mt-2 text-center text-[14px] leading-[1.6] text-muted">
                    Masukkan email akun Anda, kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                </p>

                {{-- Status message --}}
                @if (session('status'))
                    <div class="mt-5 p-4 rounded-xl bg-forest-50 border border-forest-200 text-forest-700 text-[14px] text-center" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Error message --}}
                @if ($errors->any())
                    <div class="mt-5 p-4 rounded-xl bg-ember-50 border border-ember-200 text-ember-700 text-[14px] text-center" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" novalidate class="mt-7">
                    @csrf

                    <div data-field>
                        <label for="email" class="block text-[13.5px] font-bold text-ink">
                            Email
                        </label>
                        <div
                            class="mt-2 flex items-stretch rounded-xl border border-line bg-shell transition-colors focus-within:border-gold focus-within:bg-paper focus-within:ring-4 focus-within:ring-gold/20"
                            data-control-surface
                            data-border-default="border-line"
                        >
                            <span class="inline-flex items-center pl-4 text-ink/45" aria-hidden="true">
                                @include('partials.icon', ['name' => 'mail', 'size' => 18])
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                required
                                autocomplete="email"
                                placeholder="nama@contoh.com"
                                value="{{ old('email') }}"
                                class="w-full min-w-0 bg-transparent px-3.5 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none"
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-[12.5px] font-medium text-ember-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="mt-5 flex w-full items-center justify-center gap-2.5 rounded-xl bg-gold px-6 py-[17px] text-[16px] font-bold text-espresso transition-colors hover:bg-[#ecab20]"
                    >
                        Kirim Tautan Reset
                        @include('partials.icon', ['name' => 'arrow-right', 'size' => 18])
                    </button>

                    <p class="mt-5 text-center text-[14px] text-muted">
                        Ingat kata sandi?
                        <a href="{{ route('login') }}" class="font-bold text-bronze hover:text-[#7d5d12] transition-colors">
                            Masuk
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </section>
@endsection