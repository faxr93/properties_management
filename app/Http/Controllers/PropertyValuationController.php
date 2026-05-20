<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyValuation;
use Illuminate\Http\Request;

class PropertyValuationController extends Controller
{
    public function index(Request $request)
    {
        $query = PropertyValuation::query()->with('property');

        if ($search = $request->string('search')->trim()->value()) {
            $query->whereHas('property', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('reference_no', 'ilike', "%{$search}%");
            });
        }

        if ($method = $request->string('method')->value()) {
            $query->where('method', $method);
        }

        $valuations = $query->orderByDesc('valuation_date')->paginate(12)->withQueryString();

        return view('admin.valuations.index', [
            'valuations'   => $valuations,
            'methodLabels' => PropertyValuation::methodLabels(),
            'filters'      => [
                'search' => $search,
                'method' => $method,
            ],
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.valuations.form', [
            'valuation'    => new PropertyValuation([
                'property_id'    => $request->integer('property_id') ?: null,
                'valuation_date' => now()->toDateString(),
                'currency'       => 'MYR',
                'method'         => 'market',
            ]),
            'properties'   => Property::orderBy('name')->get(['id', 'name', 'reference_no']),
            'methodLabels' => PropertyValuation::methodLabels(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateValuation($request);
        $valuation = PropertyValuation::create($data);
        return redirect()->route('admin.valuations.index')->with('status', "Valuation #{$valuation->id} recorded.");
    }

    public function edit(PropertyValuation $valuation)
    {
        return view('admin.valuations.form', [
            'valuation'    => $valuation,
            'properties'   => Property::orderBy('name')->get(['id', 'name', 'reference_no']),
            'methodLabels' => PropertyValuation::methodLabels(),
        ]);
    }

    public function update(Request $request, PropertyValuation $valuation)
    {
        $valuation->update($this->validateValuation($request));
        return redirect()->route('admin.valuations.index')->with('status', "Valuation #{$valuation->id} updated.");
    }

    public function destroy(PropertyValuation $valuation)
    {
        $valuation->delete();
        return redirect()->route('admin.valuations.index')->with('status', 'Valuation deleted.');
    }

    private function validateValuation(Request $request): array
    {
        return $request->validate([
            'property_id'    => 'required|exists:properties,id',
            'valuation_date' => 'required|date',
            'market_value'   => 'required|numeric|min:0',
            'land_value'     => 'nullable|numeric|min:0',
            'building_value' => 'nullable|numeric|min:0',
            'currency'       => 'required|string|size:3',
            'method'         => 'required|in:' . implode(',', array_keys(PropertyValuation::methodLabels())),
            'valuer_name'    => 'nullable|string|max:255',
            'valuer_license' => 'nullable|string|max:128',
            'notes'          => 'nullable|string',
        ]);
    }
}
