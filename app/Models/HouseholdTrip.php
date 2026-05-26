<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HouseholdTrip extends Model
{
    protected $fillable = [
        'household_id',
        'title',
        'destination',
        'address',
        'starts_on',
        'ends_on',
        'notes',
        'is_active',
        'sort_order',
        'created_by_user_id',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'is_active' => 'boolean',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(HouseholdTripItem::class, 'trip_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function packingItems(): HasMany
    {
        return $this->hasMany(HouseholdTripItem::class, 'trip_id')
            ->where('type', 'packing')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function prepItems(): HasMany
    {
        return $this->hasMany(HouseholdTripItem::class, 'trip_id')
            ->where('type', 'prep')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function documentItems(): HasMany
    {
        return $this->hasMany(HouseholdTripItem::class, 'trip_id')
            ->where('type', 'documents')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}