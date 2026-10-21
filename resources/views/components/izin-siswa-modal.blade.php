{{-- Formulir pengajuan izin siswa. Trigger: [data-izin-open]. Include: <x-izin-siswa-modal /> --}}
{{-- shortcut: jenis izin, unggah berkas, dan kirim izin masih mock UI; sambungkan ke backend pengajuan izin --}}
<div id="izin-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="izin-modal-title" tabindex="-1">
    <div class="fixed inset-0 bg-bark-900/50" data-izin-close></div>
    <div class="relative flex min-h-full items-start justify-center p-4 sm:p-8">
        <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- Header --}}
            <header class="relative border-b border-bark-100 bg-bark-50 px-6 py-5">
                <button type="button" data-izin-close aria-label="Tutup formulir izin"
                        class="absolute right-4 top-4 grid h-8 w-8 place-items-center rounded-lg text-bark-400 transition hover:bg-bark-100 hover:text-bark-700">
                    @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-4 w-4'])
                </button>
                <div class="flex items-center gap-2.5">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ember-100 text-ember-600">
                        @include('layouts.partials.icon', ['name' => 'clipboard-form', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div class="pr-8">
                        <h3 id="izin-modal-title" class="text-[15.5px] font-bold text-bark-800">Pengajuan Izin / Berhalangan Hadir</h3>
                        <p class="mt-0.5 text-[11.5px] text-bark-400">Sesi: Kelas 12 IPA - Super Intensif UTBK SNBT (Senin, 10 Maret 2025)</p>
                    </div>
                </div>
            </header>

            <div class="space-y-5 px-6 py-5">

                {{-- Jenis izin --}}
                <div>
                    <p class="text-[12.5px] font-bold text-bark-700">Jenis Izin / Keterangan <span class="text-ember-500">*</span></p>
                    <div class="mt-2 flex flex-wrap gap-2" data-izin-options>
                        @foreach (['Sakit', 'Dispensasi', 'Urusan Keluarga'] as $i => $jenis)
                            <button type="button" role="radio" aria-checked="{{ $i === 0 ? 'true' : 'false' }}" data-izin-option
                                    @class([
                                        'rounded-full border px-4 py-2 text-[12.5px] font-semibold transition',
                                        'border-ember-300 bg-ember-50 text-ember-700' => $i === 0,
                                        'border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => $i !== 0,
                                    ])>{{ $jenis }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Tanggal & sesi --}}
                <div>
                    <p class="text-[12.5px] font-bold text-bark-700">Tanggal &amp; Sesi Belajar</p>
                    <div class="mt-2 flex items-center gap-2.5 rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
                        @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4 shrink-0 text-ember-500'])
                        <p class="text-[12.5px] text-bark-600">Senin, 10 Maret 2025 • 15.30 – 17.00 WIB (Kalkulus &amp; Aplikasi Integral)</p>
                    </div>
                </div>

                {{-- Alasan --}}
                <div>
                    <label for="izin-alasan" class="text-[12.5px] font-bold text-bark-700">Alasan Lengkap <span class="text-ember-500">*</span></label>
                    <textarea id="izin-alasan" rows="4"
                              class="mt-2 w-full rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] leading-relaxed text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">Sedang demam tinggi dan flu sejak tadi pagi, dokter menyarankan istirahat penuh dan tidak mengikuti sesi tatap muka hari ini.</textarea>
                </div>

                {{-- Bukti pendukung --}}
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-[12.5px] font-bold text-bark-700">Unggah Bukti Pendukung <span class="font-medium text-bark-400">(Surat Dokter / Dispensasi Sekolah) — Opsional</span></p>
                        <span class="text-[11px] text-bark-400">Format PDF, JPG, PNG (Maksimal 5 MB)</span>
                    </div>

                    <div class="mt-2 rounded-xl border-2 border-dashed border-bark-200 px-4 py-4 text-center">
                        <p class="text-[12.5px] font-semibold text-bark-500">Pilih Berkas atau Tarik ke Sini</p>
                    </div>

                    <div class="mt-2 flex items-center gap-3 rounded-xl border border-bark-100 bg-bark-50 px-4 py-2.5">
                        <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-rose-500 ring-1 ring-bark-100">
                            @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-4 w-4'])
                        </div>
                        <p class="min-w-0 flex-1 truncate text-[12.5px] font-semibold text-bark-700">Surat_keterangan_dokter_aisyah.pdf</p>
                        <span class="text-[11px] text-bark-400">1.2 MB</span>
                        <button type="button" aria-label="Hapus berkas" class="grid h-6 w-6 shrink-0 place-items-center rounded-md text-bark-400 transition hover:bg-bark-100 hover:text-rose-500">
                            @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-3.5 w-3.5'])
                        </button>
                    </div>
                </div>

                {{-- Pemberitahuan --}}
                <div class="flex items-start gap-2.5 rounded-xl bg-ember-50 px-4 py-3">
                    @include('layouts.partials.icon', ['name' => 'info', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
                    <p class="text-[11.5px] leading-relaxed text-bark-500">
                        <span class="font-bold text-bark-700">Pemberitahuan Otomatis</span><br>
                        Pengajuan akan dikirimkan ke Tentor Pengampu dan WhatsApp Orang Tua (terdaftar). Setelah disetujui, pembahasan akan otomatis aktif di tab "Materi Pembelajaran" setelah sesi selesai.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <footer class="flex items-center justify-end gap-2 border-t border-bark-100 bg-bark-50/60 px-6 py-4">
                <button type="button" data-izin-close class="rounded-lg px-4 py-2.5 text-[12.5px] font-bold text-bark-400 transition hover:bg-bark-100">
                    Batal
                </button>
                <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-ember-500 px-4 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-ember-600">
                    @include('layouts.partials.icon', ['name' => 'send', 'class' => 'h-3.5 w-3.5'])
                    Kirim Formulir Izin
                </button>
            </footer>
        </div>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('izin-modal');
    if (!modal) return;

    let lastTrigger = null;

    const open = (trigger) => {
        lastTrigger = trigger;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        modal.focus();
    };

    const close = () => {
        if (modal.classList.contains('hidden')) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        if (lastTrigger) lastTrigger.focus();
    };

    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-izin-open]')) {
            open(e.target.closest('[data-izin-open]'));
            return;
        }
        if (e.target.closest('[data-izin-close]')) {
            close();
            return;
        }
        const option = e.target.closest('[data-izin-option]');
        if (option) {
            modal.querySelectorAll('[data-izin-option]').forEach((btn) => {
                const selected = btn === option;
                btn.setAttribute('aria-checked', selected ? 'true' : 'false');
                btn.classList.toggle('border-ember-300', selected);
                btn.classList.toggle('bg-ember-50', selected);
                btn.classList.toggle('text-ember-700', selected);
                btn.classList.toggle('border-bark-200', !selected);
                btn.classList.toggle('bg-white', !selected);
                btn.classList.toggle('text-bark-500', !selected);
            });
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
})();
</script>
