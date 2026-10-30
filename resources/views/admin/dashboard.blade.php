@extends('layouts.admin')

@section('title', 'Dashboard Admin — Bimbel QUALIFY')

@section('content')
<div class="mx-auto max-w-[1160px] px-6 py-8 lg:px-9 lg:py-9">

    {{-- Header halaman --}}
    <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
        <div class="min-w-0">
            <p class="flex items-center gap-2 text-[10.5px] font-semibold uppercase tracking-[0.16em] text-[#a99c8e]">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-bark-200"></span>
                Bimbel Qualify
            </p>
            <h1 class="mt-2.5 max-w-[470px] text-[26px] font-bold leading-[1.15] tracking-tight text-ink lg:text-[31px]">
                Dashboard Overview &amp; Pusat Kendali
            </h1>
            <p class="mt-2.5 max-w-[480px] text-[13px] leading-relaxed text-muted">
                Ringkasan metrik harian, verifikasi pendaftaran tertunda, jadwal sesi belajar hari ini,
                dan ikhtisar kas operasional cabang utama.
            </p>
        </div>
        <a href="{{ route('admin.dashboard', ['export' => 'csv']) }}"
                class="inline-flex shrink-0 items-center gap-2 self-start rounded-xl bg-espresso px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-bark-800 md:self-end">
            @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-4 w-4'])
            Unduh Rekap Harian (CSV)
        </a>
    </div>

    {{-- Ringkasan metrik --}}
    @php
        $stats = $dashboard['stats'];
    @endphp
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <article class="rounded-2xl border border-line border-t-4 {{ $stat['edge'] }} bg-white p-4">
                <div class="flex items-start justify-between gap-2">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ $stat['tile'] }}">
                        @include('layouts.partials.icon', ['name' => $stat['icon'], 'class' => 'h-4 w-4 text-espresso'])
                    </span>
                    <span class="rounded-full px-2.5 py-1 text-[10.5px] font-bold {{ $stat['badgeClass'] }}">
                        {{ $stat['badge'] }}
                    </span>
                </div>
                <p class="mt-3 text-[11px] font-bold uppercase tracking-[0.05em] text-ink">{{ $stat['label'] }}</p>
                <p class="mt-1.5 text-[30px] font-extrabold leading-none tracking-tight text-ink">{{ $stat['value'] }}</p>
                <p class="mt-3 flex items-center gap-1.5 text-[12px] text-muted">
                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gold"></span>
                    {{ $stat['note'] }}
                </p>
            </article>
        @endforeach
    </div>

    <div class="mt-4 grid items-start gap-4 lg:grid-cols-[2fr_1fr]">

        {{-- Kolom kiri --}}
        <div class="space-y-4">

            {{-- Aksi cepat pendaftaran --}}
            <section class="rounded-3xl border border-line/80 bg-shell p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5">
                            <span class="h-2 w-2 shrink-0 rounded-full bg-bronze"></span>
                            <h2 class="max-w-[280px] text-[16.5px] font-bold leading-snug tracking-tight text-ink">
                                Aksi Cepat: Pendaftaran Menunggu Verifikasi
                            </h2>
                        </div>
                        <p class="mt-2 max-w-[400px] pl-[18px] text-[12.5px] leading-relaxed text-muted">
                            Konfirmasi pelunasan administrasi dan penerbitan Nomor Induk Siswa (NIS) baru.
                        </p>
                    </div>
                    <span class="w-[86px] shrink-0 rounded-full bg-gold px-3.5 py-2 text-center text-[11.5px] font-bold leading-tight text-espresso">
                        {{ count($dashboard['registrations']) }} Prioritas Utama
                    </span>
                </div>

                @php
                    $registrations = $dashboard['registrations'];
                @endphp
                <div class="mt-4 space-y-3">
                    @forelse ($registrations as $reg)
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-3 rounded-2xl bg-white px-4 py-3.5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-[14px] font-bold text-espresso {{ $reg['avatar'] }}">
                                {{ $reg['letter'] }}
                            </span>
                            <div class="min-w-0 flex-1 basis-[220px]">
                                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                                    <p class="text-[14px] font-bold text-ink">{{ $reg['name'] }}</p>
                                    <span class="rounded-full px-2 py-0.5 text-[10.5px] font-semibold {{ $reg['statusClass'] }}">
                                        {{ $reg['status'] }}
                                    </span>
                                </div>
                                <p class="mt-1 text-[12.5px] text-muted">{{ $reg['meta'] }}</p>
                                <p class="mt-1 text-[12.5px] text-muted">
                                    <span class="font-bold text-ink">{{ $reg['fee'] }}</span> {{ $reg['feeNote'] }}
                                </p>
                            </div>
                            <a href="{{ route('admin.pendaftaran') }}"
                               class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-4 py-2.5 text-[12.5px] font-semibold transition {{ $reg['actionClass'] }}">
                                @if ($reg['done'])
                                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                                @endif
                                {{ $reg['action'] }}
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.pendaftaran') }}"
                       class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#c08a15] hover:underline">
                        Lihat Semua {{ $dashboard['registrationTotal'] }} Pendaftaran <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>

            {{-- Jadwal & presensi sesi --}}
            <section class="rounded-3xl border border-line/80 bg-shell p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex min-w-0 gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white text-espresso ring-1 ring-line/70">
                            @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4.5 w-4.5'])
                        </span>
                        <div class="min-w-0">
                            <h2 class="max-w-[215px] text-[16.5px] font-bold leading-snug tracking-tight text-ink">
                                Jadwal &amp; Presensi Sesi Berjalan
                            </h2>
                            <p class="mt-1.5 max-w-[260px] text-[12.5px] leading-relaxed text-muted">
                                Pantau kehadiran tentor, ketersediaan ruang belajar, dan presensi realtime siswa.
                            </p>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-start gap-1.5" role="group" aria-label="Pilih sesi belajar">
                        <button type="button" data-sesi="sore" aria-pressed="true"
                                class="rounded-xl border border-line bg-white px-3 py-2 text-center text-[11.5px] font-bold leading-tight text-ink">
                            Sesi Sore<br>(15:30 - 17:30)
                        </button>
                        <button type="button" data-sesi="malam" aria-pressed="false"
                                class="rounded-xl border border-transparent px-3 py-2 text-center text-[11.5px] font-semibold leading-tight text-bark-400">
                            Sesi Malam<br>(18:30 - 20:30)
                        </button>
                    </div>
                </div>

                @php
                    $sessions = $dashboard['sessions']['sore'];
                    $sessionsMalam = $dashboard['sessions']['malam'];
                @endphp
                <div class="mt-4 space-y-3" data-sesi-panel="sore">
                    @forelse ($sessions as $session)
                        <article class="rounded-2xl bg-white px-4 py-3.5">
                            <div class="flex items-start gap-3.5">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl text-[13px] font-extrabold text-espresso {{ $session['tile'] }}">
                                    {{ $session['code'] }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2">
                                        <h3 class="min-w-0 flex-1 text-[14px] font-bold leading-snug text-ink">
                                            {{ $session['subject'] }}
                                            @if ($session['topic'])
                                                <span class="block text-[12px] font-medium text-muted">{{ $session['topic'] }}</span>
                                            @endif
                                        </h3>
                                        <div class="flex shrink-0 items-start gap-2">
                                            <span class="rounded-full bg-palegold px-2.5 py-1 text-[11px] font-semibold text-bark-600">
                                                {{ $session['grade'] }}
                                            </span>
                                            <span class="flex max-w-[150px] items-center gap-1.5 rounded-full px-3 py-1.5 text-[11.5px] font-semibold leading-tight
                                                {{ $session['live'] ? 'bg-gold text-espresso' : 'bg-sand text-muted' }}">
                                                <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $session['live'] ? 'bg-espresso' : 'bg-bark-300' }}"></span>
                                                {{ $session['presence'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2.5 flex flex-wrap items-start gap-x-2 gap-y-1 text-[12px] text-muted">
                                        <span class="flex w-[138px] items-start gap-1.5">
                                            @include('layouts.partials.icon', ['name' => 'user', 'class' => 'mt-px h-3.5 w-3.5 text-bark-300'])
                                            <span>{{ $session['tutor'] }}</span>
                                        </span>
                                        <span class="mt-[7px] h-1 w-1 shrink-0 rounded-full bg-bark-300"></span>
                                        <span class="flex w-[140px] items-start gap-1.5">
                                            @include('layouts.partials.icon', ['name' => 'building', 'class' => 'mt-px h-3.5 w-3.5 text-bark-300'])
                                            <span>{{ $session['room'] }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl bg-white px-4 py-6 text-center text-[12.5px] text-muted">Belum ada sesi sore hari ini.</p>
                    @endforelse
                </div>

                <div class="mt-4 space-y-3" data-sesi-panel="malam" hidden>
                    @forelse ($sessionsMalam as $session)
                        <article class="rounded-2xl bg-white px-4 py-3.5">
                            <div class="flex items-start gap-3.5">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl text-[13px] font-extrabold text-espresso {{ $session['tile'] }}">
                                    {{ $session['code'] }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2">
                                        <h3 class="min-w-0 flex-1 text-[14px] font-bold leading-snug text-ink">
                                            {{ $session['subject'] }}
                                            @if ($session['topic'])
                                                <span class="block text-[12px] font-medium text-muted">{{ $session['topic'] }}</span>
                                            @endif
                                        </h3>
                                        <div class="flex shrink-0 items-start gap-2">
                                            <span class="rounded-full bg-palegold px-2.5 py-1 text-[11px] font-semibold text-bark-600">
                                                {{ $session['grade'] }}
                                            </span>
                                            <span class="flex max-w-[150px] items-center gap-1.5 rounded-full px-3 py-1.5 text-[11.5px] font-semibold leading-tight
                                                {{ $session['live'] ? 'bg-gold text-espresso' : 'bg-sand text-muted' }}">
                                                <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $session['live'] ? 'bg-espresso' : 'bg-bark-300' }}"></span>
                                                {{ $session['presence'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2.5 flex flex-wrap items-start gap-x-2 gap-y-1 text-[12px] text-muted">
                                        <span class="flex w-[138px] items-start gap-1.5">
                                            @include('layouts.partials.icon', ['name' => 'user', 'class' => 'mt-px h-3.5 w-3.5 text-bark-300'])
                                            <span>{{ $session['tutor'] }}</span>
                                        </span>
                                        <span class="mt-[7px] h-1 w-1 shrink-0 rounded-full bg-bark-300"></span>
                                        <span class="flex w-[140px] items-start gap-1.5">
                                            @include('layouts.partials.icon', ['name' => 'building', 'class' => 'mt-px h-3.5 w-3.5 text-bark-300'])
                                            <span>{{ $session['room'] }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl bg-white px-4 py-6 text-center text-[12.5px] text-muted">Belum ada sesi malam hari ini.</p>
                    @endforelse
                </div>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.absensiJadwal') }}"
                       class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#c08a15] hover:underline">
                        Buka Manajemen Absensi &amp; Jadwal Lengkap <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>
        </div>

        {{-- Kolom kanan --}}
        <div class="space-y-4">

            {{-- Ringkasan arus kas --}}
            <section class="rounded-3xl border border-line/80 bg-white p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-shell text-espresso">
                            @include('layouts.partials.icon', ['name' => 'wallet', 'class' => 'h-4 w-4'])
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-[14.5px] font-bold leading-snug text-ink">Ringkasan Arus Kas</h2>
                            <p class="mt-0.5 text-[11.5px] text-muted">Perkiraan Real-time Hari Ini</p>
                        </div>
                    </div>
                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-bronze"></span>
                </div>

                <div class="mt-4 space-y-2.5">
                    <div class="flex items-start justify-between gap-3 rounded-xl bg-shell px-4 py-3">
                        <span class="flex items-center gap-1.5 text-[12.5px] font-medium text-ink">
                            <span class="text-bronze" aria-hidden="true">↓</span>
                            Total Masuk
                        </span>
                        <span class="text-right text-[13px] font-bold text-ink">Rp {{ number_format($dashboard['cash']['income'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-3 rounded-xl bg-shell px-4 py-3">
                        <span class="flex items-center gap-1.5 text-[12.5px] font-medium leading-snug text-ink">
                            <span class="text-ember-500" aria-hidden="true">↑</span>
                            <span class="max-w-[130px]">Total Keluar (Operasional)</span>
                        </span>
                        <span class="max-w-[64px] text-right text-[13px] font-bold leading-snug text-ink">
                            {{ $dashboard['cash']['expense'] === null ? '—' : 'Rp '.number_format($dashboard['cash']['expense'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between gap-3 rounded-2xl bg-espresso px-4 py-3.5">
                    <div class="min-w-0">
                        <p class="text-[9.5px] font-bold uppercase tracking-[0.12em] text-palegold">Saldo Bersih Hari Ini</p>
                        <p class="mt-1.5 text-[19px] font-extrabold leading-none text-white">Rp {{ number_format($dashboard['cash']['balance'], 0, ',', '.') }}</p>
                    </div>
                    @include('layouts.partials.icon', ['name' => 'piggy-bank', 'class' => 'h-5 w-5 shrink-0 text-gold'])
                </div>

                @if ($dashboard['cash']['expense'] === null)
                    <p class="mt-2 text-[11px] leading-relaxed text-muted">Pencatatan pengeluaran operasional belum tersedia di sistem.</p>
                @endif

                <a href="{{ route('admin.keuangan') }}"
                   class="mt-3 flex w-full items-center justify-center gap-1 rounded-xl bg-sand px-4 py-3 text-[13px] font-semibold text-ink transition hover:bg-line">
                    Lihat Jurnal Transaksi
                    @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
                </a>
            </section>

            {{-- Klinik PR --}}
            <section class="rounded-3xl border border-line/80 bg-white p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-palegold text-espresso">
                            @include('layouts.partials.icon', ['name' => 'help-circle', 'class' => 'h-4 w-4'])
                        </span>
                        <div class="min-w-0">
                            <h2 class="max-w-[160px] text-[14.5px] font-bold leading-snug text-ink">Klinik PR Siswa Hari Ini</h2>
                            <p class="mt-0.5 max-w-[145px] text-[11.5px] text-muted">Bimbingan Tatap Muka 1-on-1</p>
                        </div>
                    </div>
                    <span class="w-[46px] shrink-0 rounded-full bg-gold px-2 py-1 text-center text-[10.5px] font-bold leading-tight text-espresso">
                        {{ $dashboard['qualifix']['butuhTentor'] }} Antrian
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-shell px-3.5 py-3">
                        <p class="text-[9.5px] font-bold uppercase tracking-[0.08em] text-muted">Tiket Masuk</p>
                        <p class="mt-1 text-[16px] font-extrabold leading-none text-ink">{{ $dashboard['qualifix']['masuk'] }} Tiket</p>
                    </div>
                    <div class="rounded-xl bg-shell px-3.5 py-3">
                        <p class="text-[9.5px] font-bold uppercase tracking-[0.08em] text-muted">Butuh Tentor</p>
                        <p class="mt-1 text-[16px] font-extrabold leading-none text-[#c9971b]">{{ $dashboard['qualifix']['butuhTentor'] }} Tiket</p>
                    </div>
                </div>

                <div class="mt-3.5 h-1.5 overflow-hidden rounded-full bg-sand" role="img" aria-label="{{ $dashboard['qualifix']['selesai'] }} dari {{ $dashboard['qualifix']['masuk'] }} tiket terlayani hari ini">
                    <div class="h-full rounded-full bg-gradient-to-r from-bronze to-gold" style="width: {{ $dashboard['qualifix']['progress'] }}%"></div>
                </div>

                <div class="mt-3 flex justify-between">
                    <a href="{{ route('admin.qualifix') }}" class="text-[13px] font-semibold text-[#c08a15] hover:underline">
                        Kelola Tiket Klinik PR
                    </a>
                    <a href="{{ route('admin.qualifix') }}" aria-label="Kelola Tiket Klinik PR"
                       class="text-[13px] font-semibold text-[#c08a15] hover:underline">
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>

            {{-- Agenda & sistem bimbel --}}
            <section class="rounded-3xl border border-line/80 bg-white p-5">
                <div class="flex items-center gap-2.5">
                    @include('layouts.partials.icon', ['name' => 'calendar', 'class' => 'h-4 w-4 text-ink'])
                    <h2 class="text-[14.5px] font-bold text-ink">Agenda &amp; Sistem Bimbel</h2>
                </div>
                <div class="mt-3.5 rounded-2xl bg-shell px-4 py-3.5">
                    <div class="flex items-start gap-2.5">
                        @include('layouts.partials.icon', ['name' => 'graduation-cap', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-[#c9971b]'])
                        <p class="max-w-[140px] text-[13px] font-bold leading-snug text-ink">{{ $dashboard['agenda']['periode'] }}</p>
                    </div>
                    <p class="mt-2 text-[12.5px] leading-relaxed text-muted">
                        {{ $dashboard['agenda']['catatan'] }}
                    </p>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sesiButtons = document.querySelectorAll('[data-sesi]');
        const sesiPanels = document.querySelectorAll('[data-sesi-panel]');

        sesiButtons.forEach((button) => {
            button.addEventListener('click', function () {
                sesiButtons.forEach((other) => {
                    const active = other === button;
                    other.setAttribute('aria-pressed', String(active));
                    other.classList.toggle('bg-white', active);
                    other.classList.toggle('border-line', active);
                    other.classList.toggle('font-bold', active);
                    other.classList.toggle('text-ink', active);
                    other.classList.toggle('border-transparent', !active);
                    other.classList.toggle('font-semibold', !active);
                    other.classList.toggle('text-bark-400', !active);
                });

                sesiPanels.forEach((panel) => {
                    panel.hidden = panel.dataset.sesiPanel !== button.dataset.sesi;
                });
            });
        });
    });
</script>
@endpush
