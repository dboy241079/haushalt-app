<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripItem extends Model
{
    protected $fillable = [
        'trip_id',
        'created_by_user_id',
        'list_type',
        'category',
        'title',
        'quantity',
        'notes',
        'is_checked',
        'is_suggested',
        'checked_at',
        'sort_order',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'is_suggested' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}