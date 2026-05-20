<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tenant_name',
        'tenant_email',
        'tenant_phone',
        'start_date',
        'end_date',
        'monthly_rent',
        'deposit',
        'currency',
        'payment_cycle',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'monthly_rent' => 'decimal:2',
        'deposit'      => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public static function statusLabels(): array
    {
        return [
            'active'    => 'Active',
            'pending'   => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function cycleLabels(): array
    {
        return [
            'monthly'        => 'Monthly',
            'quarterly'      => 'Quarterly',
            'semi_annually'  => 'Semi-Annually',
            'annually'       => 'Annually',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function getCycleLabelAttribute(): string
    {
        return self::cycleLabels()[$this->payment_cycle] ?? ucfirst($this->payment_cycle);
    }
}
