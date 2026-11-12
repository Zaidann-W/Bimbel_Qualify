@props(['message' => null])

@php
    $pesan = $message ?? session('sukses') ?? session('status');
@endphp

@if ($pesan)
    <div data-flash-toast
         class="fixed bottom-4 left-4 z-50 w-[calc(100%-2rem)] max-w-sm rounded-xl border border-line bg-white px-4 py-3 shadow-lg transition duration-300 ease-out opacity-100 lg:left-[264px]"
         role="status"
         aria-live="polite">
        <div class="flex items-start gap-2.5">
            <span class="mt-0.5 shrink-0 text-bronze">
                @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'h-4 w-4'])
            </span>
            <p class="flex-1 text-[12.5px] font-semibold leading-snug text-ink">{{ $pesan }}</p>
            <button type="button" data-flash-close aria-label="Tutup notifikasi"
                    class="grid h-6 w-6 shrink-0 place-items-center rounded-md text-bark-400 transition hover:bg-shell hover:text-ink">
                @include('layouts.partials.icon', ['name' => 'x', 'class' => 'h-4 w-4'])
            </button>
        </div>
    </div>

    <script>
        (() => {
            const toast = document.querySelector('[data-flash-toast]');

            if (! toast) {
                return;
            }

            const tutup = () => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            };

            toast.querySelector('[data-flash-close]')?.addEventListener('click', tutup);
            setTimeout(tutup, 5000);
        })();
    </script>
@endif
