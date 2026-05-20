<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query();

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('reference_no', 'ilike', "%{$search}%")
                  ->orWhere('address', 'ilike', "%{$search}%")
                  ->orWhere('city', 'ilike', "%{$search}%");
            });
        }

        if ($type = $request->string('type')->value()) {
            $query->where('type', $type);
        }

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        $properties = $query->latest()->paginate(10)->withQueryString();

        // GeoJSON for the map preview on the index page.
        $geoJson = $this->toFeatureCollection(
            (clone $query)->whereNotNull('boundary')->limit(500)->get()
        );

        return view('properties.index', [
            'properties' => $properties,
            'geoJson'    => $geoJson,
            'filters'    => [
                'search' => $search,
                'type'   => $type,
                'status' => $status,
            ],
            'typeLabels'   => Property::typeLabels(),
            'statusLabels' => Property::statusLabels(),
        ]);
    }

    public function create()
    {
        return view('properties.form', [
            'property'     => new Property(),
            'typeLabels'   => Property::typeLabels(),
            'statusLabels' => Property::statusLabels(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProperty($request);
        $property = Property::create($this->prepareGeometryAttributes($data));
        return redirect()->route('properties.show', $property)->with('status', 'Property created successfully.');
    }

    public function show(Property $property)
    {
        $property->load(['valuations', 'rentals']);

        return view('properties.show', [
            'property'  => $property,
            'geoJson'   => $this->propertyToFeature($property),
        ]);
    }

    public function edit(Property $property)
    {
        return view('properties.form', [
            'property'     => $property,
            'typeLabels'   => Property::typeLabels(),
            'statusLabels' => Property::statusLabels(),
        ]);
    }

    public function update(Request $request, Property $property)
    {
        $data = $this->validateProperty($request, $property->id);
        $property->update($this->prepareGeometryAttributes($data));
        return redirect()->route('properties.show', $property)->with('status', 'Property updated successfully.');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('properties.index')->with('status', 'Property deleted.');
    }

    /**
     * Validation rules shared by store + update.
     */
    private function validateProperty(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'reference_no'  => "required|string|max:64|unique:properties,reference_no,{$ignoreId}",
            'name'          => 'required|string|max:255',
            'type'          => 'required|in:' . implode(',', array_keys(Property::typeLabels())),
            'status'        => 'required|in:' . implode(',', array_keys(Property::statusLabels())),
            'address'       => 'required|string',
            'city'          => 'nullable|string|max:128',
            'state'         => 'nullable|string|max:128',
            'postal_code'   => 'nullable|string|max:32',
            'country'       => 'nullable|string|max:128',
            'land_area'     => 'nullable|numeric|min:0',
            'building_area' => 'nullable|numeric|min:0',
            'bedrooms'      => 'nullable|integer|min:0',
            'bathrooms'     => 'nullable|integer|min:0',
            'year_built'    => 'nullable|integer|min:1700|max:' . (date('Y') + 1),
            'description'   => 'nullable|string',
            'boundary'      => 'nullable|string', // GeoJSON Polygon string from the form
            'location'      => 'nullable|string', // GeoJSON Point string from the form
        ]);
    }

    /**
     * Convert GeoJSON strings from the form into associative arrays for JSONB storage.
     */
    private function prepareGeometryAttributes(array $data): array
    {
        foreach (['boundary', 'location'] as $field) {
            if (!empty($data[$field])) {
                $geo = json_decode($data[$field], true);
                $data[$field] = is_array($geo) && isset($geo['type'], $geo['coordinates']) ? $geo : null;
            } else {
                $data[$field] = null;
            }
        }

        return $data;
    }

    private function toFeatureCollection($properties): array
    {
        return [
            'type'     => 'FeatureCollection',
            'features' => $properties->map(fn (Property $p) => $this->propertyToFeature($p))
                ->filter(fn ($f) => $f['geometry'] !== null)
                ->values()
                ->all(),
        ];
    }

    private function propertyToFeature(Property $p): array
    {
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
    }
}
