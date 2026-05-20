<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_no',
        'name',
        'type',
        'status',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'land_area',
        'building_area',
        'bedrooms',
        'bathrooms',
        'year_built',
        'description',
        'location',
        'boundary',
    ];

    protected $casts = [
        'location'      => 'array', // GeoJSON Point  { type, coordinates: [lng, lat] }
        'boundary'      => 'array', // GeoJSON Polygon { type, coordinates: [[[lng,lat],...]] }
        'land_area'     => 'decimal:2',
        'building_area' => 'decimal:2',
    ];

    public function valuations(): HasMany
    {
        return $this->hasMany(PropertyValuation::class)->orderByDesc('valuation_date');
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(PropertyRental::class)->orderByDesc('start_date');
    }

    public function latestValuation(): ?PropertyValuation
    {
        return $this->valuations()->first();
    }

    public function activeRental(): ?PropertyRental
    {
        return $this->rentals()->where('status', 'active')->first();
    }

    public static function typeLabels(): array
    {
        return [
            'residential'  => 'Residential',
            'commercial'   => 'Commercial',
            'industrial'   => 'Industrial',
            'agricultural' => 'Agricultural',
            'mixed_use'    => 'Mixed-Use',
            'land'         => 'Land',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'available'    => 'Available',
            'occupied'     => 'Occupied',
            'under_review' => 'Under Review',
            'inactive'     => 'Inactive',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? ucfirst($this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst($this->status);
    }
}
