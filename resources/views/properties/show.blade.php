@extends('layouts.app')

@section('title', $property->name)
@section('header', $property->name)
@section('subheader', $property->reference_no . ' · ' . ($property->city ?: $property->country))

@section('actions')
    <a href="{{ route('properties.edit', $property) }}" class="btn-ghost text-xs">Edit</a>
    <form method="POST" action="{{ route('properties.destroy', $property) }}" onsubmit="return confirm('Delete this property?')">
        @csrf @method('DELETE')
        <button class="inline-flex items-center gap-1 rounded-full bg-white px-4 py-2 text-xs font-semibold text-rose-600 ring-1 ring-rose-200 hover:bg-rose-50">Delete</button>
    </form>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-ink-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-base font-semibold text-ink-900">Property Details</h2>
                    <p class="text-xs text-ink-500">All structural and locational data.</p>
                </div>
                <x-status-pill :status="$property->status" class="text-xs" />
            </div>
            <dl class="mt-5 grid grid-cols-2 gap-4 text-sm md:grid-cols-3">
                <div><dt class="text-xs text-ink-500">Type</dt><dd class="font-medium text-ink-800">{{ $property->type_label }}</dd></div>
                <div><dt class="text-xs text-ink-500">Land Area</dt><dd class="font-medium text-ink-800">{{ $property->land_area ? number_format((float)$property->land_area).' m²' : '—' }}</dd></div>
                <div><dt class="text-xs text-ink-500">Building Area</dt><dd class="font-medium text-ink-800">{{ $property->building_area ? number_format((float)$property->building_area).' m²' : '—' }}</dd></div>
                <div><dt class="text-xs text-ink-500">Bedrooms</dt><dd class="font-medium text-ink-800">{{ $property->bedrooms ?? '—' }}</dd></div>
                <div><dt class="text-xs text-ink-500">Bathrooms</dt><dd class="font-medium text-ink-800">{{ $property->bathrooms ?? '—' }}</dd></div>
                <div><dt class="text-xs text-ink-500">Year Built</dt><dd class="font-medium text-ink-800">{{ $property->year_built ?? '—' }}</dd></div>
                <div class="col-span-2 md:col-span-3">
                    <dt class="text-xs text-ink-500">Address</dt>
                    <dd class="font-medium text-ink-800">{{ $property->address }}</dd>
                    <p class="text-xs text-ink-500">{{ collect([$property->city, $property->state, $property->postal_code, $property->country])->filter()->join(', ') }}</p>
                </div>
                @if($property->description)
                    <div class="col-span-2 md:col-span-3">
                        <dt class="text-xs text-ink-500">Description</dt>
                        <dd class="text-ink-700">{{ $property->description }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="rounded-3xl bg-white shadow-soft ring-1 ring-ink-100">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-ink-900">Valuation History</h2>
                    <p class="text-xs text-ink-500">{{ $property->valuations->count() }} record(s)</p>
                </div>
                <a href="{{ route('admin.valuations.create', ['property_id' => $property->id]) }}" class="btn-accent text-xs">+ Valuation</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-ink-100 text-sm">
                    <thead class="bg-ink-50 text-left text-xs font-semibold uppercase tracking-wider text-ink-500">
                        <tr><th class="px-5 py-3">Date</th><th class="px-5 py-3">Market Value</th><th class="px-5 py-3">Method</th><th class="px-5 py-3">Valuer</th></tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @forelse($property->valuations as $v)
                            <tr>
                                <td class="px-5 py-3 text-ink-700">{{ $v->valuation_date->format('Y-m-d') }}</td>
                                <td class="px-5 py-3 font-medium text-ink-900">{{ $v->currency }} {{ number_format((float)$v->market_value, 0, '.', ',') }}</td>
                                <td class="px-5 py-3 text-ink-600">{{ $v->method_label }}</td>
                                <td class="px-5 py-3 text-ink-600">{{ $v->valuer_name ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-ink-500">No valuation records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl bg-white shadow-soft ring-1 ring-ink-100">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-ink-900">Rental History</h2>
                    <p class="text-xs text-ink-500">{{ $property->rentals->count() }} record(s)</p>
                </div>
                <a href="{{ route('admin.rentals.create', ['property_id' => $property->id]) }}" class="btn-accent text-xs">+ Rental</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-ink-100 text-sm">
                    <thead class="bg-ink-50 text-left text-xs font-semibold uppercase tracking-wider text-ink-500">
                        <tr><th class="px-5 py-3">Tenant</th><th class="px-5 py-3">Period</th><th class="px-5 py-3">Monthly Rent</th><th class="px-5 py-3">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @forelse($property->rentals as $r)
                            <tr>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-ink-800">{{ $r->tenant_name }}</p>
                                    <p class="text-xs text-ink-500">{{ $r->tenant_email ?: $r->tenant_phone ?: '—' }}</p>
                                </td>
                                <td class="px-5 py-3 text-xs text-ink-600">{{ $r->start_date->format('M Y') }} → {{ $r->end_date->format('M Y') }}</td>
                                <td class="px-5 py-3 font-medium text-ink-900">{{ $r->currency }} {{ number_format((float)$r->monthly_rent, 0, '.', ',') }}</td>
                                <td class="px-5 py-3"><x-status-pill :status="$r->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-ink-500">No rental records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-ink-100">
            <h2 class="text-sm font-semibold text-ink-900">Spatial Boundary</h2>
            <p class="text-xs text-ink-500">PostGIS polygon (SRID 4326).</p>
            <div id="property-map" class="mt-3 h-[400px] w-full overflow-hidden rounded-lg border border-ink-200"></div>
        </div>

        <div class="rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 p-5 text-white shadow-soft">
            <p class="text-xs uppercase tracking-wider text-brand-200">Latest Market Value</p>
            @if($latest = $property->valuations->first())
                <p class="mt-1 text-xl font-semibold">{{ $latest->currency }} {{ number_format((float)$latest->market_value, 0, '.', ',') }}</p>
                <p class="mt-1 text-xs text-brand-200">{{ $latest->valuation_date->format('d M Y') }} · {{ $latest->method_label }}</p>
            @else
                <p class="mt-1 text-base font-semibold">No valuation yet</p>
                <a href="{{ route('admin.valuations.create', ['property_id' => $property->id]) }}" class="mt-2 inline-block rounded-md bg-white/15 px-3 py-1.5 text-xs hover:bg-white/25">Record valuation</a>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.__propertyMap = { geoJson: @json($geoJson) };
</script>
@endpush
