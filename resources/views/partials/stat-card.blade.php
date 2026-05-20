@php
    $accent = $accent ?? 'mint';
    $bg = [
        'mint'   => 'bg-brand-100 text-brand-700',
        'lime'   => 'bg-brand-100 text-brand-700',
        'sky'    => 'bg-sky-100 text-sky-700',
        'amber'  => 'bg-amber-100 text-amber-700',
        'rose'   => 'bg-rose-100 text-rose-700',
        'violet' => 'bg-violet-100 text-violet-700',
    ][$accent] ?? 'bg-brand-100 text-brand-700';
@endphp
<div class="rounded-3xl bg-white p-5 ring-1 ring-ink-100 shadow-soft">
    <div class="flex items-start justify-between">
        <div class="grid h-10 w-10 place-items-center rounded-xl {{ $bg }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
            </svg>
        </div>
        @if(!empty($foot))
            <span class="rounded-full bg-ink-50 px-2 py-0.5 text-[10px] font-semibold text-ink-500 ring-1 ring-ink-100">{{ $foot }}</span>
        @endif
    </div>
    <div class="mt-4">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</p>
        <p class="mt-1 font-display text-xl font-bold text-ink-900">{{ $value }}</p>
    </div>
</div>
