<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdTripItem extends Model
{
    protected $fillable = [
        'trip_id',
        'title',
        'type',
        'is_done',
        'sort_order',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(HouseholdTrip::class, 'trip_id');
    }
}