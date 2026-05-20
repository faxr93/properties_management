@extends('layouts.app')

@section('title', $valuation->exists ? 'Edit Valuation' : 'New Valuation')
@section('header', $valuation->exists ? 'Edit Valuation' : 'New Valuation')

@php $action = $valuation->exists ? route('admin.valuations.update', $valuation) : route('admin.valuations.store'); @endphp

@section('content')
<form method="POST" action="{{ $action }}" class="max-w-3xl space-y-6">
    @csrf
    @if($valuation->exists) @method('PUT') @endif

    <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-ink-100">
        <h2 class="text-sm font-semibold text-ink-900">Record Details</h2>

        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-xs font-medium text-ink-600">Property</label>
                <select name="property_id" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">— Select property —</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected(old('property_id', $valuation->property_id) == $p->id)>{{ $p->reference_no }} · {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Valuation Date</label>
                <input type="date" name="valuation_date" value="{{ old('valuation_date', optional($valuation->valuation_date)->format('Y-m-d') ?? $valuation->valuation_date) }}" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Method</label>
                <select name="method" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @foreach($methodLabels as $k => $l)
                        <option value="{{ $k }}" @selected(old('method', $valuation->method) === $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', $valuation->currency ?? 'MYR') }}" maxlength="3" class="mt-1 w-full rounded-md border-ink-200 text-sm uppercase focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Market Value</label>
                <input type="number" step="0.01" min="0" name="market_value" value="{{ old('market_value', $valuation->market_value) }}" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Land Value</label>
                <input type="number" step="0.01" min="0" name="land_value" value="{{ old('land_value', $valuation->land_value) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Building Value</label>
                <input type="number" step="0.01" min="0" name="building_value" value="{{ old('building_value', $valuation->building_value) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Valuer Name</label>
                <input type="text" name="valuer_name" value="{{ old('valuer_name', $valuation->valuer_name) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Valuer License</label>
                <input type="text" name="valuer_license" value="{{ old('valuer_license', $valuation->valuer_license) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-medium text-ink-600">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('notes', $valuation->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-accent">{{ $valuation->exists ? 'Save Changes' : 'Create Valuation' }}</button>
        <a href="{{ route('admin.valuations.index') }}" class="text-sm text-ink-500 hover:text-ink-700">Cancel</a>
    </div>
</form>
@endsection
