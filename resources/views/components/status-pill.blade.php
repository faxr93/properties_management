@props(['status' => 'available'])
@php
    $palette = [
        'available'    => 'bg-brand-100 text-brand-800 ring-brand-200',
        'occupied'     => 'bg-sky-100 text-sky-700 ring-sky-200',
        'under_review' => 'bg-amber-100 text-amber-700 ring-amber-200',
        'inactive'     => 'bg-ink-100 text-ink-600 ring-ink-200',
        'active'       => 'bg-brand-100 text-brand-800 ring-brand-200',
        'pending'      => 'bg-amber-100 text-amber-700 ring-amber-200',
        'completed'    => 'bg-ink-100 text-ink-600 ring-ink-200',
        'cancelled'    => 'bg-rose-100 text-rose-700 ring-rose-200',
    ];
    $classes = $palette[$status] ?? 'bg-ink-100 text-ink-600 ring-ink-200';
    $labels = array_merge(\App\Models\Property::statusLabels(), \App\Models\PropertyRental::statusLabels());
    $label = $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-semibold ring-1 ring-inset {$classes}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    {{ $label }}
</span>
