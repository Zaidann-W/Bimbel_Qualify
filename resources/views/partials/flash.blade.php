{{-- Pesan sukses & error dari flash session untuk halaman publik. --}}
@if (session('success'))
    <div class="mx-auto max-w-[1400px] px-6 pt-6 lg:px-12">
        <div class="flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[14px] font-semibold text-emerald-700">
            @include('partials.icon', ['name' => 'check-circle', 'size' => 16, 'class' => 'mt-0.5 h-4 w-4 shrink-0'])
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="mx-auto max-w-[1400px] px-6 pt-6 lg:px-12">
        <div class="rounded-xl border border-ember-100 bg-ember-50 px-4 py-3 text-[14px] font-semibold text-ember-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
