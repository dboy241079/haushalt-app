<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingItem extends Model
{
    protected $fillable = [
        'household_id',
        'trip_id',
        'title',
        'quantity',
        'category',
        'note',
        'notes',
        'added_by_user_id',
        'bought_by_user_id',
        'is_bought',
        'bought_at',
        'actual_price',
        'needed_for_date',
    ];

    protected $casts = [
        'is_bought' => 'boolean',
        'bought_at' => 'datetime',
        'needed_for_date' => 'date',
        'actual_price' => 'decimal:2',
    ];

    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    public function boughtByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bought_by_user_id');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}