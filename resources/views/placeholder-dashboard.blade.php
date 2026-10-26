@extends('layouts.{{ $role }}')

@section('title', ucfirst($role) . ' Dashboard — Bimbel QUALIFY')

@section('content')
    <div class="mx-auto max-w-[1400px] px-6 lg:px-12 py-12">
        <div class="bg-paper rounded-[24px] border border-line/60 p-8 sm:p-12 shadow-[0_10px_30px_-22px_rgba(42,26,14,0.35)] text-center">
            {{-- Logo kecil --}}
            <div class="flex justify-center mb-6">
                @include('partials.logo', [
                    'size' => 60,
                    'variant' => 'gold',
                    'uid' => 'dashboard-' . $role,
                    'stack' => true,
                    'titleSize' => 'text-[22px]',
                    'sub' => 'Bimbel yang selalu Berqualitas',
                    'subWrap' => 'max-w-none',
                ])
            </div>

            <h1 class="text-[28px] sm:text-[32px] font-extrabold text-ink">
                Selamat Datang, {{ $user->name }}
            </h1>

            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gold/10 px-4 py-1.5 text-[13px] font-bold text-bronze">
                    @include('partials.icon', ['name' => 'shield', 'size' => 14])
                    <span class="capitalize">{{ $role }}</span>
                </span>

                @if ($user->status === 'aktif')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-forest-50 px-4 py-1.5 text-[13px] font-bold text-forest-700">
                        @include('partials.icon', ['name' => 'check-circle', 'size' => 14])
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-ember-50 px-4 py-1.5 text-[13px] font-bold text-ember-700">
                        @include('partials.icon', ['name' => 'alert-circle', 'size' => 14])
                        Nonaktif
                    </span>
                @endif
            </div>

            <p class="mt-6 text-[15px] text-muted max-w-xl mx-auto">
                Ini adalah halaman placeholder untuk dashboard <strong class="text-ink">{{ ucfirst($role) }}</strong>.
                Fungsionalitas penuh akan dikembangkan di tahap selanjutnya.
            </p>

            {{-- Logout form --}}
            <form action="{{ route('logout') }}" method="POST" class="mt-8">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl border border-line bg-paper px-6 py-3 text-[15px] font-semibold text-ink hover:bg-sand transition-colors"
                >
                    @include('partials.icon', ['name' => 'log-out', 'size' => 18])
                    Keluar
                </button>
            </form>

            {{-- Info tambahan untuk development --}}
            @if (app()->environment('local'))
                <details class="mt-8 text-left max-w-xl mx-auto">
                    <summary class="cursor-pointer text-[13px] text-muted hover:text-ink font-medium">
                        Info Debug (Development)
                    </summary>
                    <div class="mt-4 p-4 rounded-xl bg-sand text-[12px] font-mono text-ink/80 overflow-auto">
                        <div><strong>ID:</strong> {{ $user->id }}</div>
                        <div><strong>Email:</strong> {{ $user->email }}</div>
                        <div><strong>Phone:</strong> {{ $user->phone ?? '-' }}</div>
                        <div><strong>Role:</strong> {{ $user->role }}</div>
                        <div><strong>Status:</strong> {{ $user->status }}</div>
                        <div><strong>Last Login:</strong> {{ $user->last_login_at?->format('d M Y H:i') ?? 'Belum pernah' }}</div>
                        <div><strong>Created:</strong> {{ $user->created_at->format('d M Y H:i') }}</div>
                    </div>
                </details>
            @endif
        </div>
    </div>
@endsection