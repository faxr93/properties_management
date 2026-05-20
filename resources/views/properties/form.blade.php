@extends('layouts.app')

@section('title', $property->exists ? 'Edit Property' : 'New Property')
@section('header', $property->exists ? 'Edit Property' : 'New Property')
@section('subheader', 'Capture core information and draw the property boundary on the map.')

@php
    $action = $property->exists ? route('properties.update', $property) : route('properties.store');
    $boundaryGeo = $property->boundary; // already a GeoJSON array from JSONB cast
    $locationGeo = $property->location;
@endphp

@section('content')
<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if($property->exists) @method('PUT') @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-ink-100">
                <h2 class="text-sm font-semibold text-ink-900">Basic Information</h2>
                <p class="text-xs text-ink-500">Identification and classification.</p>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-ink-600">Reference No.</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no', $property->reference_no) }}" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">Name</label>
                        <input type="text" name="name" value="{{ old('name', $property->name) }}" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">Type</label>
                        <select name="type" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @foreach($typeLabels as $k => $l)
                                <option value="{{ $k }}" @selected(old('type', $property->type) === $k)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">Status</label>
                        <select name="status" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @foreach($statusLabels as $k => $l)
                                <option value="{{ $k }}" @selected(old('status', $property->status) === $k)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-ink-100">
                <h2 class="text-sm font-semibold text-ink-900">Address</h2>
                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-ink-600">Street Address</label>
                        <input type="text" name="address" value="{{ old('address', $property->address) }}" required class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">City</label>
                        <input type="text" name="city" value="{{ old('city', $property->city) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">State / Province</label>
                        <input type="text" name="state" value="{{ old('state', $property->state) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $property->postal_code) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">Country</label>
                        <input type="text" name="country" value="{{ old('country', $property->country ?? 'Malaysia') }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-ink-100">
                <h2 class="text-sm font-semibold text-ink-900">Characteristics</h2>
                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="text-xs font-medium text-ink-600">Land Area (m²)</label>
                        <input type="number" step="0.01" min="0" name="land_area" value="{{ old('land_area', $property->land_area) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">Building Area (m²)</label>
                        <input type="number" step="0.01" min="0" name="building_area" value="{{ old('building_area', $property->building_area) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">Year Built</label>
                        <input type="number" min="1700" max="{{ date('Y') + 1 }}" name="year_built" value="{{ old('year_built', $property->year_built) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">Bedrooms</label>
                        <input type="number" min="0" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-ink-600">Bathrooms</label>
                        <input type="number" min="0" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-xs font-medium text-ink-600">Description</label>
                    <textarea name="description" rows="3" class="mt-1 w-full rounded-md border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description', $property->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-ink-100">
                <h2 class="text-sm font-semibold text-ink-900">Map & Polygon</h2>
                <p class="text-xs text-ink-500">Use the toolbar to draw / edit the property boundary. A point marker for the property location is auto-derived from the polygon centroid (or can be manually placed).</p>

                <div id="property-edit-map" class="mt-3 h-[420px] w-full overflow-hidden rounded-lg border border-ink-200"></div>

                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-ink-500">
                    <button type="button" data-map-action="clear" class="rounded-md border border-ink-200 px-2 py-1 hover:bg-ink-50">Clear polygon</button>
                    <span data-map-status>No polygon drawn yet.</span>
                </div>

                <input type="hidden" name="boundary" id="field-boundary" value="{{ old('boundary', $boundaryGeo ? json_encode($boundaryGeo) : '') }}">
                <input type="hidden" name="location" id="field-location" value="{{ old('location', $locationGeo ? json_encode($locationGeo) : '') }}">
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-ink-100">
                <h2 class="text-sm font-semibold text-ink-900">Quick Tips</h2>
                <ul class="mt-3 list-disc space-y-1 pl-4 text-xs text-ink-500">
                    <li>Use the pentagon icon (top-left) to draw a polygon.</li>
                    <li>Use the edit icon to adjust vertices.</li>
                    <li>Geometry is stored as PostGIS <code class="text-[10px]">Polygon (SRID 4326)</code>.</li>
                </ul>
            </div>

            <div class="flex flex-col gap-2">
                <button type="submit" class="btn-accent w-full justify-center">
                    {{ $property->exists ? 'Save Changes' : 'Create Property' }}
                </button>
                <a href="{{ $property->exists ? route('properties.show', $property) : route('properties.index') }}" class="text-center text-xs text-ink-500 hover:text-ink-700">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    window.__propertyEditMap = {
        boundary: @json($boundaryGeo),
        location: @json($locationGeo),
    };
</script>
@endpush
