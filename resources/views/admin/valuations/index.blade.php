@extends('layouts.app')

@section('title', 'Property Valuations')
@section('header', 'Property Valuations')
@section('subheader', 'Administer valuation records across the portfolio.')

@section('actions')
    <a href="{{ route('admin.valuations.create') }}" class="btn-accent text-xs">+ Valuation</a>
@endsection

@section('content')
<div class="rounded-3xl bg-white shadow-soft ring-1 ring-ink-100">
    <form method="GET" class="flex flex-wrap items-center gap-3 border-b border-ink-100 p-4">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search property…" class="flex-1 min-w-[200px] rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
        <select name="method" class="rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">All Methods</option>
            @foreach($methodLabels as $k => $l)
                <option value="{{ $k }}" @selected($filters['method'] === $k)>{{ $l }}</option>
            @endforeach
        </select>
        <button class="btn-primary text-xs">Filter</button>
        @if(array_filter($filters))
            <a href="{{ route('admin.valuations.index') }}" class="text-xs text-ink-500 hover:text-ink-700">Reset</a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-ink-100 text-sm">
            <thead class="bg-ink-50 text-left text-xs font-semibold uppercase tracking-wider text-ink-500">
                <tr>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Property</th>
                    <th class="px-5 py-3">Market Value</th>
                    <th class="px-5 py-3">Method</th>
                    <th class="px-5 py-3">Valuer</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse($valuations as $v)
                    <tr class="hover:bg-ink-50/60">
                        <td class="px-5 py-3 text-ink-700">{{ $v->valuation_date->format('Y-m-d') }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('properties.show', $v->property) }}" class="font-medium text-ink-900 hover:text-brand-600">{{ $v->property->name }}</a>
                            <p class="font-mono text-xs text-ink-500">{{ $v->property->reference_no }}</p>
                        </td>
                        <td class="px-5 py-3 font-medium text-ink-900">{{ $v->currency }} {{ number_format((float)$v->market_value, 0, '.', ',') }}</td>
                        <td class="px-5 py-3 text-ink-600">{{ $v->method_label }}</td>
                        <td class="px-5 py-3 text-ink-600">{{ $v->valuer_name ?: '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex justify-end gap-3 text-xs">
                                <a href="{{ route('admin.valuations.edit', $v) }}" class="font-medium text-brand-600 hover:text-brand-700">Edit</a>
                                <form method="POST" action="{{ route('admin.valuations.destroy', $v) }}" onsubmit="return confirm('Delete this valuation?')">
                                    @csrf @method('DELETE')
                                    <button class="font-medium text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-ink-500">No valuations recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-ink-100 px-5 py-3">
        {{ $valuations->links() }}
    </div>
</div>
@endsection
