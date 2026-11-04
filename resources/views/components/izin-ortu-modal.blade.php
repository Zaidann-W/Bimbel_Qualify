{{-- Overlay pengajuan izin/sakit anak untuk orang tua. Trigger: [data-ortu-izin-open]. Include: <x-izin-ortu-modal :anak="$anak" :sesi="$sesi_mendatang" /> --}}
@props(['anak' => [], 'sesi' => [], 'tanggalDefault' => null])

@php
    $anakPertama = $anak[0] ?? null;
    $tanggalDefault = $tanggalDefault ?? ($sesi[0]['tanggal'] ?? now()->format('Y-m-d'));
@endphp

{{-- shortcut: submit masih mock UI; sambungkan ke backend pengajuan izin (leave_requests) --}}
<div id="izin-ortu-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="izin-ortu-title" tabindex="-1">
    <div class="fixed inset-0 bg-bark-900/60" data-ortu-izin-close></div>
    <div class="relative flex min-h-full items-start justify-center p-4 sm:p-8">
        <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- Header --}}
            <header class="relative bg-espresso px-6 py-5">
                <button type="button" data-ortu-izin-close aria-label="Tutup formulir izin"
                        class="absolute right-4 top-4 grid h-9 w-9 place-items-center rounded-lg bg-white/10 text-bark-300 transition hover:bg-white/20 hover:text-white">
                    @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-4 w-4'])
                </button>
                <div class="flex items-center gap-3.5 pr-10">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gold/20 text-gold ring-1 ring-gold/30">
                        @include('layouts.partials.icon', ['name' => 'clipboard-form', 'class' => 'h-5 w-5'])
                    </div>
                    <div class="min-w-0">
                        <h3 id="izin-ortu-title" class="text-[17px] font-bold leading-snug text-white">Pengajuan Izin / Sakit</h3>
                        <p class="mt-0.5 truncate text-[12.5px] text-bark-300">
                            @if ($anakPertama)
                                {{ $anakPertama['nama'] }} • {{ $anakPertama['kelas'] }}
                            @else
                                Ananda belum terdaftar pada sistem
                            @endif
                        </p>
                    </div>
                </div>
            </header>

            <form id="izin-ortu-form" data-ortu-izin-form class="space-y-5 px-6 py-6">

                {{-- Jenis ketidakhadiran --}}
                <div>
                    <p class="text-[12.5px] font-bold uppercase tracking-wide text-bark-800">Pilih Jenis Ketidakhadiran</p>
                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2" role="radiogroup" aria-label="Pilih jenis ketidakhadiran">
                        @foreach ([
                            ['value' => 'izin', 'label' => 'Izin', 'sub' => 'Lomba / Acara', 'icon' => 'file-text', 'tile' => 'bg-gold/20 text-bronze'],
                            ['value' => 'sakit', 'label' => 'Sakit', 'sub' => 'Pemulihan Medis', 'icon' => 'heart-pulse', 'tile' => 'bg-blue-100 text-blue-600'],
                        ] as $i => $jenis)
                            <button type="button" role="radio" data-ortu-izin-option data-value="{{ $jenis['value'] }}"
                                    aria-checked="{{ $i === 0 ? 'true' : 'false' }}"
                                    @class([
                                        'flex items-center gap-3 rounded-xl border-2 px-3.5 py-3.5 text-left transition',
                                        'border-gold bg-bark-50' => $i === 0,
                                        'border-bark-200 bg-white hover:border-bark-300' => $i !== 0,
                                    ])>
                                <span @class([
                                        'grid h-5 w-5 shrink-0 place-items-center rounded-full border-2 transition',
                                        'border-gold bg-gold' => $i === 0,
                                        'border-bark-300' => $i !== 0,
                                    ]) data-ortu-izin-radio>
                                    <span @class(['h-2 w-2 rounded-full bg-white', 'block' => $i === 0, 'hidden' => $i !== 0]) data-ortu-izin-dot></span>
                                </span>
                                <span @class(['grid h-10 w-10 shrink-0 place-items-center rounded-lg', $jenis['tile']])>
                                    @include('layouts.partials.icon', ['name' => $jenis['icon'], 'class' => 'h-5 w-5'])
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-[14.5px] font-bold text-bark-800">{{ $jenis['label'] }}</span>
                                    <span class="mt-0.5 block text-[12px] text-bark-400">{{ $jenis['sub'] }}</span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tanggal & sesi --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="izin-ortu-tanggal" class="text-[13.5px] font-semibold text-bark-700">Tanggal Sesi</label>
                        <input type="date" id="izin-ortu-tanggal" name="tanggal" value="{{ $tanggalDefault }}" min="{{ now()->format('Y-m-d') }}"
                               data-ortu-izin-tanggal
                               class="mt-2 w-full rounded-xl border border-bark-100 bg-bark-50 px-4 py-3 text-[14px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                    </div>
                    <div>
                        <label for="izin-ortu-sesi" class="text-[13.5px] font-semibold text-bark-700">Pilih Sesi</label>
                        <div class="relative mt-2">
                            <select id="izin-ortu-sesi" name="class_session_id" data-ortu-izin-sesi
                                    class="w-full appearance-none rounded-xl border border-bark-100 bg-bark-50 px-4 py-3 pr-10 text-[14px] text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                                @forelse ($sesi as $item)
                                    <option value="{{ $item['id'] }}" data-tanggal="{{ $item['tanggal'] }}">{{ $item['label'] }}</option>
                                @empty
                                    <option value="" disabled>Belum ada sesi terjadwal</option>
                                @endforelse
                                <option value="" data-tanggal="" data-ortu-izin-kosong hidden>Tidak ada sesi pada tanggal ini</option>
                            </select>
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-bark-400">
                                @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label for="izin-ortu-keterangan" class="text-[13.5px] font-semibold text-bark-700">
                        Keterangan Singkat <span class="font-normal text-bark-400">(Opsional)</span>
                    </label>
                    <input type="text" id="izin-ortu-keterangan" name="reason" maxlength="200"
                           placeholder="Contoh: Mengikuti olimpiade sekolah / Demam"
                           class="mt-2 w-full rounded-xl border border-bark-100 bg-bark-50 px-4 py-3 text-[14px] text-bark-700 placeholder:text-bark-300 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">
                </div>

                {{-- Konfirmasi WhatsApp --}}
                <label for="izin-ortu-wa" class="flex cursor-pointer items-start gap-3">
                    <span class="relative mt-0.5 inline-flex shrink-0">
                        <input type="checkbox" id="izin-ortu-wa" name="notify_wa" checked
                               class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-bark-200 bg-white checked:border-forest-500 checked:bg-forest-500 focus:outline-none focus:ring-2 focus:ring-forest-200 focus:ring-offset-1">
                        @include('layouts.partials.icon', ['name' => 'check', 'class' => 'pointer-events-none absolute left-1/2 top-1/2 h-3.5 w-3.5 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100'])
                    </span>
                    <span class="text-[13.5px] leading-relaxed text-bark-700">Kirim konfirmasi otomatis via WhatsApp ke nomor terdaftar</span>
                </label>
            </form>

            {{-- Footer --}}
            <footer class="flex items-center justify-end gap-3 border-t border-bark-100 bg-bark-50 px-6 py-4">
                <button type="button" data-ortu-izin-close
                        class="rounded-xl border border-bark-200 bg-white px-5 py-2.5 text-[13.5px] font-bold text-bark-600 transition hover:bg-bark-50">
                    Batal
                </button>
                <button type="submit" form="izin-ortu-form" data-ortu-izin-submit
                        class="inline-flex items-center gap-2 rounded-xl bg-espresso px-5 py-2.5 text-[13.5px] font-bold text-white shadow-sm transition hover:bg-bark-900">
                    @include('layouts.partials.icon', ['name' => 'send', 'class' => 'h-4 w-4 text-gold'])
                    Kirim Izin
                </button>
            </footer>
        </div>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('izin-ortu-modal');
    if (!modal) return;

    const form = modal.querySelector('[data-ortu-izin-form]');
    const tanggal = modal.querySelector('[data-ortu-izin-tanggal]');
    const sesi = modal.querySelector('[data-ortu-izin-sesi]');
    const opsiSesi = sesi ? Array.from(sesi.querySelectorAll('option[data-tanggal]')).filter((o) => !o.hasAttribute('data-ortu-izin-kosong')) : [];
    const opsiKosong = sesi ? sesi.querySelector('[data-ortu-izin-kosong]') : null;

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

    // Sembunyikan pilihan sesi yang tidak sesuai tanggal terpilih.
    const saringSesi = () => {
        if (!sesi || opsiSesi.length === 0) return;
        const dipilih = tanggal ? tanggal.value : '';
        let tersedia = null;

        opsiSesi.forEach((opsi) => {
            const cocok = dipilih === '' || opsi.dataset.tanggal === dipilih;
            opsi.hidden = !cocok;
            opsi.disabled = !cocok;
            if (cocok && tersedia === null) tersedia = opsi;
        });

        const aktif = sesi.selectedOptions[0];
        if (aktif && (aktif.hidden || aktif.disabled)) {
            sesi.value = tersedia ? tersedia.value : '';
        }
        if (opsiKosong) opsiKosong.hidden = tersedia !== null;
    };

    // Tanggal mengikuti sesi yang dipilih.
    const isiTanggalDariSesi = () => {
        const aktif = sesi ? sesi.selectedOptions[0] : null;
        if (tanggal && aktif && aktif.dataset.tanggal) tanggal.value = aktif.dataset.tanggal;
    };

    const pilihJenis = (opsi) => {
        modal.querySelectorAll('[data-ortu-izin-option]').forEach((btn) => {
            const aktif = btn === opsi;
            btn.setAttribute('aria-checked', aktif ? 'true' : 'false');
            btn.classList.toggle('border-gold', aktif);
            btn.classList.toggle('bg-bark-50', aktif);
            btn.classList.toggle('border-bark-200', !aktif);
            btn.classList.toggle('bg-white', !aktif);
            btn.classList.toggle('hover:border-bark-300', !aktif);

            const cincin = btn.querySelector('[data-ortu-izin-radio]');
            cincin?.classList.toggle('border-gold', aktif);
            cincin?.classList.toggle('bg-gold', aktif);
            cincin?.classList.toggle('border-bark-300', !aktif);
            btn.querySelector('[data-ortu-izin-dot]')?.classList.toggle('hidden', !aktif);
        });
    };

    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-ortu-izin-open]')) {
            open(e.target.closest('[data-ortu-izin-open]'));
            return;
        }
        if (e.target.closest('[data-ortu-izin-close]')) {
            close();
            return;
        }
        const opsi = e.target.closest('[data-ortu-izin-option]');
        if (opsi) pilihJenis(opsi);
    });

    tanggal?.addEventListener('change', saringSesi);
    sesi?.addEventListener('change', isiTanggalDariSesi);

    form?.addEventListener('submit', (e) => {
        e.preventDefault();
        close();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
})();
</script>
