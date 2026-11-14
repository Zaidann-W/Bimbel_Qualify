@extends('layouts.main')

@section('title', 'Menunggu Pembayaran — Bimbel QUALIFY')

@section('content')
    <section class="relative overflow-hidden bg-cream py-24">
        <div class="relative mx-auto max-w-[1400px] px-6 lg:px-12">
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-[13px] text-muted">
                <a href="{{ url('/') }}" class="hover:text-ink transition-colors">Beranda</a>
                @include('partials.icon', ['name' => 'chevron-right', 'size' => 14, 'class' => 'shrink-0 text-ink/35'])
                <span class="font-semibold text-ink" aria-current="page">Menunggu Pembayaran</span>
            </nav>

            <div class="mt-8 max-w-[800px] mx-auto">
                <div class="bg-paper border border-line/60 rounded-[24px] p-8 sm:p-12 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                    <div class="flex items-start gap-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-sandgold text-bronze">
                            @include('partials.icon', ['name' => 'loader', 'size' => 22, 'class' => 'h-5 w-5 animate-spin'])
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-[26px] sm:text-[30px] font-extrabold leading-tight text-ink mb-1">
                                Menunggu Konfirmasi Pembayaran
                            </h2>
                            <p class="text-[15px] leading-[1.7] text-muted">
                                Nomor registrasi: <span class="font-bold text-gold">{{ $application->registration_number }}</span>.
                                Halaman ini otomatis berubah menjadi formulir pembuatan akun begitu pembayaran terkonfirmasi.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl border border-line bg-white p-4">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-[12.5px] font-semibold text-muted">Status pembayaran</span>
                            <span id="status-pembayaran"
                                  class="rounded-lg bg-sandgold px-2.5 py-1.5 text-[11px] font-bold leading-none text-bronze">
                                {{ $application->payment_status === 'menunggu_verifikasi' ? 'Menunggu Verifikasi' : 'Belum Dibayar' }}
                            </span>
                        </div>

                        @if ($application->xendit_invoice_url)
                            <div class="mt-4 border-t border-line pt-4">
                                <p class="text-[12px] font-bold uppercase tracking-[0.1em] text-muted">Link Pembayaran</p>
                                <a href="{{ $application->xendit_invoice_url }}" target="_blank" rel="noopener noreferrer"
                                   class="mt-2 block break-all rounded-lg bg-sand px-3 py-2 text-[12px] font-mono text-bronze hover:bg-palegold">
                                    {{ $application->xendit_invoice_url }}
                                </a>
                            </div>
                        @endif

                        <p class="mt-4 text-[13px] leading-[1.7] text-muted">
                            Pembayaran diproses melalui Xendit. Konfirmasi dari bank kadang memerlukan waktu singkat,
                            sehingga status belum langsung berubah. Halaman ini otomatis diperbarui, jadi tidak perlu me-refresh manual.
                        </p>

                        <button type="button" id="cek-status"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl border border-line bg-white px-4 py-2.5 text-[13px] font-bold text-espresso transition hover:bg-sand">
                            @include('partials.icon', ['name' => 'refresh', 'size' => 16, 'class' => 'h-4 w-4'])
                            Cek Status Sekarang
                        </button>
                    </div>

                    <p class="mt-6 text-[13px] text-muted">
                        Butuh bantuan? Hubungi admin Bimbel QUALIFY melalui WhatsApp.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var urlStatus = @json(route('pendaftaran.status', $application->registration_number));
            var urlBerhasil = @json(route('pendaftaran.berhasil', $application->registration_number));
            var tombol = document.getElementById('cek-status');
            var sedangMemuat = false;

            function periksaStatus() {
                if (sedangMemuat) {
                    return;
                }

                sedangMemuat = true;

                fetch(urlStatus, { headers: { 'Accept': 'application/json' } })
                    .then(function (respons) {
                        if (!respons.ok) {
                            throw new Error('Gagal memuat status.');
                        }

                        return respons.json();
                    })
                    .then(function (data) {
                        if (data.accounts_created) {
                            window.location.href = urlBerhasil;
                            return;
                        }

                        if (data.lunas) {
                            window.location.reload();
                        }
                    })
                    .catch(function () {
                        // Gagal memuat status tidak consequential: pengguna dapat menekan tombol cek ulang.
                    })
                    .finally(function () {
                        sedangMemuat = false;
                    });
            }

            if (tombol) {
                tombol.addEventListener('click', periksaStatus);
            }

            window.setInterval(periksaStatus, 4000);
        })();
    </script>
@endpush
