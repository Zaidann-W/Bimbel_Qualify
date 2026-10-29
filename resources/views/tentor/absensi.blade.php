@extends('layouts.tentor')

@section('title', 'Absensi Siswa')

@section('content')
@php
    $statusMap = [
        'hadir' => ['Hadir', 'bg-emerald-100 text-emerald-700 ring-emerald-200'],
        'izin' => ['Izin', 'bg-amber-50 text-amber-700 ring-amber-200'],
        'sakit' => ['Sakit', 'bg-sky-50 text-sky-700 ring-sky-200'],
        'alpa' => ['Alpa', 'bg-rose-50 text-rose-700 ring-rose-200'],
    ];

    $sessionStatusOptions = ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'];
@endphp

<div class="space-y-5">

    {{-- Breadcrumb + judul --}}
    <div>
        <nav class="flex items-center gap-1.5 text-[11px] font-medium text-bark-400">
            <a href="{{ route('tentor.dashboard') }}" class="transition hover:text-ember-600">Dashboard</a>
            @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3 w-3 text-bark-300'])
            <span class="text-bark-600">Absensi</span>
        </nav>
        <div class="mt-2 flex flex-wrap items-center gap-3">
            <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Absensi Siswa</h1>
            @if ($session)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Sesi Berlangsung
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-bark-100 px-2.5 py-1 text-[11px] font-bold text-bark-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-bark-400"></span>
                    Tidak Ada Sesi Hari Ini
                </span>
            @endif
        </div>
        <p class="mt-1 text-[13px] text-bark-400">Catat kehadiran murid dan pantau rekap per pertemuan.</p>
    </div>

    {{-- Pilih sesi (jika ada lebih dari 1) --}}
    @if ($sessions->count() > 1)
        <div class="rounded-2xl border border-bark-100 bg-white p-4">
            <label class="block text-[12px] font-medium text-bark-600 mb-2">Pilih Sesi</label>
            <div class="flex flex-wrap gap-2">
                @foreach ($sessions as $s)
                    <a href="{{ route('tentor.absensi', ['session_id' => $s->id]) }}"
                       class="px-3 py-1.5 rounded-lg text-[12px] font-medium transition
                           {{ $session && $session->id === $s->id ? 'bg-ember-500 text-white' : 'bg-bark-50 text-bark-600 hover:bg-bark-100' }}">
                        {{ $s->start_time->format('H.i') }} – {{ $s->end_time->format('H.i') }} · {{ $s->subject->name ?? $s->topic ?? 'Mata Pelajaran' }} · {{ $s->classGroup->code ?? 'Kelas' }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Panel sesi --}}
    @if ($session)
        <div class="grid grid-cols-1 gap-x-6 gap-y-4 rounded-2xl border border-bark-100 bg-white p-5 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $sesi = [
                    ['Rombongan Belajar', $session->classGroup->code ?? '-', $session->classGroup->education_level.' '.$session->classGroup->grade.' '.$session->classGroup->major ?? '-', 'graduation-cap'],
                    ['Waktu Sesi', $session->session_date->locale('id')->translatedFormat('l, j F Y'), $session->start_time->format('H.i').' – '.$session->end_time->format('H.i').' WIB', 'clock'],
                    ['Mata Pelajaran', $session->subject->name ?? $session->topic ?? '-', 'Topik: '.($session->topic ?? '-'), 'book-open'],
                    ['Ruangan', $session->room->name ?? '-', $session->room->building ?? '', 'map-pin'],
                ];
            @endphp
            @foreach ($sesi as [$label, $value, $meta, $icon])
                <div class="flex items-start gap-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-bark-50 text-bark-500">
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-[18px] w-[18px]'])
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                        <p class="mt-0.5 truncate text-[14px] font-bold text-bark-800">{{ $value }}</p>
                        <p class="truncate text-[11px] text-bark-400">{{ $meta }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Stat cards --}}
        @php
            $totalSiswa = $students->count();
            $hadirCount = $attendances->where('status', 'hadir')->count();
            $izinCount = $attendances->whereIn('status', ['izin', 'sakit'])->count();
            $alpaCount = $attendances->where('status', 'alpa')->count();
            $belumInput = $totalSiswa - $attendances->count();
        @endphp
        <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            @php
                $stats = [
                    ['Total Siswa', $totalSiswa, 'Terdaftar', 'bg-bark-100 text-bark-600', 'users'],
                    ['Hadir', $hadirCount, 'Sudah tap-in', 'bg-emerald-50 text-emerald-600', 'user-check'],
                    ['Izin / Sakit', $izinCount, 'Dengan keterangan', 'bg-amber-50 text-amber-600', 'mail'],
                    ['Alpa', $alpaCount, 'Tanpa keterangan', 'bg-rose-50 text-rose-600', 'siren'],
                ];
            @endphp
            @foreach ($stats as [$label, $value, $sub, $tone, $icon])
                <div class="relative overflow-hidden rounded-2xl border border-bark-100 bg-white p-5">
                    <div class="absolute inset-x-0 top-0 h-1 bg-ember-400"></div>
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                            <p class="mt-2 text-[28px] font-bold leading-none tracking-tight text-bark-800">{{ $value }}</p>
                        </div>
                        <div @class(['grid h-9 w-9 shrink-0 place-items-center rounded-xl', $tone])>
                            @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-[18px] w-[18px]'])
                        </div>
                    </div>
                    <p class="mt-3 text-[11px] text-bark-400">{{ $sub }}</p>
                </div>
            @endforeach
        </div>

        {{-- Form absensi --}}
        <form action="{{ route('tentor.absensi.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="session_id" value="{{ $session->id }}">

            {{-- Tabel kehadiran --}}
            <section class="overflow-hidden rounded-2xl border border-bark-100 bg-white">
                <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-5 py-4">
                    <div>
                        <h2 class="text-[15px] font-bold text-bark-800">Daftar Kehadiran</h2>
                        <p class="text-[11px] text-bark-400">Pilih status kehadiran setiap peserta didik pada sesi ini.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-bark-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                            <input type="search" placeholder="Cari nama siswa…"
                                   class="w-full rounded-lg border border-bark-200 bg-bark-50 py-2 pl-8 pr-3 text-[12px] placeholder:text-bark-300 focus:border-ember-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-ember-100 sm:w-48"
                                   id="searchInput">
                        </div>
                        <button type="button" id="setAllHadirBtn"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-[12px] font-semibold text-emerald-700 transition hover:bg-emerald-100">
                            @include('layouts.partials.icon', ['name' => 'check', 'class' => 'h-3.5 w-3.5'])
                            Set Semua Hadir
                        </button>
                    </div>
                </header>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-left">
                        <thead>
                            <tr class="border-b border-bark-100 bg-bark-50/60 text-[10px] font-bold uppercase tracking-wide text-bark-400">
                                <th class="w-14 px-5 py-3 text-center">No</th>
                                <th class="px-5 py-3">Nama Siswa & NIS / NISN</th>
                                <th class="w-32 px-5 py-3">Waktu Datang</th>
                                <th class="w-64 px-5 py-3">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bark-100" id="attendanceTableBody">
                            @forelse ($students as $i => $siswa)
                                @php
                                    $attendance = $attendances->get($siswa->id);
                                    $currentStatus = $attendance->status ?? 'alpa';
                                    [$label, $badge] = $statusMap[$currentStatus];
                                    $waktu = $attendance->arrived_at ? $attendance->arrived_at->format('H:i') : null;
                                @endphp
                                <tr class="transition hover:bg-bark-50/60" data-nama="{{ strtolower($siswa->full_name) }}">
                                    <td class="px-5 py-3.5 text-center text-[12px] font-medium text-bark-400">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-ember-100 text-[11px] font-bold text-ember-700">
                                                {{ strtoupper(mb_substr($siswa->full_name, 0, 1) . mb_substr(explode(' ', $siswa->full_name)[1] ?? '', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-[13px] font-semibold text-bark-800">{{ $siswa->full_name }}</p>
                                                <p class="truncate text-[11px] text-bark-400">{{ $siswa->nis }} · {{ $siswa->nisn }} · {{ $siswa->school_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <input type="time" name="attendances[{{ $siswa->id }}][arrived_at]"
                                               value="{{ $waktu }}"
                                               class="w-full rounded-lg border border-bark-200 bg-bark-50 py-1.5 pl-2 pr-3 text-[12px] placeholder:text-bark-300 focus:border-ember-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-ember-100">
                                        <input type="hidden" name="attendances[{{ $siswa->id }}][student_id]" value="{{ $siswa->id }}">
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="inline-flex rounded-lg bg-bark-50 p-0.5 ring-1 ring-bark-100">
                                            @foreach ($sessionStatusOptions as $key => $opt)
                                                <button type="button"
                                                        data-student="{{ $siswa->id }}"
                                                        data-status="{{ $key }}"
                                                        class="status-btn px-2.5 py-1 text-[11px] font-semibold rounded-md transition
                                                            {{ $key === $currentStatus ? $badge.' ring-1' : 'text-bark-400 hover:text-bark-600' }}"
                                                        @class(['cursor-pointer', $key !== $currentStatus])
                                                >
                                                    {{ $opt }}
                                                </button>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="attendances[{{ $siswa->id }}][status]" value="{{ $currentStatus }}" class="status-input" data-student="{{ $siswa->id }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-bark-400">Tidak ada siswa terdaftar di kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-bark-100 bg-bark-50/50 px-5 py-4">
                    <p class="text-[12px] text-bark-400">
                        Menampilkan <span class="font-bold text-bark-600">{{ $totalSiswa }}</span> siswa terdaftar.
                    </p>
                    <div class="flex items-center gap-2">
                        <button type="button" id="resetBtn" class="rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                            Reset Form
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-ember-500 px-3.5 py-2 text-[12px] font-semibold text-white shadow-sm transition hover:bg-ember-600">
                            @include('layouts.partials.icon', ['name' => 'clipboard-check', 'class' => 'h-3.5 w-3.5'])
                            Simpan Presensi Kelas
                        </button>
                    </div>
                </footer>
            </section>

        </form>

        {{-- Riwayat pertemuan --}}
        @php
            // Ambil sesi sebelumnya untuk riwayat
            $riwayatSessions = ClassSession::with(['classGroup', 'subject'])
                ->where('staff_id', $staffProfile->id)
                ->whereDate('session_date', '<', today())
                ->orderByDesc('session_date')
                ->take(5)
                ->get();
        @endphp

        @if ($riwayatSessions->isNotEmpty())
            <section class="rounded-2xl border border-bark-100 bg-white">
                <header class="border-b border-bark-100 px-5 py-4">
                    <h2 class="text-[15px] font-bold text-bark-800">Riwayat Sesi Terakhir</h2>
                    <p class="text-[11px] text-bark-400">Rekap kehadiran pertemuan yang sudah ditutup</p>
                </header>
                <ul class="grid grid-cols-1 divide-y divide-bark-100 sm:grid-cols-3 sm:divide-y-0">
                    @foreach ($riwayatSessions as $sesi)
                        @php
                            $histAttendances = StudentAttendance::where('class_session_id', $sesi->id)->get();
                            $histHadir = $histAttendances->where('status', 'hadir')->count();
                            $histSakit = $histAttendances->where('status', 'sakit')->count();
                            $histIzin = $histAttendances->where('status', 'izin')->count();
                            $histTotal = $histAttendances->count();
                            $histPersen = $histTotal > 0 ? round(($histHadir / $histTotal) * 100) : 0;
                        @endphp
                        <li class="px-5 py-4 sm:border-r sm:border-bark-100 sm:last:border-r-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[13px] font-bold text-bark-800">{{ $sesi->subject->name ?? 'Sesi' }} · {{ $sesi->session_date->format('d/m/Y') }}</p>
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $histPersen }}%</span>
                            </div>
                            <p class="mt-0.5 text-[11px] text-bark-400">{{ $sesi->session_date->locale('id')->translatedFormat('l, j F Y') }}</p>
                            <p class="mt-2 text-[12px] text-bark-600">
                                <span class="font-bold text-bark-800">{{ $histHadir }} / {{ $histTotal }} Hadir</span>
                            </p>
                            <p class="mt-0.5 text-[11px] text-bark-400">Hadir: {{ $histHadir }} · Sakit: {{ $histSakit }} · Izin: {{ $histIzin }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

    @else
        {{-- Tidak ada sesi hari ini --}}
        <div class="rounded-2xl border border-bark-100 bg-white p-12 text-center">
            @include('layouts.partials.icon', ['name' => 'calendar-x', 'class' => 'mx-auto h-12 w-12 text-bark-300'])
            <h3 class="mt-4 text-[18px] font-bold text-bark-800">Tidak ada jadwal mengajar hari ini</h3>
            <p class="mt-2 text-bark-400">Anda tidak memiliki sesi mengajar pada tanggal {{ today()->locale('id')->translatedFormat('l, j F Y') }}.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status button toggle
    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.dataset.student;
            const status = this.dataset.status;

            // Update hidden input
            document.querySelector(`.status-input[data-student="${studentId}"]`).value = status;

            // Update button styles
            document.querySelectorAll(`.status-btn[data-student="${studentId}"]`).forEach(b => {
                const isActive = b.dataset.status === status;
                const badgeClasses = {
                    hadir: 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                    izin: 'bg-amber-50 text-amber-700 ring-amber-200',
                    sakit: 'bg-sky-50 text-sky-700 ring-sky-200',
                    alpa: 'bg-rose-50 text-rose-700 ring-rose-200',
                };
                if (isActive) {
                    b.className = 'status-btn px-2.5 py-1 text-[11px] font-semibold rounded-md transition ' + badgeClasses[status] + ' ring-1 cursor-pointer';
                } else {
                    b.className = 'status-btn px-2.5 py-1 text-[11px] font-semibold rounded-md transition text-bark-400 hover:text-bark-600 cursor-pointer';
                }
            });
        });
    });

    // Search filter
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('attendanceTableBody');
    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            tableBody.querySelectorAll('tr[data-nama]').forEach(row => {
                row.style.display = row.dataset.nama.includes(query) ? '' : 'none';
            });
        });
    }

    // Set semua hadir
    const setAllHadirBtn = document.getElementById('setAllHadirBtn');
    if (setAllHadirBtn) {
        setAllHadirBtn.addEventListener('click', function() {
            document.querySelectorAll('.status-btn[data-status="hadir"]').forEach(btn => {
                btn.click();
            });
        });
    }

    // Reset button
    const resetBtn = document.getElementById('resetBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (confirm('Reset semua status ke Alpa dan kosongkan waktu datang?')) {
                document.querySelectorAll('.status-btn[data-status="alpa"]').forEach(btn => {
                    btn.click();
                });
                document.querySelectorAll('input[type="time"]').forEach(input => {
                    input.value = '';
                });
            }
        });
    }
});
</script>
@endpush