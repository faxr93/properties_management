<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyValuation extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'valuation_date',
        'market_value',
        'land_value',
        'building_value',
        'currency',
        'method',
        'valuer_name',
        'valuer_license',
        'notes',
    ];

    protected $casts = [
        'valuation_date' => 'date',
        'market_value'   => 'decimal:2',
        'land_value'     => 'decimal:2',
        'building_value' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public static function methodLabels(): array
    {
        return [
            'market'      => 'Market Approach',
            'income'      => 'Income Approach',
            'cost'        => 'Cost Approach',
            'comparative' => 'Comparative Sales',
        ];
    }

    public function getMethodLabelAttribute(): string
    {
        return self::methodLabels()[$this->method] ?? ucfirst($this->method);
    }
}
