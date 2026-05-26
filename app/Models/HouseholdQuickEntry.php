<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdQuickEntry extends Model
{
    protected $fillable = [
        'household_id',
        'user_id',
        'quick_type',
        'room',
        'note',
        'done_on',
    ];

    protected $casts = [
        'done_on' => 'date',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}