@extends('layouts.tentor')

@section('title', 'Qualifix')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-[22px] font-bold tracking-tight text-bark-800">Qualifix</h1>
        <p class="mt-1 text-[13px] text-bark-400">Respons dan pandu pemahaman tugas siswa secara cepat, terarah, dan berkualitas.</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach ($stats as [$label, $value, $unit, $sub, $icon, $iconTone, $tone])
            <div class="relative overflow-hidden rounded-2xl border border-bark-100 bg-white p-5">
                <div class="absolute inset-x-0 top-0 h-1 bg-ember-400"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-bark-400">{{ $label }}</p>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="text-[28px] font-bold leading-none tracking-tight text-bark-800">{{ $value }}</span>
                            <span class="text-[12px] font-medium text-bark-400">{{ $unit }}</span>
                        </div>
                    </div>
                    <div @class(['grid h-9 w-9 place-items-center rounded-xl', $tone])>
                        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-[18px] w-[18px]'])
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-bark-400">{{ $sub }}</p>
            </div>
        @endforeach
    </div>

    {{-- Tabs (Requirement 1: Filter bekerja sesuai fungsi) --}}
    <div class="flex flex-wrap items-center gap-2" id="qualifix-tabs">
        @foreach ($tabs as $tab)
            @php
                $isActive = $tab['active'] ?? false;
                $tabKey = $tab['key'] ?? 'semua';
                $tabLabel = $tab['label'] ?? ($tab[0] ?? 'Semua');
                $tabCount = $tab['count'] ?? ($tab[1] ?? 0);
            @endphp
            <a href="{{ route('tentor.qualifix', ['tab' => $tabKey]) }}"
               data-tab="{{ $tabKey }}"
               @class([
                   'tab-filter-btn flex items-center gap-2 rounded-lg px-3.5 py-2 text-[12.5px] font-semibold transition',
                   'bg-bark-800 text-white shadow-sm' => $isActive,
                   'border border-bark-200 bg-white text-bark-500 hover:bg-bark-50' => ! $isActive,
               ])>
                <span>{{ $tabLabel }}</span>
                @if ($isActive)
                    <span class="grid h-5 min-w-[20px] px-1 place-items-center rounded-full bg-ember-400 text-[10px] font-bold text-bark-900">{{ $tabCount }}</span>
                @else
                    <span class="text-bark-400 text-[11px]">({{ $tabCount }})</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Layout Utama: Kiri Antrean Pertanyaan, Kanan Detail Pertanyaan & Jawaban Tentor --}}
    <div class="flex flex-col lg:flex-row gap-6 items-start">

        {{-- Kolom Kiri: Antrean Pertanyaan (Tersaring berdasarkan tab yang aktif) --}}
        <section class="w-full lg:w-[350px] shrink-0 overflow-hidden rounded-2xl border border-bark-100 bg-white shadow-sm">
            <header class="flex items-center justify-between border-b border-bark-100 px-4 py-3.5">
                <h2 class="text-[11px] font-bold uppercase tracking-wide text-bark-400">Antrean Pertanyaan</h2>
                <span class="text-[11px] text-bark-400 font-medium">{{ count($antrean) }} dari {{ $totalCount ?? count($antrean) }} tampil</span>
            </header>

            @if (count($antrean) > 0)
                <ul class="divide-y divide-bark-100 p-2 space-y-1.5 max-h-[calc(100vh-280px)] overflow-y-auto" id="antrean-list">
                    @foreach ($antrean as $item)
                        @php
                            $itemId = $item['id'] ?? $item[8] ?? 0;
                            $itemInitial = $item['initial'] ?? $item[0] ?? 'S';
                            $itemNama = $item['nama'] ?? $item[1] ?? 'Siswa';
                            $itemStatus = $item['status'] ?? $item[2] ?? 'Menunggu';
                            $itemMeta = $item['meta'] ?? $item[3] ?? '';
                            $itemTopik = $item['topik'] ?? $item[4] ?? '';
                            $itemPreview = $item['preview'] ?? $item[5] ?? '';
                            $itemTags = $item['tags'] ?? $item[6] ?? [];
                            $itemActive = $item['active'] ?? $item[7] ?? false;
                            $pemberiJawaban = $item['pemberi_jawaban'] ?? null;
                            $isMenunggu = ($item['is_menunggu'] ?? false) || $itemStatus === 'Menunggu';
                            $isSudahDijawab = ($item['is_sudah_dijawab'] ?? false) || $itemStatus === 'Sudah Dijawab';
                            $isSelesai = ($item['is_selesai'] ?? false) || $itemStatus === 'Selesai';
                        @endphp
                        <li>
                            <a href="{{ route('tentor.qualifix', ['tab' => $activeTab, 'ticket_id' => $itemId]) }}"
                               data-ticket-id="{{ $itemId }}"
                               @class([
                                   'block p-3.5 rounded-xl transition border text-left',
                                   'border-ember-400 bg-ember-50/70 shadow-sm ring-1 ring-ember-300/50' => $itemActive,
                                   'border-transparent hover:bg-bark-50/70 hover:border-bark-200' => ! $itemActive,
                               ])>
                                <div class="flex items-start gap-2.5">
                                    <div @class([
                                        'grid h-9 w-9 shrink-0 place-items-center rounded-full text-[12px] font-bold',
                                        'bg-ember-200 text-ember-800' => $itemActive,
                                        'bg-bark-100 text-bark-600' => ! $itemActive,
                                    ])>{{ $itemInitial }}</div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-[13px] font-bold text-bark-800">{{ $itemNama }}</p>
                                        <p class="truncate text-[11px] text-bark-400">{{ $itemMeta }}</p>
                                    </div>
                                    <span @class([
                                        'shrink-0 rounded-full px-2 py-[2.5px] text-[10px] font-bold',
                                        'bg-ember-100 text-ember-700' => $isMenunggu,
                                        'bg-emerald-50 text-emerald-700 border border-emerald-200/60' => $isSudahDijawab,
                                        'bg-sky-50 text-sky-700 border border-sky-200/60' => $isSelesai,
                                    ])>{{ $itemStatus }}</span>
                                </div>

                                <p class="mt-2 text-[12.5px] font-semibold text-bark-700 line-clamp-1">{{ $itemTopik }}</p>

                                @if ($itemPreview)
                                    <p class="mt-1 line-clamp-2 text-[11.5px] leading-relaxed text-bark-500">{{ $itemPreview }}</p>
                                @endif

                                <div class="mt-2.5 flex flex-wrap items-center justify-between gap-1.5">
                                    <div class="flex flex-wrap items-center gap-1">
                                        @foreach ($itemTags as $tag)
                                            <span class="inline-flex items-center rounded-md bg-bark-100 px-2 py-0.5 text-[10px] font-semibold text-bark-600">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>

                                    @if ($pemberiJawaban)
                                        <span @class([
                                            'inline-flex items-center gap-1 text-[11px] font-medium',
                                            'text-sky-700' => $isSelesai,
                                            'text-emerald-600' => ! $isSelesai,
                                        ])>
                                            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5'])
                                            <span>Dijawab: <strong class="font-bold">{{ $pemberiJawaban }}</strong></span>
                                        </span>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-8 text-center">
                    <p class="text-[12.5px] text-bark-500">Tidak ada pertanyaan pada filter ini.</p>
                </div>
            @endif
        </section>

        {{-- Kolom Kanan: Detail Pertanyaan & Jawaban Tentor (Berdampingan di sebelah antrean) --}}
        <div class="w-full lg:flex-1 min-w-0 space-y-6">
            @if ($selectedTicket)
                @php
                    $sStudent = $selectedTicket->student;
                    $sSubject = $selectedTicket->subject;
                    $sClass = $selectedTicket->classGroup;
                    $sStudentAtts = $selectedTicket->attachments->whereNull('answer_id');
                    $sAnswers = $selectedTicket->answers;
                    $sInitial = $sStudent?->inisial() ?? 'S';
                    $sNama = $sStudent?->full_name ?? 'Siswa';
                    $sKelas = $sClass?->name ?? $sClass?->code ?? ($sStudent?->school_grade ? 'Kelas ' . $sStudent->school_grade : 'Siswa');
                    $sWaktu = $selectedTicket->submitted_at?->translatedFormat('d M Y, H:i') ?? 'Baru saja';
                    $isSelesai = $selectedTicket->status === 'selesai';
                    $isSudahDijawab = $selectedTicket->status === 'sedang_dibahas';
                    $isMenunggu = $selectedTicket->status === 'menunggu_tentor';
                    $isCurrentTentorAssigned = $staffProfile && $selectedTicket->staff->contains('id', $staffProfile->id);
                    $assignedTentors = $selectedTicket->staff;
                @endphp

                {{-- Card 1: Detail Pertanyaan Siswa --}}
                <section class="rounded-2xl border border-bark-100 bg-white shadow-sm">
                    <header class="border-b border-bark-100 px-6 py-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-ember-100 text-[14px] font-bold text-ember-700">{{ $sInitial }}</div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-[15px] font-bold text-bark-800">{{ $sNama }}</h2>
                                        <span class="rounded-full bg-bark-100 px-2.5 py-0.5 text-[10.5px] font-bold text-bark-600">{{ $sKelas }}</span>
                                    </div>
                                    <p class="mt-0.5 text-[11.5px] text-bark-400">Diajukan: {{ $sWaktu }} • No. Tiket: <span class="font-mono font-semibold text-bark-600">{{ $selectedTicket->ticket_number }}</span></p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-bark-100 px-3 py-1 text-[11px] font-bold text-bark-700">
                                    {{ $sSubject?->name ?? 'Mata Pelajaran' }}
                                </span>

                                {{-- Requirement 2: Bedakan status Sudah Dijawab dengan Selesai --}}
                                @if ($isSelesai)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 border border-sky-200 px-3 py-1 text-[11px] font-bold text-sky-700">
                                        @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3.5 w-3.5 text-sky-600'])
                                        Selesai • Rating Diberikan Siswa
                                    </span>
                                @elseif ($isSudahDijawab)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-[11px] font-bold text-emerald-700">
                                        @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-3.5 w-3.5 text-emerald-600'])
                                        Sudah Dijawab
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-ember-100 px-3 py-1 text-[11px] font-bold text-ember-700">
                                        @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-3.5 w-3.5'])
                                        Menunggu Respons
                                    </span>
                                @endif
                            </div>
                        </div>
                    </header>

                    <div class="space-y-4 px-6 py-5">
                        {{-- Topik / Judul Soal --}}
                        <div class="flex items-start gap-3 rounded-xl border border-ember-200 bg-ember-50 px-4 py-3.5">
                            @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'mt-0.5 h-4 w-4 shrink-0 text-ember-600'])
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-ember-700">Topik / Judul Pertanyaan</p>
                                <p class="text-[13.5px] font-bold text-bark-800 mt-0.5">{{ $selectedTicket->title }}</p>
                            </div>
                        </div>

                        {{-- Teks Pertanyaan Siswa --}}
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-bark-400 mb-1.5">Pertanyaan Siswa:</p>
                            <div class="rounded-xl border-l-[3px] border-ember-400 bg-bark-50 px-4 py-3.5 text-[13px] leading-relaxed text-bark-700 whitespace-pre-line">
                                {{ $selectedTicket->question }}
                            </div>
                        </div>

                        {{-- Lampiran Siswa (Foto / Berkas Soal) --}}
                        <div>
                            <h3 class="text-[12.5px] font-bold text-bark-700">
                                Lampiran Siswa ({{ $sStudentAtts->count() }} Berkas)
                            </h3>

                            @if ($sStudentAtts->isNotEmpty())
                                <div class="mt-2.5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($sStudentAtts as $att)
                                        @php
                                            $file = $att->file;
                                            $isImage = $file && in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/gif']);
                                            $sizeFormatted = $file && $file->size_bytes ? number_format($file->size_bytes / 1024, 1) . ' KB' : 'Lampiran';
                                        @endphp
                                        <div class="rounded-xl border border-bark-100 bg-bark-50/60 p-3">
                                            @if ($file && $isImage)
                                                <div class="mb-2.5 overflow-hidden rounded-lg border border-bark-200 bg-white">
                                                    <a href="{{ route('tentor.qualifix.file', $file->id) }}" target="_blank" title="Klik untuk membuka foto ukuran penuh">
                                                        <img src="{{ route('tentor.qualifix.file', $file->id) }}"
                                                             alt="{{ $file->original_name }}"
                                                             class="max-h-56 w-full object-contain bg-bark-100 hover:scale-[1.01] transition duration-200">
                                                    </a>
                                                </div>
                                            @endif

                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-ember-500 ring-1 ring-bark-200">
                                                    @include('layouts.partials.icon', ['name' => $isImage ? 'image' : 'file-text', 'class' => 'h-4 w-4'])
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-[12px] font-bold text-bark-800" title="{{ $file?->original_name }}">{{ $file?->original_name ?? 'Berkas Lampiran' }}</p>
                                                    <p class="text-[11px] text-bark-400">{{ $sizeFormatted }}</p>
                                                </div>
                                                @if ($file)
                                                    <a href="{{ route('tentor.qualifix.file', $file->id) }}" target="_blank"
                                                       class="inline-flex items-center gap-1 rounded-lg border border-bark-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-bark-700 hover:bg-bark-100 transition shrink-0">
                                                        @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-3.5 w-3.5'])
                                                        Buka
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-1 text-[12px] text-bark-400 italic">Siswa tidak menyertakan berkas lampiran foto/dokumen.</p>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- Banner Klaim jika tiket ditugaskan ke tentor lain dan belum selesai --}}
                @if ($assignedTentors->isNotEmpty() && ! $isCurrentTentorAssigned && ! $isSelesai)
                    @php
                        $penugasanNama = $assignedTentors->pluck('full_name_with_title')->join(', ');
                    @endphp
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-sky-600 text-white">
                                @include('layouts.partials.icon', ['name' => 'users', 'class' => 'h-4 w-4'])
                            </div>
                            <div>
                                <p class="text-[13px] font-bold text-sky-900">Ditugaskan Kepada: {{ $penugasanNama }}</p>
                                <p class="text-[11.5px] text-sky-700">Anda dapat mengklaim tiket ini untuk ikut memberikan solusi kepada siswa.</p>
                            </div>
                        </div>
                        <form action="{{ route('tentor.qualifix.claim', $selectedTicket->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-lg bg-sky-700 px-3.5 py-2 text-[12px] font-bold text-white transition hover:bg-sky-800 shadow-sm">
                                Ambil &amp; Jawab Tiket
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Card 2: Jawaban Tentor (Riwayat Jawaban & Pembahasan) --}}
                @if ($sAnswers->isNotEmpty())
                    <section @class([
                        'rounded-2xl border bg-white shadow-sm',
                        'border-sky-200' => $isSelesai,
                        'border-emerald-200' => ! $isSelesai,
                    ])>
                        <header @class([
                            'flex items-center justify-between border-b px-6 py-4 rounded-t-2xl',
                            'border-sky-100 bg-sky-50/70' => $isSelesai,
                            'border-emerald-100 bg-emerald-50/70' => ! $isSelesai,
                        ])>
                            <div class="flex items-center gap-3">
                                <div @class([
                                    'grid h-8 w-8 place-items-center rounded-full text-white',
                                    'bg-sky-600' => $isSelesai,
                                    'bg-emerald-600' => ! $isSelesai,
                                ])>
                                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4 w-4'])
                                </div>
                                <div>
                                    <h3 @class([
                                        'text-[14px] font-bold',
                                        'text-sky-900' => $isSelesai,
                                        'text-emerald-900' => ! $isSelesai,
                                    ])>
                                        {{ $isSelesai ? 'Jawaban & Solusi (Selesai)' : 'Jawaban & Solusi dari Tentor' }}
                                    </h3>
                                    <p @class([
                                        'text-[11px]',
                                        'text-sky-700' => $isSelesai,
                                        'text-emerald-700' => ! $isSelesai,
                                    ])>
                                        {{ $isSelesai ? 'Sesi pembahasan telah selesai dan siswa telah memberikan rating.' : 'Jawaban dapat diedit oleh tentor penjawab, dan tentor lain dapat menambahkan jawaban.' }}
                                    </p>
                                </div>
                            </div>
                            <span @class([
                                'rounded-full px-2.5 py-1 text-[11px] font-bold',
                                'bg-sky-100 text-sky-800' => $isSelesai,
                                'bg-emerald-100 text-emerald-800' => ! $isSelesai,
                            ])>
                                {{ $sAnswers->count() }} Solusi Terkirim
                            </span>
                        </header>

                        <div class="divide-y divide-bark-100 p-6 space-y-6">
                            @foreach ($sAnswers as $answer)
                                @php
                                    $aStaff = $answer->staff;
                                    $aTentorName = $aStaff?->full_name_with_title ?? $aStaff?->user?->name ?? 'Tentor';
                                    $aJobTitle = $aStaff?->job_title ?? 'Master Tentor';
                                    $aTime = $answer->sent_at?->translatedFormat('d M Y, H:i') ?? $answer->created_at?->translatedFormat('d M Y, H:i');
                                    $aAtts = $answer->attachments;
                                    $isMyAnswer = $staffProfile && $answer->staff_id === $staffProfile->id;
                                    $canEditThisAnswer = $isMyAnswer && ! $isSelesai;
                                @endphp
                                <div class="space-y-3 pt-4 first:pt-0" id="answer-container-{{ $answer->id }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <div class="grid h-9 w-9 place-items-center rounded-full bg-bark-800 text-[12px] font-bold text-white">
                                                {{ Str::upper(Str::substr($aTentorName, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-[13px] font-bold text-bark-800">{{ $aTentorName }}</p>
                                                    @if ($isMyAnswer)
                                                        <span class="rounded bg-ember-100 text-ember-800 px-1.5 py-0.5 text-[9.5px] font-bold">Jawaban Anda</span>
                                                    @endif
                                                </div>
                                                <p class="text-[11px] text-bark-400">{{ $aJobTitle }} • {{ $aTime }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            {{-- Requirement 2: Jika sudah dijawab jawaban masih bisa di edit oleh tentor yang menjawab --}}
                                            @if ($canEditThisAnswer)
                                                <button type="button"
                                                        onclick="toggleEditAnswer({{ $answer->id }})"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-white px-3 py-1 text-[11.5px] font-bold text-emerald-700 transition hover:bg-emerald-50 shadow-sm">
                                                    @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-3.5 w-3.5'])
                                                    <span>Edit Jawaban</span>
                                                </button>
                                            @elseif ($isSelesai)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-bark-100 px-2.5 py-1 text-[10.5px] font-bold text-bark-600">
                                                    @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-3 w-3 text-sky-600'])
                                                    Terkunci (Selesai)
                                                </span>
                                            @endif

                                            <span @class([
                                                'rounded-full px-2.5 py-1 text-[10.5px] font-bold border',
                                                'bg-sky-50 text-sky-700 border-sky-200' => $isSelesai,
                                                'bg-emerald-50 text-emerald-700 border-emerald-200' => ! $isSelesai,
                                            ])>
                                                Terkirim
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Teks Jawaban Saat Ini --}}
                                    <div class="rounded-xl bg-bark-50 px-4 py-3.5 text-[13px] leading-relaxed text-bark-700 whitespace-pre-line border border-bark-100" id="answer-text-{{ $answer->id }}">
                                        {{ $answer->body }}
                                    </div>

                                    {{-- Form Edit Jawaban (Hanya muncul jika tentor pembuat klik tombol edit dan belum selesai) --}}
                                    @if ($canEditThisAnswer)
                                        <div id="edit-answer-form-{{ $answer->id }}" class="hidden rounded-xl border border-emerald-200 bg-emerald-50/40 p-4">
                                            <form action="{{ route('tentor.qualifix.answer.update', $answer->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <label class="block text-[12px] font-bold text-emerald-900 mb-1.5">
                                                    Edit Teks Jawaban / Solusi:
                                                </label>
                                                <textarea name="body"
                                                          rows="5"
                                                          required
                                                          class="w-full rounded-xl border border-bark-200 bg-white px-3.5 py-2.5 text-[12.5px] leading-relaxed text-bark-700 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">{{ old('body', $answer->body) }}</textarea>

                                                <div class="mt-3 flex items-center justify-between">
                                                    <button type="button"
                                                            onclick="toggleEditAnswer({{ $answer->id }})"
                                                            class="rounded-lg border border-bark-200 bg-white px-3.5 py-1.5 text-[11.5px] font-semibold text-bark-600 transition hover:bg-bark-50">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                            class="rounded-lg bg-emerald-600 px-4 py-1.5 text-[12px] font-bold text-white shadow-sm transition hover:bg-emerald-700">
                                                        Simpan Perubahan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- Lampiran Solusi dari Tentor --}}
                                    @if ($aAtts->isNotEmpty())
                                        <div class="space-y-2 pt-1">
                                            <p class="text-[11.5px] font-bold text-bark-600">Lampiran Solusi dari Tentor:</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                                @foreach ($aAtts as $aAtt)
                                                    @php
                                                        $aFile = $aAtt->file;
                                                        $isAImg = $aFile && in_array($aFile->mime_type, ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/gif']);
                                                    @endphp
                                                    <div class="rounded-lg border border-bark-200 bg-white p-2.5 flex items-center justify-between gap-2">
                                                        <div class="flex items-center gap-2 min-w-0">
                                                            @include('layouts.partials.icon', ['name' => $isAImg ? 'image' : 'file-text', 'class' => 'h-4 w-4 text-emerald-600 shrink-0'])
                                                            <span class="truncate text-[12px] font-medium text-bark-700" title="{{ $aFile?->original_name }}">{{ $aFile?->original_name ?? 'Berkas Solusi' }}</span>
                                                        </div>
                                                        @if ($aFile)
                                                            <a href="{{ route('tentor.qualifix.file', $aFile->id) }}" target="_blank"
                                                               class="text-[11px] font-bold text-emerald-700 hover:underline shrink-0">
                                                                Lihat
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Requirement 2: Jika selesai, maka siswa telah memberi rating dan jawaban tidak dapat diganti maupun dijawab oleh tentor lain --}}
                @if ($isSelesai)
                    <div class="rounded-2xl border border-sky-200 bg-sky-50/70 p-6 text-center shadow-sm">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-sky-100 text-sky-700 mb-3">
                            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-6 w-6'])
                        </div>
                        <h3 class="text-[15px] font-bold text-bark-800">Pertanyaan Ini Telah Selesai</h3>
                        <p class="mt-1 text-[12.5px] text-bark-600 max-w-md mx-auto">
                            Siswa telah memberikan rating dan menandai bahwa pembahasan ini telah selesai dipahami. Sesi telah ditutup sehingga jawaban tidak dapat diganti maupun ditambahkan lagi.
                        </p>
                    </div>
                @else
                    {{-- Card 3: Ruang Solusi Tentor (Form Kirim Jawaban Baru / Pembahasan Tambahan oleh Tentor Lain) --}}
                    <form action="{{ route('tentor.qualifix.answer', $selectedTicket->id) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          id="form-jawab-qualifix">
                        @csrf
                        <section class="rounded-2xl border border-bark-100 bg-white shadow-sm">
                            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-100 px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-bark-800 text-white">
                                        @include('layouts.partials.icon', ['name' => 'pen-line', 'class' => 'h-4 w-4'])
                                    </div>
                                    <div>
                                        <h2 class="text-[15px] font-bold text-bark-800">
                                            {{ $isSudahDijawab ? 'Tambah Jawaban / Pembahasan Lanjutan' : 'Ruang Solusi Tentor' }}
                                        </h2>
                                        <p class="text-[11px] text-bark-400">
                                            {{ $isSudahDijawab ? 'Tentor lain atau Anda dapat menambahkan penjelasan untuk siswa ' . $sNama : 'Jawaban akan langsung diterima oleh ' . $sNama }}
                                        </p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-bark-100 px-2.5 py-1 text-[11px] font-bold text-bark-600">
                                    @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-3.5 w-3.5'])
                                    {{ $isSudahDijawab ? 'Tambah Tanggapan' : 'Tanggapi Soal' }}
                                </span>
                            </header>

                            <div class="space-y-5 px-6 py-5">
                                {{-- Teks jawaban --}}
                                <div>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <label for="solusi-teks" class="text-[12.5px] font-bold text-bark-700">
                                            Teks Penjelasan / Pembahasan <span class="text-rose-500">*</span>
                                        </label>
                                        <span class="text-[11px] text-bark-400">Wajib diisi</span>
                                    </div>
                                    <textarea id="solusi-teks"
                                              name="body"
                                              rows="6"
                                              required
                                              placeholder="Tuliskan langkah-langkah, konsep, atau rumus untuk memandu {{ $sNama }}..."
                                              class="mt-2 w-full rounded-xl border border-bark-200 bg-white px-4 py-3 text-[13px] leading-relaxed text-bark-700 focus:border-ember-300 focus:outline-none focus:ring-2 focus:ring-ember-100">{{ old('body') }}</textarea>
                                </div>

                                {{-- Unggah foto / berkas lampiran --}}
                                <div>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-[12.5px] font-bold text-bark-700">Unggah Foto Solusi / Coretan Rumus (Opsional)</span>
                                        <span class="text-[11px] text-bark-400">JPG, PNG, PDF maks. 5MB per berkas (hingga 5 berkas)</span>
                                    </div>

                                    {{-- Dropzone --}}
                                    <div id="dropzone-area"
                                         class="mt-2 rounded-xl border-2 border-dashed border-bark-200 bg-bark-50/40 px-4 py-5 text-center transition hover:border-ember-400 hover:bg-ember-50/20 cursor-pointer">
                                        @include('layouts.partials.icon', ['name' => 'image', 'class' => 'mx-auto h-7 w-7 text-bark-300'])
                                        <p class="mt-2 text-[12px] text-bark-600 font-medium">Klik atau seret foto pembahasan ke area ini</p>
                                        <p class="text-[11px] text-bark-400 mt-0.5">Mendukung format gambar dan dokumen penjelasan</p>

                                        {{-- File input --}}
                                        <input type="file"
                                               name="files[]"
                                               id="solusi-files"
                                               multiple
                                               class="hidden"
                                               accept=".jpg,.jpeg,.png,.pdf">

                                        <button type="button"
                                                id="btn-pilih-foto"
                                                class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-bark-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-bark-700 transition hover:bg-bark-50 shadow-sm">
                                            @include('layouts.partials.icon', ['name' => 'download', 'class' => 'h-3.5 w-3.5 rotate-180'])
                                            Pilih Foto / Berkas
                                        </button>
                                    </div>

                                    {{-- Preview files --}}
                                    <div id="selected-files-container" class="mt-3 space-y-2 hidden">
                                        <p class="text-[11.5px] font-bold text-bark-600">Berkas yang akan diunggah:</p>
                                        <div id="selected-files-list" class="grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Aksi --}}
                            <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-bark-100 px-6 py-4 bg-bark-50/30 rounded-b-2xl">
                                <a href="{{ route('tentor.qualifix', ['tab' => $activeTab]) }}"
                                   class="rounded-lg border border-bark-200 bg-white px-4 py-2 text-[12px] font-semibold text-bark-600 transition hover:bg-bark-50">
                                    Batal
                                </a>

                                <button type="submit"
                                        id="btn-kirim-jawaban"
                                        class="inline-flex items-center gap-2 rounded-lg bg-ember-500 px-5 py-2.5 text-[12.5px] font-bold text-white shadow-sm transition hover:bg-ember-600">
                                    @include('layouts.partials.icon', ['name' => 'send', 'class' => 'h-4 w-4'])
                                    <span>Kirim Jawaban ke Siswa</span>
                                </button>
                            </footer>
                        </section>
                    </form>

                    {{-- Tandai Selesai (Jika belum berstatus selesai) --}}
                    <div class="rounded-2xl border border-bark-100 bg-white p-4 flex items-center justify-between shadow-sm">
                        <div>
                            <p class="text-[13px] font-bold text-bark-800">Selesaikan Pembahasan Tiket Ini?</p>
                            <p class="text-[11.5px] text-bark-400">Tandai jika pembahasan telah tuntas dan siswa telah memahami solusi.</p>
                        </div>
                        <form action="{{ route('tentor.qualifix.resolve', $selectedTicket->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-[12px] font-bold text-white shadow-sm transition hover:bg-emerald-700">
                                Tandai Selesai
                            </button>
                        </form>
                    </div>
                @endif
            @else
                {{-- Kosong --}}
                <section class="rounded-2xl border border-bark-100 bg-white p-12 text-center shadow-sm">
                    <div class="grid h-16 w-16 place-items-center rounded-full bg-bark-100 mx-auto mb-4 text-bark-300">
                        @include('layouts.partials.icon', ['name' => 'message-circle', 'class' => 'h-8 w-8'])
                    </div>
                    <h3 class="text-[15px] font-bold text-bark-800">Belum Ada Antrean</h3>
                    <p class="mt-1 text-[13px] text-bark-400">Tidak ada pertanyaan Qualifix pada kategori ini saat ini.</p>
                </section>
            @endif
        </div>
    </div>
