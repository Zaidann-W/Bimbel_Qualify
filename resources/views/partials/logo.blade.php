{{-- Pemanggilan: @include('partials.logo', ['size' => 44]) --}}
{{-- $variant: 'default' (hitam, untuk latar terang) | 'gold' (gradasi emas, untuk latar gelap) --}}
@php
    $size ??= 44;
    $variant ??= 'default';
    $uid ??= 'a';
    $subSize ??= 'text-[9.5px]';
    $titleSize ??= 'text-[19px]';
    $sub ??= 'Lembaga Bimbingan Belajar';
    $subWrap ??= 'max-w-[132px]';
    $stack ??= false;
@endphp
<div class="{{ $stack ? 'flex flex-col items-center gap-3 text-center' : 'flex items-center gap-3' }}">
    <svg
        width="{{ $size }}"
        height="{{ $size }}"
        viewBox="0 0 72 72"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        class="shrink-0"
        role="img"
        aria-label="Logo Bimbel QUALIFY"
    >
        <defs>
            <linearGradient id="qualify-gold-{{ $uid }}" x1="8%" y1="0%" x2="92%" y2="100%">
                <stop offset="0%" stop-color="#F9DE8C" />
                <stop offset="45%" stop-color="#E3AC3C" />
                <stop offset="100%" stop-color="#A9711A" />
            </linearGradient>
        </defs>
        {{-- Mahkota --}}
        <circle cx="13.5" cy="12.5" r="3" fill="url(#qualify-gold-{{ $uid }})" />
        <circle cx="33.8" cy="7.6" r="3.2" fill="url(#qualify-gold-{{ $uid }})" />
        <circle cx="54.1" cy="12.5" r="3" fill="url(#qualify-gold-{{ $uid }})" />
        <path
            d="M14.2 24.8 18.4 14.1c.4-1 1.8-1.1 2.3-.1l5.9 9.4 6-11.8c.5-1 1.9-.9 2.3.1l6 11.8 5.9-9.4c.5-1 1.9-.9 2.3.1l4.2 10.7c.4 1-.3 2.1-1.4 2.1H15.6c-1.1 0-1.8-1.1-1.4-2.1Z"
            fill="url(#qualify-gold-{{ $uid }})"
        />
        {{-- Huruf Q --}}
        <circle cx="36" cy="47.5" r="13.6" stroke="url(#qualify-gold-{{ $uid }})" stroke-width="7" />
        <path
            d="M45.4 56.6 52.6 64"
            stroke="url(#qualify-gold-{{ $uid }})"
            stroke-width="7"
            stroke-linecap="round"
        />
    </svg>

    <div class="leading-none">
        <div
            class="{{ $titleSize }} font-extrabold tracking-[0.05em]
                @if ($variant === 'gold')
                    bg-gradient-to-b from-[#F9DE8C] via-[#E3AC3C] to-[#A9711A] bg-clip-text text-transparent
                @else
                    text-ink
                @endif"
        >
            QUALIFY
        </div>
        <div
            class="{{ $subSize }} font-semibold uppercase tracking-[0.13em] mt-1.5 {{ $subWrap }}
                @if ($variant === 'gold')
                    text-[#C9B48A]
                @else
                    text-muted
                @endif"
        >
            {{ $sub }}
        </div>
    </div>
</div>
