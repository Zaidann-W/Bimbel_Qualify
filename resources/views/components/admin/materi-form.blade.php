@props(['materi' => null, 'subjects', 'kelas'])

@php
    $targetTerpilih = array_map('intval', old('target', $materi?->classGroups->pluck('id')->all() ?? []));
    $mapelTerpilih = (int) old('mapel', $materi?->subject_id ?? 0);
    $aksesTerpilih = (bool) old('akses', $materi?->allow_download ?? true);
@endphp

<form id="materi-form"
      action="{{ $materi ? route('admin.manajemenMateri.update', $materi) : route('admin.manajemenMateri.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="mt-5">
    @csrf
    @if($materi)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-12">

        {{-- Berkas modul --}}
        <div class="md:col-span-6">
            <label for="materi-berkas" class="block text-[12px] font-bold leading-snug text-ink">
                File Modul <span class="font-medium text-muted">(PDF / PPTX / DOCX)</span>
            </label>
            <label for="materi-berkas"
                   class="mt-2 flex cursor-pointer items-center gap-2.5 rounded-xl border border-bark-200 bg-shell px-4 py-3 transition hover:bg-sand focus-within:border-gold focus-within:bg-white focus-within:ring-2 focus-within:ring-gold/30">
                @include('layouts.partials.icon', ['name' => 'upload', 'class' => 'h-4 w-4 shrink-0 text-bronze'])
                <span id="materi-berkas-nama" class="min-w-0 flex-1 truncate text-[13px] leading-snug text-muted">
                    {{ $materi?->file?->original_name ?? 'Belum ada berkas dipilih — klik untuk memilih dari komputer' }}
                </span>
            </label>
            <input type="file" id="materi-berkas" name="berkas" accept=".pdf,.pptx,.docx"
                   class="sr-only" aria-describedby="materi-berkas-error">
            <p id="materi-berkas-error" class="mt-1.5 hidden text-[11.5px] font-semibold leading-snug text-ember-700"></p>
            @if($materi?->file)
                <p class="mt-1.5 text-[11.5px] leading-snug text-muted">
                    Berkas saat ini: <span class="font-semibold text-ink">{{ $materi->file->original_name }}</span>.
                    Biarkan kosong bila tidak ingin menggantinya.
                </p>
            @endif
        </div>

        {{-- Judul materi --}}
        <div class="md:col-span-3">
            <label for="materi-judul" class="block text-[12px] font-bold leading-snug text-ink">Judul Materi</label>
            <input type="text" id="materi-judul" name="judul" aria-describedby="materi-judul-error"
                   value="{{ old('judul', $materi?->title) }}"
                   placeholder="Contoh: Bank Soal Penalaran Matematika"
                   class="mt-2 w-full rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30 @error('judul') border-ember-700 @enderror">
            @error('judul')
                <p class="mt-1.5 text-[11.5px] font-semibold leading-snug text-ember-700">{{ $message }}</p>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="md:col-span-3">
            <label for="materi-deskripsi" class="block text-[12px] font-bold leading-snug text-ink">Deskripsi</label>
            <textarea id="materi-deskripsi" name="deskripsi" rows="2"
                      placeholder="Singkat ceritakan isi modul ini..."
                      class="mt-2 w-full rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">{{ old('deskripsi', $materi?->description) }}</textarea>
        </div>

        {{-- Mata pelajaran --}}
        <div class="md:col-span-3">
            <label for="materi-mapel" class="block text-[12px] font-bold leading-snug text-ink">Mata Pelajaran</label>
            <div class="relative mt-2">
                <select id="materi-mapel" name="mapel"
                        class="w-full appearance-none rounded-xl border border-bark-200 bg-white px-4 py-3 pr-10 text-[13px] text-ink transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30 @error('mapel') border-ember-700 @enderror">
                    <option value="">Pilih mata pelajaran</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected($mapelTerpilih === (int) $subject->id)>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-0 grid w-10 place-items-center text-bark-400">
                    @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                </span>
            </div>
            @error('mapel')
                <p class="mt-1.5 text-[11.5px] font-semibold leading-snug text-ember-700">{{ $message }}</p>
            @enderror
            @if ($subjects->isEmpty())
                <p class="mt-1.5 text-[11px] leading-snug text-muted">
                    Daftar mata pelajaran belum tersedia.
                </p>
            @endif
        </div>

        {{-- Target kelas --}}
        <div class="md:col-span-9">
            <label for="materi-target-trigger" class="block text-[12px] font-bold leading-snug text-ink">Target Kelas</label>
            <div class="relative z-40 mt-2" id="materi-target-picker">
                <button type="button" id="materi-target-trigger"
                        class="relative flex w-full items-center gap-2 rounded-xl border border-bark-200 bg-white px-4 py-3 pr-10 text-left text-[13px] text-ink transition hover:border-bark-300 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30 @error('target') border-ember-700 @enderror"
                        aria-haspopup="listbox" aria-expanded="false" aria-controls="materi-target-panel">
                    <span id="materi-target-label" class="truncate">Pilih kelas</span>
                    <span class="pointer-events-none absolute inset-y-0 right-0 grid w-10 place-items-center text-bark-400">
                        @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4'])
                    </span>
                </button>

                <div id="materi-target-panel"
                     class="absolute left-0 right-0 z-50 mt-1 hidden overflow-hidden rounded-xl border border-line bg-white shadow-lg">
                    <div class="border-b border-line bg-shell px-2.5 py-1.5">
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 grid w-8 place-items-center text-bark-300">
                                @include('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                            </span>
                            <input type="search" id="materi-target-search" placeholder="Cari kode atau jenjang kelas..."
                                   aria-label="Cari kelas sasaran"
                                   class="w-full rounded-lg border border-line bg-white py-1.5 pl-8 pr-3 text-[12px] text-ink placeholder:text-bark-300 transition focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/30">
                        </div>
                    </div>

                    <div id="materi-target-list" class="max-h-32 overflow-y-auto p-1.5">
                        @forelse ($kelas as $kelasItem)
                            <label data-target-option
                                   data-kode="{{ $kelasItem->code }}"
                                   data-teks="{{ strtolower(trim($kelasItem->code.' '.$kelasItem->education_level.' '.($kelasItem->grade ?? ''))) }}"
                                   class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-2 transition hover:bg-shell">
                                <input type="checkbox" name="target[]" value="{{ $kelasItem->id }}"
                                       @checked(in_array((int) $kelasItem->id, $targetTerpilih, true))
                                       class="rounded border-bark-300 text-espresso focus:ring-gold">
                                <span class="text-[13px] font-bold leading-snug text-ink">{{ $kelasItem->code }}</span>
                                <span class="text-[12px] leading-snug text-muted">{{ $kelasItem->education_level }}</span>
                            </label>
                        @empty
                            <p class="px-2 py-4 text-[12.5px] leading-snug text-muted">Belum ada kelas aktif.</p>
                        @endforelse
                    </div>

                    <div class="flex items-center justify-between gap-2 border-t border-line px-2.5 py-1.5">
                        <p class="text-[11.5px] leading-snug text-muted">
                            <span id="materi-target-count" class="font-bold text-ink">0</span> kelas dipilih
                        </p>
                        <button type="button" id="materi-target-toggle-all"
                                class="text-[11.5px] font-semibold text-bronze transition hover:underline">
                            Pilih Semua
                        </button>
                    </div>
                </div>
            </div>
            <p class="mt-1 text-[11px] leading-none text-muted">Pilih satu atau beberapa kelas sasaran.</p>
            @error('target')
                <p class="mt-1.5 text-[11.5px] font-semibold leading-snug text-ember-700">{{ $message }}</p>
            @enderror
            @if ($kelas->isEmpty())
                <p class="mt-1.5 text-[11px] leading-snug text-muted">
                    Tambahkan rombel terlebih dahulu pada halaman
                    <a href="{{ route('admin.dataSiswa') }}" class="font-semibold text-bronze hover:underline">Data Siswa &amp; Kelas</a>.
                </p>
            @endif
        </div>

        {{-- Hak akses --}}
        <div class="md:col-span-3">
            <label for="materi-akses" class="block text-[12px] font-bold leading-snug text-ink">Hak Akses Siswa</label>
            <div class="mt-2">
                <input type="hidden" name="akses" value="0">
                <label class="flex cursor-pointer items-center gap-2">
                    <input type="checkbox" id="materi-akses" name="akses" value="1" @checked($aksesTerpilih)
                           class="rounded border-bark-300 text-espresso focus:ring-gold">
                    <span class="text-[13px] text-ink">Boleh diunduh siswa</span>
                </label>
            </div>
        </div>

        {{-- Aksi --}}
        <div class="flex flex-col gap-3 md:col-span-12 sm:flex-row sm:items-center sm:justify-end">
            {{ $slot }}
        </div>
    </div>
</form>

<script>
    (() => {
        const berkas = document.getElementById('materi-berkas');
        const namaBerkas = document.getElementById('materi-berkas-nama');

        berkas?.addEventListener('change', () => {
            const file = berkas.files[0];
            namaBerkas.textContent = file
                ? `${file.name} — ${(file.size / (1024 * 1024)).toFixed(1)} MB`
                : 'Belum ada berkas dipilih — klik untuk memilih dari komputer';
            namaBerkas.classList.toggle('text-ink', Boolean(file));
            namaBerkas.classList.toggle('text-muted', ! file);
        });

        const picker = document.getElementById('materi-target-picker');
        const trigger = document.getElementById('materi-target-trigger');
        const panel = document.getElementById('materi-target-panel');
        const labelKelas = document.getElementById('materi-target-label');
        const penghitungKelas = document.getElementById('materi-target-count');
        const pencarianKelas = document.getElementById('materi-target-search');
        const tombolSemua = document.getElementById('materi-target-toggle-all');
        const opsiKelas = panel ? Array.from(panel.querySelectorAll('[data-target-option]')) : [];

        const kelasTerpilih = () => opsiKelas.filter((opsi) => opsi.querySelector('input[type="checkbox"]').checked);

        const perbaruiRingkasan = () => {
            const terpilih = kelasTerpilih();
            penghitungKelas.textContent = String(terpilih.length);
            labelKelas.textContent = terpilih.length === 0
                ? 'Pilih kelas'
                : terpilih.map((opsi) => opsi.dataset.kode).join(', ');
        };

        const tutupPanel = () => {
            panel?.classList.add('hidden');
            trigger?.setAttribute('aria-expanded', 'false');
        };

        trigger?.addEventListener('click', () => {
            const akanTerbuka = panel.classList.contains('hidden');
            panel.classList.toggle('hidden', ! akanTerbuka);
            trigger.setAttribute('aria-expanded', String(akanTerbuka));

            if (akanTerbuka) {
                pencarianKelas.value = '';
                pencarianKelas.dispatchEvent(new Event('input'));
                pencarianKelas.focus();
            }
        });

        document.addEventListener('click', (event) => {
            if (picker && ! picker.contains(event.target)) {
                tutupPanel();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                tutupPanel();
            }
        });

        pencarianKelas?.addEventListener('input', () => {
            const kata = pencarianKelas.value.trim().toLowerCase();
            opsiKelas.forEach((opsi) => {
                opsi.classList.toggle('hidden', ! opsi.dataset.teks.includes(kata));
            });
        });

        tombolSemua?.addEventListener('click', () => {
            const opsiTampil = opsiKelas.filter((opsi) => ! opsi.classList.contains('hidden'));
            const semuaTerpilih = opsiTampil.length > 0
                && opsiTampil.every((opsi) => opsi.querySelector('input[type="checkbox"]').checked);

            opsiTampil.forEach((opsi) => {
                opsi.querySelector('input[type="checkbox"]').checked = ! semuaTerpilih;
            });

            tombolSemua.textContent = semuaTerpilih ? 'Pilih Semua' : 'Batalkan Semua';
            perbaruiRingkasan();
        });

        opsiKelas.forEach((opsi) => {
            opsi.querySelector('input[type="checkbox"]').addEventListener('change', perbaruiRingkasan);
        });

        perbaruiRingkasan();
    })();
</script>
