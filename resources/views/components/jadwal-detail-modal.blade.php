{{-- Overlay detail sesi jadwal. Include sekali per halaman: <x-jadwal-detail-modal /> --}}
<div id="jadwal-detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="jadwal-detail-title" tabindex="-1">
    <div class="fixed inset-0 bg-bark-900/40" data-modal-close></div>
    <div class="relative flex min-h-full items-start justify-center p-4 sm:p-6">
        <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- Header --}}
            <header class="relative border-b border-bark-100 bg-bark-50 px-6 py-5">
                <button type="button" data-modal-close
                        class="absolute right-4 top-4 grid h-8 w-8 place-items-center rounded-lg text-bark-400 transition hover:bg-bark-100 hover:text-bark-700"
                        aria-label="Tutup detail sesi">
                    @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-4 w-4'])
                </button>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-ember-600">Detail Sesi Terpilih</p>
                <h3 id="jadwal-detail-title" class="mt-1.5 pr-8 text-[18px] font-bold text-bark-800" data-field="title"></h3>
                <p class="mt-1 text-[12.5px] text-bark-400" data-field="subtitle"></p>
            </header>

            <div class="space-y-4 px-6 py-5">

                {{-- Ruang + fasilitas --}}
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="flex items-center gap-2 text-[13.5px] font-bold text-bark-800">
                        @include('layouts.partials.icon', ['name' => 'map-pin', 'class' => 'h-4 w-4 text-ember-500'])
                        <span data-field="room"></span>
                    </p>
                    {{-- shortcut: fasilitas ruang belum ada di data jadwal, masih statis semua rombel --}}
                    <div class="flex flex-wrap items-center gap-3 text-[11.5px] font-medium text-bark-500" data-field="facilities">
                        <span class="flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'monitor', 'class' => 'h-3.5 w-3.5 text-emerald-500'])
                            Smart Screen
                        </span>
                        <span class="flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'wind', 'class' => 'h-3.5 w-3.5 text-sky-500'])
                            AC Normal
                        </span>
                        <span class="flex items-center gap-1.5">
                            @include('layouts.partials.icon', ['name' => 'users', 'class' => 'h-3.5 w-3.5 text-bark-400'])
                            Kapasitas 15
                        </span>
                    </div>
                </div>

                {{-- Siswa rombel --}}
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="flex items-center gap-2 text-[13.5px] font-bold text-bark-800">
                        Daftar Siswa Rombel Ini
                        <span class="rounded-full bg-bark-100 px-2 py-0.5 text-[10px] font-bold text-bark-500" data-field="siswa"></span>
                    </p>
                    <p class="text-[11.5px] text-bark-400">Update: 15:25 WIB</p>
                </div>

                {{-- Status absensi --}}
                <div class="flex items-start gap-2.5 rounded-xl border border-bark-100 bg-bark-50 px-4 py-3">
                    @include('layouts.partials.icon', ['name' => 'clipboard-check', 'class' => 'mt-0.5 h-4 w-4 text-ember-500'])
                    <p class="text-[12.5px] text-bark-600">
                        <span class="font-semibold text-bark-700">Status Absensi:</span>
                        Belum Disimpan oleh Tentor (Pending Closing)
                    </p>
                </div>

                {{-- Aksi utama --}}
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <a href="#"
                       class="flex items-center justify-center gap-2 rounded-xl bg-ember-500 px-4 py-3 text-[13px] font-bold text-white transition hover:bg-ember-600">
                        @include('layouts.partials.icon', ['name' => 'log-in', 'class' => 'h-4 w-4'])
                        Lanjut ke Form Absensi Siswa
                    </a>
                    <button type="button"
                            class="flex items-center justify-center gap-2 rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] font-bold text-bark-600 transition hover:bg-bark-50">
                        @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4'])
                        Unduh Berkas Absensi Sesi (PDF)
                    </button>
                </div>

                {{-- Kebijakan akses --}}
                <div class="flex items-start gap-2.5 rounded-xl bg-ember-50 px-4 py-3">
                    @include('layouts.partials.icon', ['name' => 'info', 'class' => 'mt-0.5 h-4 w-4 text-ember-500'])
                    <p class="text-[11.5px] leading-relaxed text-bark-500">
                        <span class="font-semibold text-bark-700">Kebijakan Hak Akses:</span>
                        Tentor hanya memiliki hak akses pantau dan absensi. Perubahan jadwal atau mutasi siswa dilakukan oleh Super Admin Front Office.
                    </p>
                </div>
            </div>

            {{-- Footer bantuan --}}
            <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-bark-100 bg-bark-50/60 px-6 py-4">
                <div class="flex items-start gap-2.5">
                    @include('layouts.partials.icon', ['name' => 'help-circle', 'class' => 'mt-0.5 h-4 w-4 text-ember-500'])
                    <div>
                        <p class="text-[13px] font-bold text-bark-800">Ada Kendala Ruangan atau Jadwal?</p>
                        <p class="text-[11.5px] text-bark-400">Hubungi Koordinator Akademik &amp; FO</p>
                    </div>
                </div>
                <a href="#" class="rounded-lg border border-bark-200 bg-white px-3.5 py-2.5 text-[12px] font-bold text-bark-600 transition hover:bg-bark-50">
                    Hubungi Admin FO
                </a>
            </footer>
        </div>
    </div>
</div>

{{-- shortcut: status absensi & waktu update masih statis, ganti dari backend kalau data sesi sudah ada --}}
<script>
(() => {
    const modal = document.getElementById('jadwal-detail-modal');
    if (!modal) return;

    let lastRow = null;

    const fill = (row) => {
        modal.querySelector('[data-field=title]').textContent = row.dataset.title || '';
        modal.querySelector('[data-field=subtitle]').textContent = row.dataset.subtitle || '';
        modal.querySelector('[data-field=room]').textContent = row.dataset.room || '';
        modal.querySelector('[data-field=siswa]').textContent = row.dataset.siswa || '';
        const facilities = row.querySelector('[data-facilities]');
        if (facilities) modal.querySelector('[data-field=facilities]').innerHTML = facilities.innerHTML;
    };

    const open = (row) => {
        fill(row);
        lastRow = row;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        modal.focus();
    };

    const close = () => {
        if (modal.classList.contains('hidden')) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        if (lastRow) lastRow.focus();
    };

    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-modal-close]')) {
            close();
            return;
        }
        // tombol/link di dalam baris (Buka Kelas, Absensi) jangan buka modal
        if (e.target.closest('button, a')) return;
        const row = e.target.closest('.jadwal-row');
        if (row) open(row);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            close();
            return;
        }
        if ((e.key === 'Enter' || e.key === ' ') && e.target.classList?.contains('jadwal-row')) {
            e.preventDefault();
            open(e.target);
        }
    });
})();
</script>
