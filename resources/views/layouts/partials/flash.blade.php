{{-- Pesan sukses & error dari flash session, dipakai di seluruh halaman admin. --}}
@if (session('success'))
    <div class="px-4 pt-4 sm:px-6">
        <div class="flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[13px] font-semibold text-emerald-700">
            @include('layouts.partials.icon', ['name' => 'check-circle', 'class' => 'mt-px h-4 w-4 shrink-0'])
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="px-4 pt-4 sm:px-6">
        <div class="rounded-xl border border-ember-100 bg-ember-50 px-4 py-3 text-[13px] font-semibold text-ember-700">
            <ul class="list-disc space-y-1 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
