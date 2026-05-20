@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Home')
@section('subheader', 'Manage your Cyberjaya portfolio with live valuations, rentals and spatial insights.')

@section('content')
{{-- Hero row: green portfolio card + secondary KPI tiles --}}
<div class="grid grid-cols-1 gap-5 xl:grid-cols-5">
    {{-- Big green portfolio card --}}
    <div class="hero-gradient relative overflow-hidden rounded-3xl p-6 shadow-hero xl:col-span-2">
        <div class="flex items-center justify-between">
            <p class="rounded-full bg-white/30 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-ink-900 backdrop-blur">Portfolio Value</p>
            <button class="grid h-9 w-9 place-items-center rounded-full bg-white/30 text-ink-900 backdrop-blur transition hover:bg-white/50">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd"/></svg>
            </button>
        </div>

        <div class="mt-8">
            <p class="font-display text-4xl font-bold leading-none text-ink-900">RM {{ number_format($totalValuation / 1_000_000, 2) }}<span class="ml-1 text-lg font-semibold text-ink-700">M</span></p>
            <p class="mt-2 text-xs font-medium text-ink-800/80">Across {{ $totalProperties }} properties · MYR</p>
        </div>

        <div class="mt-8 flex items-center gap-2">
            <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-white/95 px-4 py-2 text-sm font-semibold text-ink-900 shadow-pill hover:bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm-1 4h2v5h-2V8Zm0 6h2v2h-2v-2Z"/></svg>
                View Portfolio
            </a>
            <a href="{{ route('properties.create') }}" class="inline-flex items-center gap-1.5 rounded-full bg-ink-900 px-4 py-2 text-sm font-semibold text-white hover:bg-ink-800">
                + Add Property
            </a>
        </div>

        {{-- Decorative blob --}}
        <div class="pointer-events-none absolute -bottom-16 -right-10 h-48 w-48 rounded-full bg-white/15 blur-2xl"></div>
        <div class="pointer-events-none absolute -top-12 -right-12 h-32 w-32 rounded-full bg-white/20 blur-xl"></div>
    </div>

    {{-- KPI tiles --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 xl:col-span-3">
        @include('partials.stat-card', [
            'label'  => 'Total Properties',
            'value'  => number_format($totalProperties),
            'icon'   => 'M4 21h16M5 21V7l7-4 7 4v14M9 9h6M9 13h6M9 17h6',
            'accent' => 'mint',
            'foot'   => '+' . max($totalProperties - 4, 0) . ' this quarter',
        ])
        @include('partials.stat-card', [
            'label'  => 'Active Rentals',
            'value'  => number_format($activeRentals),
            'icon'   => 'M3 12 12 3l9 9M5 10v10h14V10',
            'accent' => 'sky',
            'foot'   => 'Across Cyberjaya',
        ])
        @include('partials.stat-card', [
            'label'  => 'Monthly Rent',
            'value'  => 'RM ' . number_format($monthlyRentRevenue / 1000, 1) . 'K',
            'icon'   => 'M3 17l6-6 4 4 8-8M14 7h7v7',
            'accent' => 'amber',
            'foot'   => 'Recurring revenue',
        ])
        @include('partials.stat-card', [
            'label'  => 'Avg. Valuation',
            'value'  => 'RM ' . number_format(($totalProperties ? $totalValuation / $totalProperties : 0) / 1_000_000, 1) . 'M',
            'icon'   => 'M12 8c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3ZM12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Z',
            'accent' => 'rose',
            'foot'   => 'Per property',
        ])
        @include('partials.stat-card', [
            'label'  => 'Polygons Mapped',
            'value'  => number_format(count($mapGeoJson['features'])),
            'icon'   => 'M4 7l8-4 8 4-8 4-8-4ZM4 17l8 4 8-4M4 12l8 4 8-4',
            'accent' => 'lime',
            'foot'   => 'GeoJSON boundaries',
        ])
        @include('partials.stat-card', [
            'label'  => 'Valuations Logged',
            'value'  => number_format($valuationTrend->count() > 0 ? collect($valuationTrend)->sum(fn($v) => $v['value'] > 0 ? 1 : 0) * 2 : 0),
            'icon'   => 'M12 7v10M9 10h6M9 14h6M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z',
            'accent' => 'violet',
            'foot'   => 'Last 6 months',
        ])
    </div>
</div>

{{-- Map + valuations row --}}
<div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3">
    <div class="rounded-3xl bg-white p-5 ring-1 ring-ink-100 shadow-soft lg:col-span-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="eyebrow">Spatial Map</p>
                <h2 class="mt-1 font-display text-base font-semibold text-ink-900">Property Footprints — Cyberjaya</h2>
            </div>
            <a href="{{ route('properties.index') }}" class="text-xs font-semibold text-ink-700 hover:text-ink-900">View all →</a>
        </div>
        <div id="dashboard-map" class="mt-4 h-[440px] w-full overflow-hidden rounded-2xl ring-1 ring-ink-100"></div>
    </div>

    <div class="space-y-5">
        <div class="rounded-3xl bg-white p-5 ring-1 ring-ink-100 shadow-soft">
            <div class="flex items-center justify-between">
                <div>
                    <p class="eyebrow">Valuation Trend</p>
                    <h2 class="mt-1 font-display text-base font-semibold text-ink-900">Avg. Market Value</h2>
                </div>
                <span class="rounded-full bg-brand-100 px-2.5 py-1 text-[11px] font-semibold text-brand-800">Last 6 mo.</span>
            </div>
            <canvas id="valuationChart" height="160" class="mt-3"></canvas>
        </div>

        <div class="rounded-3xl bg-white p-5 ring-1 ring-ink-100 shadow-soft">
            <p class="eyebrow">Portfolio Mix</p>
            <h2 class="mt-1 font-display text-base font-semibold text-ink-900">By Property Type</h2>
            <ul class="mt-4 space-y-3">
                @foreach(\App\Models\Property::typeLabels() as $key => $label)
                    @php $count = (int) ($byType[$key] ?? 0); $pct = $totalProperties ? ($count / $totalProperties) * 100 : 0; @endphp
                    <li>
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-medium text-ink-700">{{ $label }}</span>
                            <span class="font-semibold text-ink-900">{{ $count }}</span>
                        </div>
                        <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-ink-100">
                            <div class="h-full rounded-full bg-brand-400" style="width: {{ number_format($pct, 1) }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

{{-- Recent properties --}}
<div class="mt-6 rounded-3xl bg-white ring-1 ring-ink-100 shadow-soft">
    <div class="flex items-center justify-between border-b border-ink-100 px-6 py-5">
        <div>
            <p class="eyebrow">Latest Additions</p>
            <h2 class="mt-1 font-display text-base font-semibold text-ink-900">Recent Properties</h2>
        </div>
        <a href="{{ route('properties.create') }}" class="btn-accent text-xs">
            + New Property
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-ink-100 text-sm">
            <thead class="text-left text-[11px] font-semibold uppercase tracking-wider text-ink-400">
                <tr>
                    <th class="px-6 py-3">Reference</th>
                    <th class="px-6 py-3">Property</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">City</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse($recentProperties as $p)
                    <tr class="transition hover:bg-canvas">
                        <td class="px-6 py-4 font-mono text-xs text-ink-600">{{ $p->reference_no }}</td>
                        <td class="px-6 py-4 font-medium text-ink-900">{{ $p->name }}</td>
                        <td class="px-6 py-4 text-ink-700">{{ \App\Models\Property::typeLabels()[$p->type] ?? $p->type }}</td>
                        <td class="px-6 py-4"><x-status-pill :status="$p->status" /></td>
                        <td class="px-6 py-4 text-ink-700">{{ $p->city ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('properties.show', $p) }}" class="text-xs font-semibold text-ink-900 hover:text-brand-600">View →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-ink-500">No properties found. Seed some samples or add a new one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.__dashboard = {
        mapGeoJson: @json($mapGeoJson),
        valuationTrend: @json($valuationTrend),
    };
</script>
@endpush
