<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdCostItem extends Model
{
    protected $fillable = [
        'household_id',
        'title',
        'category',
        'interval',
        'amount',
        'is_active',
        'is_auto_generated',
        'sort_order',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_auto_generated' => 'boolean',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }
}