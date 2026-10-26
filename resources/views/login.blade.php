@extends('layouts.main')

@section('title', 'Masuk ke Akun — Bimbel QUALIFY')
@section('meta_description', 'Masuk ke akun Bimbel QUALIFY dengan email untuk melanjutkan ke dashboard Anda.')

@section('content')
    <section class="relative overflow-hidden bg-cream">
        {{-- Hangat khas QUALIFY: wash radial emas seperti pada halaman beranda & registrasi --}}
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -top-40 left-1/2 h-[560px] w-[820px] -translate-x-1/2 rounded-full bg-[radial-gradient(closest-side,rgba(245,196,84,0.26),rgba(245,196,84,0))]"></div>
            <div class="absolute -bottom-56 -right-32 h-[520px] w-[680px] rounded-full bg-[radial-gradient(closest-side,rgba(238,216,172,0.5),rgba(238,216,172,0))]"></div>
            <div class="absolute -top-24 -left-40 h-[440px] w-[560px] rounded-full bg-[radial-gradient(closest-side,rgba(245,196,84,0.14),rgba(245,196,84,0))]"></div>
        </div>

        <div class="relative mx-auto max-w-[1400px] px-6 lg:px-12 pt-12 lg:pt-16 pb-16 lg:pb-24">
            {{-- Logo bertumpuk: satu momen emas yang menjadi wajah halaman ini --}}
            <div class="flex justify-center">
                @include('partials.logo', [
                    'size' => 84,
                    'variant' => 'gold',
                    'uid' => 'login',
                    'stack' => true,
                    'titleSize' => 'text-[30px]',
                    'sub' => 'Bimbel yang selalu Berqualitas',
                    'subWrap' => 'max-w-none',
                ])
            </div>

            {{-- Kartu masuk --}}
            <div class="mx-auto mt-8 w-full max-w-[440px] rounded-[24px] border border-line/60 bg-paper p-7 sm:p-9 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                <h1 class="text-center text-[22px] sm:text-[24px] font-extrabold leading-tight text-ink">
                    Selamat Datang Kembali
                </h1>
                <p class="mt-2 text-center text-[14px] leading-[1.6] text-muted">
                    Masukkan kredensial akun Anda untuk melanjutkan
                </p>

                {{-- Server-side error message (rate limit, inactive account, dll) --}}
                @if ($errors->any())
                    <div class="mt-4 p-4 rounded-xl bg-ember-50 border border-ember-200 text-ember-700 text-[14px] text-center" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="form-masuk" action="{{ route('login.simpan') }}" method="POST" novalidate class="mt-7">
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
                                data-label="Email"
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

                    <div class="mt-5" data-field>
                        <div class="flex items-baseline justify-between gap-4">
                            <label for="password" class="block text-[13.5px] font-bold text-ink">
                                Kata Sandi
                            </label>
                            <a href="{{ route('password.request') }}" class="text-[13px] font-bold text-bronze hover:text-[#7d5d12] transition-colors">
                                Lupa Kata Sandi?
                            </a>
                        </div>
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
                                data-label="Kata sandi"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi"
                                class="w-full min-w-0 bg-transparent px-3.5 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none"
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            >
                            <button
                                type="button"
                                id="toggle-password"
                                class="inline-flex shrink-0 items-center px-4 text-ink/45 transition-colors hover:text-ink"
                                aria-label="Tampilkan kata sandi"
                                aria-controls="password"
                            >
                                <span id="ikon-mata">
                                    @include('partials.icon', ['name' => 'eye', 'size' => 18])
                                </span>
                                <span id="ikon-mata-tutup" class="hidden">
                                    @include('partials.icon', ['name' => 'eye-off', 'size' => 18])
                                </span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-[12.5px] font-medium text-ember-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="-m-1 flex cursor-pointer items-center gap-3 rounded-xl p-1">
                            <input type="checkbox" name="remember" class="peer sr-only">
                            <span class="grid h-5 w-5 shrink-0 place-items-center rounded-md border-2 border-line bg-shell text-transparent transition-colors peer-checked:border-espresso peer-checked:bg-espresso peer-checked:text-gold peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true">
                                @include('partials.icon', ['name' => 'check', 'size' => 12, 'stroke' => 3.2])
                            </span>
                            <span class="text-[14px] text-ink">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        id="btn-submit"
                        class="mt-5 flex w-full items-center justify-center gap-2.5 rounded-xl bg-gold px-6 py-[17px] text-[16px] font-bold text-espresso transition-colors hover:bg-[#ecab20] disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <span id="btn-text">Masuk ke Akun</span>
                        <span id="btn-loading" class="hidden flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                        @include('partials.icon', ['name' => 'arrow-right', 'size' => 18])
                    </button>

                    <p class="mt-5 text-center text-[14px] text-muted">
                        Belum memiliki akun?
                        <a href="{{ route('registrasi') }}" class="font-bold text-bronze hover:text-[#7d5d12] transition-colors">
                            Daftar sekarang
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('form-masuk');

            if (!form) {
                return;
            }

            var gerakHemat = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var btnSubmit = document.getElementById('btn-submit');
            var btnText = document.getElementById('btn-text');
            var btnLoading = document.getElementById('btn-loading');

            function permukaan(kontrol) {
                var field = kontrol.closest('[data-field]');
                return field ? field.querySelector('[data-control-surface]') : null;
            }

            function kotakError(kontrol) {
                return form.querySelector('[data-error-for="' + kontrol.name + '"]');
            }

            function valid(kontrol) {
                return kontrol.value.trim() !== '';
            }

            function pesanError(kontrol) {
                return (kontrol.dataset.label || 'Bagian ini') + ' wajib diisi.';
            }

            function gayaError(surface, adaError) {
                if (!surface) {
                    return;
                }

                surface.classList.toggle('ring-2', adaError);
                surface.classList.toggle('ring-ember-300', adaError);

                if (surface.dataset.borderDefault) {
                    surface.classList.toggle(surface.dataset.borderDefault, !adaError);
                    surface.classList.toggle('border-ember-400', adaError);
                }
            }

            function tandaiError(kontrol, adaError) {
                var kotak = kotakError(kontrol);

                if (kotak) {
                    kotak.textContent = adaError ? pesanError(kontrol) : '';
                    kotak.classList.toggle('hidden', !adaError);
                }

                gayaError(permukaan(kontrol), adaError);
                kontrol.setAttribute('aria-invalid', adaError ? 'true' : 'false');
            }

            function gulirKe(elemen) {
                elemen.scrollIntoView({ behavior: gerakHemat ? 'auto' : 'smooth', block: 'center' });
            }

            function setLoading(loading) {
                if (btnSubmit) {
                    btnSubmit.disabled = loading;
                    if (btnText) btnText.classList.toggle('hidden', loading);
                    if (btnLoading) btnLoading.classList.toggle('hidden', !loading);
                }
            }

            var wajib = Array.prototype.slice.call(form.querySelectorAll('[required]'));

            wajib.forEach(function (kontrol) {
                kontrol.addEventListener('input', function () {
                    if (kontrol.getAttribute('aria-invalid') === 'true') {
                        tandaiError(kontrol, !valid(kontrol));
                    }
                });
            });

            // Tampilkan / sembunyikan kata sandi
            var tombolMata = document.getElementById('toggle-password');
            var inputSandi = document.getElementById('password');

            if (tombolMata && inputSandi) {
                tombolMata.addEventListener('click', function () {
                    var sedangTampil = inputSandi.type === 'password';

                    inputSandi.type = sedangTampil ? 'text' : 'password';
                    tombolMata.setAttribute('aria-label', sedangTampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                    document.getElementById('ikon-mata').classList.toggle('hidden', sedangTampil);
                    document.getElementById('ikon-mata-tutup').classList.toggle('hidden', !sedangTampil);
                    inputSandi.focus();
                });
            }

            // Handle server-side errors on page load
            wajib.forEach(function (kontrol) {
                if (kontrol.getAttribute('aria-invalid') === 'true') {
                    tandaiError(kontrol, true);
                }
            });

            form.addEventListener('submit', function (event) {
                var pertamaInvalid = null;

                wajib.forEach(function (kontrol) {
                    var oke = valid(kontrol);

                    tandaiError(kontrol, !oke);

                    if (!oke && !pertamaInvalid) {
                        pertamaInvalid = kontrol;
                    }
                });

                if (pertamaInvalid) {
                    event.preventDefault();
                    gulirKe(permukaan(pertamaInvalid) || pertamaInvalid);
                    pertamaInvalid.focus({ preventScroll: true });
                    return;
                }

                // Client-side validation passed - show loading state
                setLoading(true);

                // Safety: re-enable button after 10s if something goes wrong
                setTimeout(function () {
                    setLoading(false);
                }, 10000);
            });
        })();
    </script>
@endpush