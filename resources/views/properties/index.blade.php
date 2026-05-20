@extends('layouts.app')

@section('title', 'Properties')
@section('header', 'Properties')
@section('subheader', 'Search, filter and manage your portfolio.')

@section('actions')
    <a href="{{ route('properties.create') }}" class="btn-accent text-xs">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd"/></svg>
        New Property
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-3xl bg-white shadow-soft ring-1 ring-ink-100">
        <form method="GET" class="flex flex-wrap items-center gap-3 border-b border-ink-100 p-4">
            <div class="relative flex-1 min-w-[200px]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search by name, reference, city…"
                       class="w-full rounded-md border-ink-200 pl-9 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <select name="type" class="rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">All Types</option>
                @foreach($typeLabels as $k => $l)
                    <option value="{{ $k }}" @selected($filters['type'] === $k)>{{ $l }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">All Statuses</option>
                @foreach($statusLabels as $k => $l)
                    <option value="{{ $k }}" @selected($filters['status'] === $k)>{{ $l }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-xs">Filter</button>
            @if(array_filter($filters))
                <a href="{{ route('properties.index') }}" class="text-xs text-ink-500 hover:text-ink-700">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-ink-100 text-sm">
                <thead class="bg-ink-50 text-left text-xs font-semibold uppercase tracking-wider text-ink-500">
                    <tr>
                        <th class="px-5 py-3">Reference</th>
                        <th class="px-5 py-3">Property</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Land/Building</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse($properties as $p)
                        <tr class="hover:bg-ink-50/60">
                            <td class="px-5 py-3 font-mono text-xs text-ink-600">{{ $p->reference_no }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('properties.show', $p) }}" class="font-medium text-ink-900 hover:text-brand-600">{{ $p->name }}</a>
                                <p class="text-xs text-ink-500">{{ $p->city ?: '—' }}{{ $p->state ? ', '.$p->state : '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-ink-600">{{ $typeLabels[$p->type] ?? $p->type }}</td>
                            <td class="px-5 py-3"><x-status-pill :status="$p->status" /></td>
                            <td class="px-5 py-3 text-xs text-ink-600">
                                {{ $p->land_area ? number_format((float)$p->land_area, 0).' m²' : '—' }}
                                <span class="text-ink-400">/</span>
                                {{ $p->building_area ? number_format((float)$p->building_area, 0).' m²' : '—' }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-3 text-xs">
                                    <a href="{{ route('properties.show', $p) }}" class="font-medium text-brand-600 hover:text-brand-700">View</a>
                                    <a href="{{ route('properties.edit', $p) }}" class="font-medium text-ink-500 hover:text-ink-700">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-ink-500">No properties match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-ink-100 px-5 py-3">
            {{ $properties->links() }}
        </div>
    </div>

    <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-ink-100">
        <h2 class="text-sm font-semibold text-ink-900">Spatial Overview</h2>
        <p class="text-xs text-ink-500">Polygons of currently filtered properties.</p>
        <div id="properties-map" class="mt-3 h-[460px] w-full overflow-hidden rounded-lg border border-ink-200"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.__propertiesMap = { geoJson: @json($geoJson) };
</script>
@endpush
