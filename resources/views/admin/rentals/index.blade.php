@extends('layouts.app')

@section('title', 'Property Rentals')
@section('header', 'Property Rentals')
@section('subheader', 'Track active and historical lease agreements.')

@section('actions')
    <a href="{{ route('admin.rentals.create') }}" class="btn-accent text-xs">+ Rental</a>
@endsection

@section('content')
<div class="rounded-3xl bg-white shadow-soft ring-1 ring-ink-100">
    <form method="GET" class="flex flex-wrap items-center gap-3 border-b border-ink-100 p-4">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search tenant or property…" class="flex-1 min-w-[200px] rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
        <select name="status" class="rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">All Statuses</option>
            @foreach($statusLabels as $k => $l)
                <option value="{{ $k }}" @selected($filters['status'] === $k)>{{ $l }}</option>
            @endforeach
        </select>
        <button class="btn-primary text-xs">Filter</button>
        @if(array_filter($filters))
            <a href="{{ route('admin.rentals.index') }}" class="text-xs text-ink-500 hover:text-ink-700">Reset</a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-ink-100 text-sm">
            <thead class="bg-ink-50 text-left text-xs font-semibold uppercase tracking-wider text-ink-500">
                <tr>
                    <th class="px-5 py-3">Tenant</th>
                    <th class="px-5 py-3">Property</th>
                    <th class="px-5 py-3">Period</th>
                    <th class="px-5 py-3">Monthly Rent</th>
                    <th class="px-5 py-3">Cycle</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse($rentals as $r)
                    <tr class="hover:bg-ink-50/60">
                        <td class="px-5 py-3">
                            <p class="font-medium text-ink-800">{{ $r->tenant_name }}</p>
                            <p class="text-xs text-ink-500">{{ $r->tenant_email ?: $r->tenant_phone ?: '—' }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('properties.show', $r->property) }}" class="font-medium text-ink-800 hover:text-brand-600">{{ $r->property->name }}</a>
                            <p class="font-mono text-xs text-ink-500">{{ $r->property->reference_no }}</p>
                        </td>
                        <td class="px-5 py-3 text-xs text-ink-600">{{ $r->start_date->format('d M Y') }} → {{ $r->end_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 font-medium text-ink-900">{{ $r->currency }} {{ number_format((float)$r->monthly_rent, 0, '.', ',') }}</td>
                        <td class="px-5 py-3 text-ink-600">{{ $r->cycle_label }}</td>
                        <td class="px-5 py-3"><x-status-pill :status="$r->status" /></td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex justify-end gap-3 text-xs">
                                <a href="{{ route('admin.rentals.edit', $r) }}" class="font-medium text-brand-600 hover:text-brand-700">Edit</a>
                                <form method="POST" action="{{ route('admin.rentals.destroy', $r) }}" onsubmit="return confirm('Delete this rental?')">
                                    @csrf @method('DELETE')
                                    <button class="font-medium text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-ink-500">No rentals recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-ink-100 px-5 py-3">
        {{ $rentals->links() }}
    </div>
</div>
@endsection
