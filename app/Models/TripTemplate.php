<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripTemplate extends Model
{
    protected $fillable = [
        'household_id',
        'created_by_user_id',
        'name',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TripTemplateItem::class)
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}