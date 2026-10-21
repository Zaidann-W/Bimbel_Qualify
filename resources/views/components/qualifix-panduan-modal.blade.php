{{-- Panduan tanya soal. Trigger: [data-qualifix-panduan-open]. Include: <x-qualifix-panduan-modal /> --}}
<div id="qualifix-panduan-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="qualifix-panduan-title" tabindex="-1">
    <div class="fixed inset-0 bg-bark-900/50" data-panduan-close></div>
    <div class="relative flex min-h-full items-start justify-center p-4 sm:p-8">
        <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- Header --}}
            <header class="relative border-b border-bark-100 bg-bark-50 px-6 py-5">
                <button type="button" data-panduan-close aria-label="Tutup panduan tanya soal"
                        class="absolute right-4 top-4 grid h-8 w-8 place-items-center rounded-lg text-bark-400 transition hover:bg-bark-100 hover:text-bark-700">
                    @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-4 w-4'])
                </button>
                <div class="flex items-center gap-2.5">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ember-100 text-ember-600">
                        @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div class="pr-8">
                        <h3 id="qualifix-panduan-title" class="text-[15.5px] font-bold text-bark-800">Panduan Tanya Soal</h3>
                        <p class="mt-0.5 text-[11.5px] text-bark-400">Tulis pertanyaan yang jelas supaya dibalas lebih cepat</p>
                    </div>
                </div>
            </header>

            <div class="space-y-5 px-6 py-5">

                {{-- Langkah --}}
                <ol class="space-y-3">
                    @foreach ([
                        ['Pilih mapel yang tepat', 'Tentukan mata pelajaran lebih dulu supaya pertanyaan sampai ke tentor pengampu.'],
                        ['Tulis judul / topik soal', 'Sebutkan bab atau materinya, misalnya "Barisan Geometri Bertingkat".'],
                        ['Salin atau ketik isi soal', 'Tulis lengkap: diketahui, yang ditanya, dan pilihan jawabannya bila ada.'],
                        ['Uraikan bagian yang bingung', 'Jelaskan langkah yang sudah kamu coba dan di langkah ke berapa kamu tersendat.'],
                        ['Lampirkan foto soal', 'Unggah foto soal asli bila memungkinkan — lebih cepat dipahami tentor.'],
                    ] as $i => [$judul, $isi])
                        <li class="flex items-start gap-3">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-ember-100 text-[11px] font-bold text-ember-700">{{ $i + 1 }}</span>
                            <div>
                                <p class="text-[13px] font-bold text-bark-800">{{ $judul }}</p>
                                <p class="mt-0.5 text-[12.5px] leading-relaxed text-bark-500">{{ $isi }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>

                {{-- Contoh --}}
                <div class="rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
                    <p class="text-[12px] font-bold text-bark-700">Contoh pertanyaan yang bagus</p>
                    <blockquote class="mt-1.5 border-l-[3px] border-ember-400 pl-3 text-[12.5px] italic leading-relaxed text-bark-600">
                        "Kak, di soal nomor 14 saya sudah pakai rumus suku ke-n deret geometri tapi hasilnya 512, sedangkan kunci 768. Saya bingung beda rasio bertingkatnya itu dihitung dari ruas yang mana?"
                    </blockquote>
                </div>

                {{-- Catatan --}}
                <div class="flex items-start gap-2.5 rounded-xl bg-ember-50 px-4 py-3">
                    @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-500'])
                    <p class="text-[11.5px] leading-relaxed text-bark-500">
                        <span class="font-bold text-bark-700">Jam aktif balasan 08.00–20.00 WIB.</span>
                        Satu pertanyaan fokus ke satu soal akan dijawab lebih cepat daripada menumpuk banyak soal sekaligus.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <footer class="flex items-center justify-end gap-2 border-t border-bark-100 bg-bark-50/60 px-6 py-4">
                <button type="button" data-panduan-close class="rounded-lg px-4 py-2.5 text-[12.5px] font-bold text-bark-400 transition hover:bg-bark-100">
                    Tutup
                </button>
                <button type="button" data-panduan-close data-qualifix-ajukan-open class="inline-flex items-center gap-2 rounded-lg bg-ember-500 px-4 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-ember-600">
                    @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-3.5 w-3.5'])
                    Mulai Ajukan Pertanyaan
                </button>
            </footer>
        </div>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('qualifix-panduan-modal');
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
        if (e.target.closest('[data-qualifix-panduan-open]')) {
            open(e.target.closest('[data-qualifix-panduan-open]'));
            return;
        }
        if (e.target.closest('[data-panduan-close]')) {
            close();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
})();
</script>
