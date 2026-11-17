@props(['session', 'highlight' => false])

@php
    $dayLabel = $highlight
        ? 'Hari Ini'
        : $session->date->locale('id')->translatedFormat('D');

    $timeLabel = $session->start_time->format('H:i').' – '.$session->end_time->format('H:i');

    $tutorLabel = trim(explode(',', $session->tutor_name ?? '')[0]);

    $roomLabel = $session->room_base_name;
    if ($session->room_floor_label !== null) {
        $roomLabel .= ', '.str_replace('Lantai', 'Lt.', trim($session->room_floor_label, '()'));
    }

    $metaLabel = implode(' · ', array_filter([$timeLabel, $tutorLabel, $roomLabel]));
@endphp

<a href="{{ route('siswa.jadwal.show', $session) }}"
   @class([
       'group flex items-center gap-4 rounded-2xl border px-4 py-5 transition sm:px-5 sm:gap-5',
       'border-gold' => $highlight,
       'border-bark-100 bg-white hover:border-bark-200' => ! $highlight,
   ])>
    <span @class([
        'grid h-14 shrink-0 place-items-center rounded-xl text-center',
        'w-16 bg-gold text-espresso' => $highlight,
        'w-14 bg-bark-50 text-bark-600' => ! $highlight,
    ])>
        <span class="block whitespace-nowrap text-[9px] font-bold uppercase leading-none tracking-wide">{{ $dayLabel }}</span>
        <span class="mt-1.5 block text-[18px] font-extrabold leading-none">{{ $session->date->translatedFormat('j') }}</span>
    </span>

    <span class="min-w-0 flex-1">
        <span class="block text-[16px] font-bold leading-snug text-bark-800 sm:text-[18px]">{{ $session->title }}</span>
        <span class="mt-1.5 block text-[12.5px] leading-relaxed text-bark-400">{{ $metaLabel }}</span>
    </span>

    @if ($highlight)
        <span class="hidden shrink-0 items-center gap-1.5 rounded-lg bg-espresso px-4 py-2.5 text-[12.5px] font-bold text-white transition group-hover:bg-bark-900 sm:inline-flex">
            Lihat detail
            @include('layouts.partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5'])
        </span>
    @endif

    @include('layouts.partials.icon', [
        'name' => 'chevron-right',
        'class' => 'h-4 w-4 shrink-0 text-bark-300 transition group-hover:text-bark-500'.($highlight ? ' sm:hidden' : ''),
    ])
</a>
