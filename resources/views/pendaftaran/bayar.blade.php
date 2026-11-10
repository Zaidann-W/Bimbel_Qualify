@extends('layouts.main')

@section('title', 'Pembayaran Pendaftaran — Bimbel QUALIFY')

@section('content')
    <section class="relative overflow-hidden bg-cream py-24">
        <div class="relative mx-auto max-w-[1400px] px-6 lg:px-12">
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-[13px] text-muted">
                <a href="{{ url('/') }}" class="hover:text-ink transition-colors">Beranda</a>
                @include('partials.icon', ['name' => 'chevron-right', 'size' => 14, 'class' => 'shrink-0 text-ink/35'])
                <span class="font-semibold text-ink" aria-current="page">Pembayaran Pendaftaran</span>
            </nav>

            <div class="mt-8 max-w-[720px] mx-auto">
                <div class="bg-paper border border-line/60 rounded-[24px] p-8 sm:p-12 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)]">
                    <h2 class="text-[28px] sm:text-[32px] font-extrabold leading-tight text-ink mb-2">
                        Pembayaran Pendaftaran
                    </h2>
                    <p class="text-[15px] leading-[1.7] text-muted mb-6">
                        Nomor registrasi: <span class="font-bold text-gold">{{ $application->registration_number }}</span>
                    </p>

                    @if ($application->payment_status === 'menunggu_verifikasi')
                        <div class="mb-6 p-4 rounded-xl bg-gold/10 border border-gold/30 text-bronze">
                            <span class="font-semibold text-ink">Status:</span>
                            Pembayaran sudah dikirim, menunggu verifikasi admin.
                        </div>
                    @endif

                    <form action="{{ route('pendaftaran.bayar.simpan', $application->registration_number) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-5">
                            <div>
                                <label class="block text-[13px] font-bold uppercase tracking-[0.1em] text-muted mb-2">
                                    Metode Pembayaran
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 p-3 rounded-xl border border-line bg-white hover:bg-cream transition cursor-pointer">
                                        <input type="radio" name="payment_method" value="xendit"
                                               class="h-4 w-4 shrink-0 accent-gold">
                                        <span class="text-[14px] text-ink">Xendit (QR Code / VA / Kartu Kredit)</span>
                                        <span class="ml-auto text-[12px] text-muted">Pembayaran terverifikasi otomatis</span>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 rounded-xl border border-line bg-white hover:bg-cream transition cursor-pointer">
                                        <input type="radio" name="payment_method" value="transfer_bank"
                                               class="h-4 w-4 shrink-0 accent-gold">
                                        <span class="text-[14px] text-ink">Transfer Bank Manual</span>
                                        <span class="ml-auto text-[12px] text-muted">Upload bukti transfer untuk diverifikasi admin</span>
                                    </label>
                                </div>
                            </div>

                            <div id="bukti-transfer-field" class="hidden">
                                <label class="block text-[13px] font-bold uppercase tracking-[0.1em] text-muted mb-2">
                                    Bukti Transfer
                                </label>
                                <input type="file" name="bukti_transfer"
                                       accept=".jpg,.jpeg,.png,.pdf"
                                       class="block w-full text-sm text-ink file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gold file:text-bronze hover:file:bg-gold/80">
                                <p class="mt-1 text-[11px] text-muted">Format: JPG, JPEG, PNG, PDF. Maksimal 5MB.</p>
                            </div>

                            <div class="pt-6 flex items-center gap-3">
                                <a href="{{ route('registrasi.konfirmasi') }}"
                                   class="text-[14px] text-muted hover:text-ink underline">
                                    Kembali
                                </a>
                                <button type="submit"
                                        class="flex-1 bg-gold hover:bg-palegold text-espresso font-bold py-3.5 rounded-xl transition-colors">
                                    Simpan Bukti Pembayaran
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

@push('scripts')
    <script>
        const methodRadios = document.querySelectorAll('input[name="payment_method"]');
        const buktiField = document.getElementById('bukti-transfer-field');

        methodRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.value === 'transfer_bank') {
                    buktiField.classList.remove('hidden');
                } else {
                    buktiField.classList.add('hidden');
                }
            });
        });
    </script>
@endpush
