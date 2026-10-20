@props(['name', 'class' => ''])

@php
    $paths = [
        'layout-grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'clipboard-check' => '<path d="M9 4h6a1 1 0 0 1 1 1v1H8V5a1 1 0 0 1 1-1Z"/><path d="M8 5H6.5A1.5 1.5 0 0 0 5 6.5v12A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 17.5 5H16"/><path d="m9 13 2 2 4-4"/>',
        'message-circle' => '<path d="M20 11.5a7.5 7.5 0 0 1-10.9 6.7L4 20l1.8-4.6A7.5 7.5 0 1 1 20 11.5Z"/>',
        'book-open' => '<path d="M12 6.5C10.5 5.2 8.4 4.5 5.5 4.5H4v12h1.5c2.9 0 5 .7 6.5 2 1.5-1.3 3.6-2 6.5-2H20v-12h-1.5c-2.9 0-5 .7-6.5 2Z"/><path d="M12 6.5v12"/>',
        'users' => '<circle cx="9" cy="8" r="3"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/><path d="M16 5.5a3 3 0 0 1 0 5.9"/><path d="M17.5 13.6A5.5 5.5 0 0 1 20.5 19"/>',
        'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
        'calendar' => '<rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3.5V6M16 3.5V6"/>',
        'user-check' => '<circle cx="10" cy="8" r="3.2"/><path d="M4 19.5a6.2 6.2 0 0 1 12.4 0"/><path d="m16.5 12.5 2 2 3.5-3.5"/>',
        'help-circle' => '<circle cx="12" cy="12" r="8.5"/><path d="M9.8 9.6a2.3 2.3 0 1 1 3.1 2.2c-.6.3-.9.8-.9 1.4v.3"/><path d="M12 16.6h.01"/>',
        'siren' => '<path d="M8 17V11a4 4 0 1 1 8 0v6"/><rect x="5" y="17" width="14" height="3" rx="1.5"/><path d="M12 3v2M5.5 5.5 7 7M18.5 5.5 17 7"/>',
        'chevron-right' => '<path d="m9 6 6 6-6 6"/>',
        'pen-line' => '<path d="M4 20h4l10-10a2.5 2.5 0 0 0-3.5-3.5L4 16.5V20Z"/><path d="m14 7 3 3"/>',
        'folder-open' => '<path d="M3.5 7.5A2 2 0 0 1 5.5 5.5h3.4a2 2 0 0 1 1.4.6l1 1a2 2 0 0 0 1.4.6h4.8a2 2 0 0 1 2 2v1"/><path d="M3.5 9.5h15.8a1.4 1.4 0 0 1 1.3 1.9l-2.4 6a1.8 1.8 0 0 1-1.7 1.1H5.5a2 2 0 0 1-2-2v-7Z"/>',
        'graduation-cap' => '<path d="m12 4 9 4.5-9 4.5-9-4.5L12 4Z"/><path d="M7 11v4c0 1.2 2.2 2.5 5 2.5s5-1.3 5-2.5v-4"/>',
        'flame' => '<path d="M12 21c3.6 0 6-2.3 6-5.4 0-3.6-3.3-5.2-3.3-8.6 0-1-.3-2-1.2-3-.2 2.2-1.3 3.3-2.3 4.4-1.2 1.3-2.6 2.7-2.6 5.1C8.6 16.4 9.6 21 12 21Z"/>',
        'tag' => '<path d="M4 11.5V5.5A1.5 1.5 0 0 1 5.5 4h6l8.5 8.5a1.5 1.5 0 0 1 0 2.1l-6.4 6.4a1.5 1.5 0 0 1-2.1 0L4 11.5Z"/><circle cx="8.5" cy="8.5" r="1.2"/>',
        'map-pin' => '<path d="M12 21s6.5-5.6 6.5-10.4A6.5 6.5 0 0 0 5.5 10.6C5.5 15.4 12 21 12 21Z"/><circle cx="12" cy="10.4" r="2.3"/>',
        'user-plus' => '<circle cx="10" cy="8" r="3.2"/><path d="M4 19.5a6.2 6.2 0 0 1 12.4 0"/><path d="M18.5 8v5M16 10.5h5"/>',
        'info' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 11v5"/><path d="M12 8h.01"/>',
        'log-in' => '<path d="M14 8V6.5A1.5 1.5 0 0 0 12.5 5h-6A1.5 1.5 0 0 0 5 6.5v11A1.5 1.5 0 0 0 6.5 19h6a1.5 1.5 0 0 0 1.5-1.5V16"/><path d="M10 12h10m0 0-3-3m3 3-3 3"/>',
        'mail' => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4.5 7.5 7.5 5.5 7.5-5.5"/>',
        'phone' => '<path d="M7 3.5h2.2l1.5 3.7-2 1.4a11 11 0 0 0 5.7 5.7l1.4-2 3.7 1.5V16a2.5 2.5 0 0 1-2.7 2.5A14.5 14.5 0 0 1 4.5 6.2 2.5 2.5 0 0 1 7 3.5Z"/>',
        'award' => '<circle cx="12" cy="9" r="5"/><path d="m8.5 13.5-1.5 7 5-2.5 5 2.5-1.5-7"/>',
        'loader' => '<path d="M12 3.5v3M12 17.5v3M3.5 12h3M17.5 12h3M6 6l2 2M16 16l2 2M18 6l-2 2M8 16l-2 2"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'h-4 w-4 shrink-0 ' . $class]) }}
     fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
     viewBox="0 0 24 24" aria-hidden="true">
    {!! $paths[$name] ?? '' !!}
</svg>
