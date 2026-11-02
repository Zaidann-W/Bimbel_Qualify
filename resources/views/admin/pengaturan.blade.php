@extends('layouts.admin')

@section('title', 'Pengaturan Sistem — Qualify')

@section('content')
<section class="bg-white px-4 pb-6 pt-5 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="max-w-2xl">
            <h1 class="text-[28px] font-extrabold leading-[1.12] tracking-tight text-ink sm:text-[32px]">
                Pengaturan Sistem
            </h1>
            <p class="mt-2.5 max-w-xl text-[13.5px] leading-relaxed text-muted">
                Konfigurasi preferensi operasional dan notifikasi bimbel.
            </p>
        </div>
    </div>
</section>

@if (session('success'))
    <div class="px-4 sm:px-6">
        <div class="rounded-md bg-forest-50 px-4 py-3 mb-5">
            <p class="text-sm text-forest-700">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="px-4 sm:px-6">
        <div class="rounded-md bg-ember-50 px-4 py-3 mb-5">
            <p class="text-sm text-ember-700">{{ session('error') }}</p>
        </div>
    </div>
@endif

<div class="px-4 py-5 sm:px-6">
    <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        @csrf

        <!-- Notifikasi WhatsApp -->
        <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start gap-3 px-5 py-4">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-5 w-5'])
                </span>
                <div class="min-w-0">
                    <h2 class="text-[16px] font-extrabold leading-snug tracking-tight text-ink">Notifikasi WhatsApp</h2>
                    <p class="mt-1 max-w-md text-[12px] leading-relaxed text-muted">Kirim otomatis ke nomor orang tua / siswa</p>
                </div>
            </div>

            <div class="border-t border-line">
                <label for="notif-struk" class="flex cursor-pointer items-center justify-between gap-4 border-b border-line px-5 py-3.5 transition last:border-b-0 hover:bg-bark-50">
                    <span class="text-[13px] font-semibold leading-snug text-ink">Kirim bukti struk pembayaran otomatis</span>
                    <input type="hidden" name="notif_struk" value="0">
                    <input type="checkbox" id="notif-struk" name="notif_struk" value="1"
                           {{ old('notif_struk', ! empty($settings['notif_struk'])) ? 'checked' : '' }}
                           class="peer sr-only">
                    <span class="relative h-5 w-9 shrink-0 rounded-full bg-bark-200 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform after:content-[''] peer-checked:bg-espresso peer-checked:after:translate-x-4 peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true"></span>
                </label>

                <label for="notif-absensi" class="flex cursor-pointer items-center justify-between gap-4 border-b border-line px-5 py-3.5 transition last:border-b-0 hover:bg-bark-50">
                    <span class="text-[13px] font-semibold leading-snug text-ink">Pemberitahuan ketidakhadiran kelas</span>
                    <input type="hidden" name="notif_absensi" value="0">
                    <input type="checkbox" id="notif-absensi" name="notif_absensi" value="1"
                           {{ old('notif_absensi', ! empty($settings['notif_absensi'])) ? 'checked' : '' }}
                           class="peer sr-only">
                    <span class="relative h-5 w-9 shrink-0 rounded-full bg-bark-200 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform after:content-[''] peer-checked:bg-espresso peer-checked:after:translate-x-4 peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true"></span>
                </label>

                <label for="notif-tagihan" class="flex cursor-pointer items-center justify-between gap-4 border-b border-line px-5 py-3.5 last:border-b-0 hover:bg-bark-50">
                    <span class="text-[13px] font-semibold leading-snug text-ink">Pengingat tagihan SPP jatuh tempo</span>
                    <input type="hidden" name="notif_tagihan" value="0">
                    <input type="checkbox" id="notif-tagihan" name="notif_tagihan" value="1"
                           {{ old('notif_tagihan', ! empty($settings['notif_tagihan'])) ? 'checked' : '' }}
                           class="peer sr-only">
                    <span class="relative h-5 w-9 shrink-0 rounded-full bg-bark-200 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform after:content-[''] peer-checked:bg-espresso peer-checked:after:translate-x-4 peer-focus-visible:ring-4 peer-focus-visible:ring-gold/40" aria-hidden="true"></span>
                </label>
            </div>

            <footer class="flex items-start gap-2 border-t border-line bg-bark-50 px-5 py-3.5">
                @include('layouts.partials.icon', ['name' => 'whatsapp', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-forest-600'])
                <p class="text-[11.5px] leading-relaxed text-muted">
                    Pesan dikirim dari nomor resmi bimbel, maksimal 200 pesan per hari.
                </p>
            </footer>
        </section>

        <!-- Periode & cabang -->
        <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-[0_1px_3px_rgba(20,20,20,0.05)]">
            <div class="flex items-start gap-3 px-5 py-4">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold text-espresso">
                    @include('layouts.partials.icon', ['name' => 'sliders', 'class' => 'h-5 w-5'])
                </span>
                <div class="min-w-0">
                    <h2 class="text-[16px] font-extrabold leading-snug tracking-tight text-ink">Periode &amp; Cabang Bimbel</h2>
                    <p class="mt-1 max-w-md text-[12px] leading-relaxed text-muted">Parameter operasional dan kalender akademik</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 border-t border-line px-5 py-5 sm:grid-cols-2">
                <div>
                    <label for="tahun-ajaran" class="block text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">
                        Tahun Ajaran
                    </label>
                    <input id="tahun-ajaran" name="tahun_ajaran" type="text"
                           value="{{ old('tahun_ajaran', $settings['tahun_ajaran'] ?? '2024/2025') }}"
                           class="mt-2 w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[13.5px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/40">
                </div>

                <div>
                    <label for="semester" class="block text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">
                        Semester
                    </label>
                    <select id="semester" name="semester"
                            class="mt-2 w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[13.5px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/40">
                        <option value="Semester Ganjil" {{ old('semester', $settings['semester'] ?? 'Semester Ganjil') === 'Semester Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                        <option value="Semester Genap" {{ old('semester', $settings['semester'] ?? 'Semester Ganjil') === 'Semester Genap' ? 'selected' : '' }}>Semester Genap</option>
                    </select>
                </div>

                <div>
                    <label for="penanggung-jawab" class="block text-[10.5px] font-bold uppercase leading-none tracking-[0.12em] text-muted">
                        Penanggung Jawab Cabang
                    </label>
                    <input id="penanggung-jawab" name="penanggung_jawab" type="text"
                           value="{{ old('penanggung_jawab', $settings['penanggung_jawab'] ?? 'Ust. Farhan Kamil, M.Pd.') }}"
                           class="mt-2 w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-[13.5px] font-semibold text-ink transition hover:bg-bark-50 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/40">
                </div>
            </div>

            <div class="border-t border-line bg-bark-50 px-5 py-4">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-espresso px-4 py-2.5 text-[13px] font-bold text-white transition hover:bg-bark-800">
                    @include('layouts.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4 text-gold'])
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </section>
    </form>
</div>