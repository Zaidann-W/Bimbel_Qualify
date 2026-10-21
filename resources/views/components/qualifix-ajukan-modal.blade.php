{{-- Formulir ajukan soal / PR baru. Trigger: [data-qualifix-ajukan-open]. Include: <x-qualifix-ajukan-modal /> --}}
{{-- shortcut: pilih mapel, unggah foto, dan kirim pertanyaan masih mock UI; sambungkan ke backend Q&A --}}
<div id="qualifix-ajukan-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="qualifix-ajukan-title" tabindex="-1">
    <div class="fixed inset-0 bg-bark-900/50" data-ajukan-close></div>
    <div class="relative flex min-h-full items-start justify-center p-4 sm:p-8">
        <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- Header --}}
            <header class="relative border-b border-bark-100 bg-bark-50 px-6 py-5">
                <button type="button" data-ajukan-close aria-label="Tutup formulir pertanyaan"
                        class="absolute right-4 top-4 grid h-8 w-8 place-items-center rounded-lg text-bark-400 transition hover:bg-bark-100 hover:text-bark-700">
                    @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-4 w-4'])
                </button>
                <div class="flex items-center gap-2.5">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ember-100 text-ember-600">
                        @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div class="pr-8">
                        <h3 id="qualifix-ajukan-title" class="text-[15.5px] font-bold text-bark-800">Ajukan Pertanyaan / Tugas PR Baru</h3>
                        <p class="mt-0.5 text-[11.5px] text-bark-400">Langsung ke Master Tutor Qualify sesuai mapel</p>
                    </div>
                </div>
            </header>

            <div class="space-y-5 px-6 py-5">

                {{-- Pilih mapel --}}
                <fieldset>
                    <legend class="text-[12.5px] font-bold text-bark-700">Pilih Mata Pelajaran <span class="text-ember-500">*</span></legend>
                    <div class="mt-2 grid grid-cols-2 gap-2" data-ajukan-options>
                        @foreach (['Matematika Saintek', 'Fisika Saintek', 'Kimia Saintek', 'Biologi Saintek', 'Bahasa Indonesia', 'Bahasa Inggris'] as $i => $m)
                            <button type="button" role="radio" aria-checked="{{ $i === 0 ? 'true' : 'false' }}" data-ajukan-option
                                    @class([
                                        'flex items-center gap-2 rounded-lg border px-3 py-2 text-[12px] font-semibold transition',
                                        'border-ember-300 bg-ember-50 text-ember-700' => $i === 0,
                                        'border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => $i !== 0,
                                    ])>{{ $m }}</button>
                        @endforeach
                    </div>
                </fieldset>

                {{-- Judul soal --}}
                <div>
                    <label for="ajukan-judul" class="text-[12.5px] font-bold text-bark-700">Judul / Topik Soal</label>
                    <input id="ajukan-judul" type="text" placeholder="Contoh: Barisan Geometri Bertingkat"
                           class="mt-2 w-full rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] text-bark-700 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                </div>

                {{-- Isi soal --}}
                <div>
                    <label for="ajukan-isi" class="text-[12.5px] font-bold text-bark-700">Isi Soal / Pertanyaan <span class="text-ember-500">*</span></label>
                    <textarea id="ajukan-isi" rows="5" placeholder="Tulis soal atau pertanyaanmu di sini…"
                              class="mt-2 w-full rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] leading-relaxed text-bark-700 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100"></textarea>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label for="ajukan-keterangan" class="text-[12.5px] font-bold text-bark-700">Keterangan Tambahan</label>
                    <textarea id="ajukan-keterangan" rows="3" placeholder="Bagian mana yang sudah kamu coba, atau langkah yang masih membingungkan…"
                              class="mt-2 w-full rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] leading-relaxed text-bark-700 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100"></textarea>
                </div>

                {{-- Unggah foto --}}
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-[12.5px] font-bold text-bark-700">Unggah Foto Soal <span class="font-medium text-bark-400">(Opsional)</span></p>
                        <span class="text-[11px] text-bark-400">Format JPG, PNG, PDF (Maksimal 5 MB)</span>
                    </div>
                    <div class="mt-2 rounded-xl border-2 border-dashed border-bark-200 px-4 py-4 text-center">
                        @include('layouts.partials.icon', ['name' => 'image', 'class' => 'mx-auto h-6 w-6 text-bark-300'])
                        <p class="mt-2 text-[12px] text-bark-400">Tarik &amp; letakkan foto soal di sini atau klik dari berkas dan perangkat</p>
                        <button type="button" class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                            @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-3.5 w-3.5 rotate-180'])
                            Pilih Foto
                        </button>
                    </div>
                </div>

                {{-- Pemberitahuan --}}
                <div class="flex items-start gap-2.5 rounded-xl bg-ember-50 px-4 py-3">
                    @include('layouts.partials.icon', ['name' => 'info', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
                    <p class="text-[11.5px] leading-relaxed text-bark-500">
                        <span class="font-bold text-bark-700">Balasan rata-rata &lt; 1 jam</span> pada jam aktif belajar (08.00–20.00 WIB). Pertanyaanmu akan muncul di tab "Menunggu" sampai tentor menjawab.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <footer class="flex items-center justify-end gap-2 border-t border-bark-100 bg-bark-50/60 px-6 py-4">
                <button type="button" data-ajukan-close class="rounded-lg px-4 py-2.5 text-[12.5px] font-bold text-bark-400 transition hover:bg-bark-100">
                    Batal
                </button>
                <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-ember-500 px-4 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-ember-600">
                    @include('layouts.partials.icon', ['name' => 'send', 'class' => 'h-3.5 w-3.5'])
                    Kirim Pertanyaan
                </button>
            </footer>
        </div>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('qualifix-ajukan-modal');
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
        if (e.target.closest('[data-qualifix-ajukan-open]')) {
            open(e.target.closest('[data-qualifix-ajukan-open]'));
            return;
        }
        if (e.target.closest('[data-ajukan-close]')) {
            close();
            return;
        }
        const option = e.target.closest('[data-ajukan-option]');
        if (option) {
            modal.querySelectorAll('[data-ajukan-option]').forEach((btn) => {
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
