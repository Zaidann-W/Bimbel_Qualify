@extends('layouts.main')

@section('title', 'Atur Ulang Kata Sandi — Bimbel QUALIFY')
@section('meta_description', 'Atur ulang kata sandi akun Bimbel QUALIFY Anda.')

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
                    'uid' => 'reset-password',
                    'stack' => true,
                    'titleSize' => 'text-[30px]',
                    'sub' => 'Bimbel yang selalu Berqualitas',
                    'subWrap' => 'max-w-none',
                ])
            </div>

            {{-- Kartu reset password --}}
            <div class="mx-auto mt-8 w-full max-w-[440px] rounded-[24px] border border-line/60 bg-paper p-7 sm:p-9 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <h1 class="text-center text-[22px] sm:text-[24px] font-extrabold leading-tight text-ink">
                    Atur Ulang Kata Sandi
                </h1>
                <p class="mt-2 text-center text-[14px] leading-[1.6] text-muted">
                    Masukkan kata sandi baru Anda di bawah ini.
                </p>

                {{-- Error message --}}
                @if ($errors->any())
                    <div class="mt-5 p-4 rounded-xl bg-ember-50 border border-ember-200 text-ember-700 text-[14px] text-center" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST" novalidate class="mt-7">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

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
                                value="{{ $email ?? old('email') }}"
                                readonly
                                class="w-full min-w-0 bg-transparent px-3.5 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none cursor-not-allowed bg-sand"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-[12.5px] font-medium text-ember-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-5" data-field>
                        <label for="password" class="block text-[13.5px] font-bold text-ink">
                            Kata Sandi Baru
                        </label>
                        <div
                            class="mt-2 flex items-stretch rounded-xl border border-line bg-shell transition-colors focus-within:border-gold focus-within:bg-paper focus-within:ring-4 focus-within:ring-gold/20"
                            data-control-surface
                            data-border-default="border-line"
                        >
                            <span class="inline-flex items-center pl-4 text-ink/45" aria-hidden="true">
                                @include('partials.icon', ['name' => 'lock', 'size' => 18])
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="min. 8 karakter, huruf besar/kecil, angka & simbol"
                                class="w-full min-w-0 bg-transparent px-3.5 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none"
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            >
                            <button
                                type="button"
                                class="inline-flex shrink-0 items-center px-4 text-ink/45 transition-colors hover:text-ink"
                                aria-label="Tampilkan kata sandi"
                                aria-controls="password"
                                onclick="togglePassword('password', this)"
                            >
                                <span class="icon-eye">
                                    @include('partials.icon', ['name' => 'eye', 'size' => 18])
                                </span>
                                <span class="icon-eye-off hidden">
                                    @include('partials.icon', ['name' => 'eye-off', 'size' => 18])
                                </span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-[12.5px] font-medium text-ember-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-5" data-field>
                        <label for="password_confirmation" class="block text-[13.5px] font-bold text-ink">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <div
                            class="mt-2 flex items-stretch rounded-xl border border-line bg-shell transition-colors focus-within:border-gold focus-within:bg-paper focus-within:ring-4 focus-within:ring-gold/20"
                            data-control-surface
                            data-border-default="border-line"
                        >
                            <span class="inline-flex items-center pl-4 text-ink/45" aria-hidden="true">
                                @include('partials.icon', ['name' => 'lock', 'size' => 18])
                            </span>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="ulangi kata sandi baru"
                                class="w-full min-w-0 bg-transparent px-3.5 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none"
                                aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                            >
                            <button
                                type="button"
                                class="inline-flex shrink-0 items-center px-4 text-ink/45 transition-colors hover:text-ink"
                                aria-label="Tampilkan kata sandi"
                                aria-controls="password_confirmation"
                                onclick="togglePassword('password_confirmation', this)"
                            >
                                <span class="icon-eye">
                                    @include('partials.icon', ['name' => 'eye', 'size' => 18])
                                </span>
                                <span class="icon-eye-off hidden">
                                    @include('partials.icon', ['name' => 'eye-off', 'size' => 18])
                                </span>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-1.5 text-[12.5px] font-medium text-ember-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="mt-5 flex w-full items-center justify-center gap-2.5 rounded-xl bg-gold px-6 py-[17px] text-[16px] font-bold text-espresso transition-colors hover:bg-[#ecab20]"
                    >
                        Simpan Kata Sandi Baru
                        @include('partials.icon', ['name' => 'arrow-right', 'size' => 18])
                    </button>

                    <p class="mt-5 text-center text-[14px] text-muted">
                        <a href="{{ route('login') }}" class="font-bold text-bronze hover:text-[#7d5d12] transition-colors">
                            Kembali ke Masuk
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function togglePassword(inputId, button) {
            var input = document.getElementById(inputId);
            var iconEye = button.querySelector('.icon-eye');
            var iconEyeOff = button.querySelector('.icon-eye-off');

            if (!input || !iconEye || !iconEyeOff) {
                return;
            }

            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            iconEye.classList.toggle('hidden', isPassword);
            iconEyeOff.classList.toggle('hidden', !isPassword);
            input.focus();
        }
    </script>
@endpush