</div>

<x-flash-toast />

@push('scripts')
<script>
function toggleEditAnswer(answerId) {
    const form = document.getElementById('edit-answer-form-' + answerId);
    if (form) {
        form.classList.toggle('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('solusi-files');
    const btnPilihFoto = document.getElementById('btn-pilih-foto');
    const dropzone = document.getElementById('dropzone-area');
    const filesContainer = document.getElementById('selected-files-container');
    const filesList = document.getElementById('selected-files-list');

    let dt = new DataTransfer();

    // Trigger file picker saat tombol 'Pilih Foto' atau dropzone diklik
    if (btnPilihFoto && fileInput) {
        btnPilihFoto.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.click();
        });
    }

    if (dropzone && fileInput) {
        dropzone.addEventListener('click', function (e) {
            if (e.target !== btnPilihFoto && !btnPilihFoto.contains(e.target)) {
                fileInput.click();
            }
        });

        // Drag & drop support
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-ember-400', 'bg-ember-50/40');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-ember-400', 'bg-ember-50/40');
            }, false);
        });

        dropzone.addEventListener('drop', function (e) {
            const droppedFiles = e.dataTransfer.files;
            if (droppedFiles.length > 0) {
                appendFiles(droppedFiles);
            }
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            appendFiles(fileInput.files);
        });
    }

    function appendFiles(newFiles) {
        for (let i = 0; i < newFiles.length; i++) {
            const file = newFiles[i];

            // Validasi ukuran (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Berkas ' + file.name + ' melebihi ukuran maksimum 5MB.');
                continue;
            }

            // Batas maksimal 5 berkas
            if (dt.items.length >= 5) {
                alert('Maksimal lampiran adalah 5 berkas.');
                break;
            }

            dt.items.add(file);
        }

        fileInput.files = dt.files;
        renderFileList();
    }

    function renderFileList() {
        if (!filesContainer || !filesList) return;

        filesList.innerHTML = '';

        if (dt.files.length === 0) {
            filesContainer.classList.add('hidden');
            return;
        }

        filesContainer.classList.remove('hidden');

        Array.from(dt.files).forEach((file, index) => {
            const isImage = file.type.startsWith('image/');
            const sizeStr = (file.size / 1024).toFixed(1) + ' KB';

            const card = document.createElement('div');
            card.className = 'flex items-center justify-between gap-2 rounded-lg border border-bark-200 bg-white p-2.5 shadow-sm';

            const left = document.createElement('div');
            left.className = 'flex items-center gap-2 min-w-0';

            if (isImage) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'h-9 w-9 rounded object-cover border border-bark-100 shrink-0';
                left.appendChild(img);
            } else {
                const iconBox = document.createElement('div');
                iconBox.className = 'grid h-9 w-9 shrink-0 place-items-center rounded bg-bark-100 text-[11px] font-bold text-bark-600';
                iconBox.innerText = 'PDF';
                left.appendChild(iconBox);
            }

            const info = document.createElement('div');
            info.className = 'min-w-0 flex-1';

            const name = document.createElement('p');
            name.className = 'truncate text-[12px] font-bold text-bark-800';
            name.title = file.name;
            name.innerText = file.name;

            const size = document.createElement('p');
            size.className = 'text-[10px] text-bark-400';
            size.innerText = sizeStr;

            info.appendChild(name);
            info.appendChild(size);
            left.appendChild(info);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'grid h-6 w-6 shrink-0 place-items-center rounded text-bark-400 hover:bg-rose-50 hover:text-rose-600 transition text-[13px] font-bold';
            removeBtn.innerText = '✕';
            removeBtn.title = 'Hapus berkas ini';
            removeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                removeFile(index);
            });

            card.appendChild(left);
            card.appendChild(removeBtn);
            filesList.appendChild(card);
        });
    }

    function removeFile(index) {
        const newDt = new DataTransfer();
        Array.from(dt.files).forEach((file, i) => {
            if (i !== index) {
                newDt.items.add(file);
            }
        });
        dt = newDt;
        fileInput.files = dt.files;
        renderFileList();
    }
});
</script>
@endpush
@endsection
