<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyRental;
use App\Models\PropertyValuation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalProperties   = Property::count();
        $activeRentals     = PropertyRental::where('status', 'active')->count();
        $totalValuation    = PropertyValuation::sum('market_value');
        $monthlyRentRevenue = PropertyRental::where('status', 'active')->sum('monthly_rent');

        $byType   = Property::query()
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $byStatus = Property::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Last 6 months of valuations (average market value per month).
        $start = Carbon::now()->subMonths(5)->startOfMonth();
        $rawValuations = PropertyValuation::query()
            ->where('valuation_date', '>=', $start)
            ->select(
                DB::raw("to_char(valuation_date, 'YYYY-MM') as bucket"),
                DB::raw('AVG(market_value) as avg_value'),
            )
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy('bucket');

        $valuationTrend = collect();
        for ($i = 0; $i < 6; $i++) {
            $key = Carbon::now()->subMonths(5 - $i)->format('Y-m');
            $valuationTrend->push([
                'label' => Carbon::createFromFormat('Y-m', $key)->format('M Y'),
                'value' => (float) ($rawValuations[$key]->avg_value ?? 0),
            ]);
        }

        $recentProperties = Property::query()
            ->latest()
            ->limit(5)
            ->get(['id', 'reference_no', 'name', 'type', 'status', 'city']);

        $mapProperties = Property::query()
            ->whereNotNull('boundary')
            ->limit(500)
            ->get(['id', 'reference_no', 'name', 'type', 'status', 'boundary', 'location']);

        return view('dashboard', [
            'totalProperties'    => $totalProperties,
            'activeRentals'      => $activeRentals,
            'totalValuation'     => $totalValuation,
            'monthlyRentRevenue' => $monthlyRentRevenue,
            'byType'             => $byType,
            'byStatus'           => $byStatus,
            'valuationTrend'     => $valuationTrend,
            'recentProperties'   => $recentProperties,
            'mapGeoJson'         => $this->toFeatureCollection($mapProperties),
        ]);
    }

    private function toFeatureCollection($properties): array
    {
        return [
            'type'     => 'FeatureCollection',
            'features' => $properties->map(function (Property $p) {
                return [
                    'type'       => 'Feature',
                    'geometry'   => $p->boundary ?: null,
                    'properties' => [
                        'id'           => $p->id,
                        'reference_no' => $p->reference_no,
                        'name'         => $p->name,
                        'type'         => $p->type_label,
                        'status'       => $p->status_label,
                        'url'          => route('properties.show', $p),
                    ],
                ];
            })->filter(fn ($f) => $f['geometry'] !== null)->values()->all(),
        ];
    }
}
