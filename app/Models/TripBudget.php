<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripBudget extends Model
{
    protected $fillable = [
        'trip_id',
        'created_by_user_id',
        'category',
        'title',
        'amount',
        'is_paid',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}