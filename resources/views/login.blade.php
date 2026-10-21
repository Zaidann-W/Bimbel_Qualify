@extends('layouts.main')

@section('title', 'Masuk ke Akun — Bimbel QUALIFY')
@section('meta_description', 'Masuk ke akun Bimbel QUALIFY dengan email, NISN, atau nomor HP untuk melanjutkan ke kelas digital, jadwal bimbingan, dan informasi belajar Anda.')

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

                <form id="form-masuk" action="{{ route('login.simpan') }}" method="POST" novalidate class="mt-7">
                    @csrf

                    <div data-field>
                        <label for="identitas" class="block text-[13.5px] font-bold text-ink">
                            Email / NISN / No. HP
                        </label>
                        <div
                            class="mt-2 flex items-stretch rounded-xl border border-line bg-shell transition-colors focus-within:border-gold focus-within:bg-paper focus-within:ring-4 focus-within:ring-gold/20"
                            data-control-surface
                            data-border-default="border-line"
                        >
                            <span class="inline-flex items-center pl-4 text-ink/45" aria-hidden="true">
                                @include('partials.icon', ['name' => 'user', 'size' => 18])
                            </span>
                            <input
                                type="text"
                                id="identitas"
                                name="identitas"
                                data-label="Email / NISN / No. HP"
                                required
                                autocomplete="username"
                                placeholder="1234567890"
                                class="w-full min-w-0 bg-transparent px-3.5 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none"
                            >
                        </div>
                        <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="identitas" role="alert"></p>
                    </div>

                    <div class="mt-5" data-field>
                        <div class="flex items-baseline justify-between gap-4">
                            <label for="kata_sandi" class="block text-[13.5px] font-bold text-ink">
                                Kata Sandi
                            </label>
                            <a href="#" class="text-[13px] font-bold text-bronze hover:text-[#7d5d12] transition-colors">
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
                                id="kata_sandi"
                                name="kata_sandi"
                                data-label="Kata sandi"
                                required
                                autocomplete="current-password"
                                placeholder="qualify2025!"
                                class="w-full min-w-0 bg-transparent px-3.5 py-3.5 text-[15px] text-ink placeholder:text-muted/55 focus:outline-none"
                            >
                            <button
                                type="button"
                                id="toggle-kata-sandi"
                                class="inline-flex shrink-0 items-center px-4 text-ink/45 transition-colors hover:text-ink"
                                aria-label="Tampilkan kata sandi"
                                aria-controls="kata_sandi"
                            >
                                <span id="ikon-mata">
                                    @include('partials.icon', ['name' => 'eye', 'size' => 18])
                                </span>
                                <span id="ikon-mata-tutup" class="hidden">
                                    @include('partials.icon', ['name' => 'eye-off', 'size' => 18])
                                </span>
                            </button>
                        </div>
                        <p class="mt-1.5 hidden text-[12.5px] font-medium text-ember-700" data-error-for="kata_sandi" role="alert"></p>
                    </div>

                    <div class="mt-4">
                        <label class="-m-1 flex cursor-pointer items-center gap-3 rounded-xl p-1">
                            <input type="checkbox" name="ingat" class="peer sr-only">
                            <span class="grid h-5 w-5 shrink-0 place-items-center rounded-md border-2 border-line bg-shell text-transparent transition-colors peer-checked:border-espresso peer-checked:bg-espresso peer-checked:text-gold peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true">
                                @include('partials.icon', ['name' => 'check', 'size' => 12, 'stroke' => 3.2])
                            </span>
                            <span class="text-[14px] text-ink">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="mt-5 flex w-full items-center justify-center gap-2.5 rounded-xl bg-gold px-6 py-[17px] text-[16px] font-bold text-espresso transition-colors hover:bg-[#ecab20]"
                    >
                        Masuk ke Akun
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
                    surface.classList.toggle(surface.dataset.borderDefault, ! adaError);
                    surface.classList.toggle('border-ember-400', adaError);
                }
            }

            function tandaiError(kontrol, adaError) {
                var kotak = kotakError(kontrol);

                if (kotak) {
                    kotak.textContent = adaError ? pesanError(kontrol) : '';
                    kotak.classList.toggle('hidden', ! adaError);
                }

                gayaError(permukaan(kontrol), adaError);
                kontrol.setAttribute('aria-invalid', adaError ? 'true' : 'false');
            }

            function gulirKe(elemen) {
                elemen.scrollIntoView({ behavior: gerakHemat ? 'auto' : 'smooth', block: 'center' });
            }

            var wajib = Array.prototype.slice.call(form.querySelectorAll('[required]'));

            wajib.forEach(function (kontrol) {
                kontrol.addEventListener('input', function () {
                    if (kontrol.getAttribute('aria-invalid') === 'true') {
                        tandaiError(kontrol, ! valid(kontrol));
                    }
                });
            });

            // Tampilkan / sembunyikan kata sandi
            var tombolMata = document.getElementById('toggle-kata-sandi');
            var inputSandi = document.getElementById('kata_sandi');

            if (tombolMata && inputSandi) {
                tombolMata.addEventListener('click', function () {
                    var sedangTampil = inputSandi.type === 'password';

                    inputSandi.type = sedangTampil ? 'text' : 'password';
                    tombolMata.setAttribute('aria-label', sedangTampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                    document.getElementById('ikon-mata').classList.toggle('hidden', sedangTampil);
                    document.getElementById('ikon-mata-tutup').classList.toggle('hidden', ! sedangTampil);
                    inputSandi.focus();
                });
            }

            form.addEventListener('submit', function (event) {
                var pertamaInvalid = null;

                wajib.forEach(function (kontrol) {
                    var oke = valid(kontrol);

                    tandaiError(kontrol, ! oke);

                    if (! oke && ! pertamaInvalid) {
                        pertamaInvalid = kontrol;
                    }
                });

                if (pertamaInvalid) {
                    event.preventDefault();
                    gulirKe(permukaan(pertamaInvalid) || pertamaInvalid);
                    pertamaInvalid.focus({ preventScroll: true });
                }
            });
        })();
    </script>
@endpush
