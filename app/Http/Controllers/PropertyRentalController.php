<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyRental;
use Illuminate\Http\Request;

class PropertyRentalController extends Controller
{
    public function index(Request $request)
    {
        $query = PropertyRental::query()->with('property');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('tenant_name', 'ilike', "%{$search}%")
                  ->orWhere('tenant_email', 'ilike', "%{$search}%")
                  ->orWhereHas('property', fn ($pq) =>
                      $pq->where('name', 'ilike', "%{$search}%")
                         ->orWhere('reference_no', 'ilike', "%{$search}%"));
            });
        }

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        $rentals = $query->orderByDesc('start_date')->paginate(12)->withQueryString();

        return view('admin.rentals.index', [
            'rentals'      => $rentals,
            'statusLabels' => PropertyRental::statusLabels(),
            'cycleLabels'  => PropertyRental::cycleLabels(),
            'filters'      => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.rentals.form', [
            'rental'       => new PropertyRental([
                'property_id'   => $request->integer('property_id') ?: null,
                'start_date'    => now()->toDateString(),
                'end_date'      => now()->addYear()->toDateString(),
                'currency'      => 'MYR',
                'payment_cycle' => 'monthly',
                'status'        => 'active',
            ]),
            'properties'   => Property::orderBy('name')->get(['id', 'name', 'reference_no']),
            'statusLabels' => PropertyRental::statusLabels(),
            'cycleLabels'  => PropertyRental::cycleLabels(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRental($request);
        $rental = PropertyRental::create($data);
        return redirect()->route('admin.rentals.index')->with('status', "Rental for {$rental->tenant_name} saved.");
    }

    public function edit(PropertyRental $rental)
    {
        return view('admin.rentals.form', [
            'rental'       => $rental,
            'properties'   => Property::orderBy('name')->get(['id', 'name', 'reference_no']),
            'statusLabels' => PropertyRental::statusLabels(),
            'cycleLabels'  => PropertyRental::cycleLabels(),
        ]);
    }

    public function update(Request $request, PropertyRental $rental)
    {
        $rental->update($this->validateRental($request));
        return redirect()->route('admin.rentals.index')->with('status', "Rental #{$rental->id} updated.");
    }

    public function destroy(PropertyRental $rental)
    {
        $rental->delete();
        return redirect()->route('admin.rentals.index')->with('status', 'Rental record deleted.');
    }

    private function validateRental(Request $request): array
    {
        return $request->validate([
            'property_id'   => 'required|exists:properties,id',
            'tenant_name'   => 'required|string|max:255',
            'tenant_email'  => 'nullable|email|max:255',
            'tenant_phone'  => 'nullable|string|max:64',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'monthly_rent'  => 'required|numeric|min:0',
            'deposit'       => 'nullable|numeric|min:0',
            'currency'      => 'required|string|size:3',
            'payment_cycle' => 'required|in:' . implode(',', array_keys(PropertyRental::cycleLabels())),
            'status'        => 'required|in:' . implode(',', array_keys(PropertyRental::statusLabels())),
            'notes'         => 'nullable|string',
        ]);
    }
}
