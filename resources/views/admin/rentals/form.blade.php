@extends('layouts.app')

@section('title', $rental->exists ? 'Edit Rental' : 'New Rental')
@section('header', $rental->exists ? 'Edit Rental' : 'New Rental')

@php $action = $rental->exists ? route('admin.rentals.update', $rental) : route('admin.rentals.store'); @endphp

@section('content')
<form method="POST" action="{{ $action }}" class="max-w-3xl space-y-6">
    @csrf
    @if($rental->exists) @method('PUT') @endif

    <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-ink-100">
        <h2 class="text-sm font-semibold text-ink-900">Lease Details</h2>

        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-xs font-medium text-ink-600">Property</label>
                <select name="property_id" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">— Select property —</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected(old('property_id', $rental->property_id) == $p->id)>{{ $p->reference_no }} · {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Tenant Name</label>
                <input type="text" name="tenant_name" value="{{ old('tenant_name', $rental->tenant_name) }}" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Status</label>
                <select name="status" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @foreach($statusLabels as $k => $l)
                        <option value="{{ $k }}" @selected(old('status', $rental->status) === $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Tenant Email</label>
                <input type="email" name="tenant_email" value="{{ old('tenant_email', $rental->tenant_email) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Tenant Phone</label>
                <input type="text" name="tenant_phone" value="{{ old('tenant_phone', $rental->tenant_phone) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date', optional($rental->start_date)->format('Y-m-d') ?? $rental->start_date) }}" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date', optional($rental->end_date)->format('Y-m-d') ?? $rental->end_date) }}" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Monthly Rent</label>
                <input type="number" step="0.01" min="0" name="monthly_rent" value="{{ old('monthly_rent', $rental->monthly_rent) }}" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Deposit</label>
                <input type="number" step="0.01" min="0" name="deposit" value="{{ old('deposit', $rental->deposit) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', $rental->currency ?? 'MYR') }}" maxlength="3" class="mt-1 w-full rounded-md border-ink-200 text-sm uppercase focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="text-xs font-medium text-ink-600">Payment Cycle</label>
                <select name="payment_cycle" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @foreach($cycleLabels as $k => $l)
                        <option value="{{ $k }}" @selected(old('payment_cycle', $rental->payment_cycle) === $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-medium text-ink-600">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('notes', $rental->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-accent">{{ $rental->exists ? 'Save Changes' : 'Create Rental' }}</button>
        <a href="{{ route('admin.rentals.index') }}" class="text-sm text-ink-500 hover:text-ink-700">Cancel</a>
    </div>
</form>
@endsection
