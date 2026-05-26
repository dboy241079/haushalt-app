<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chore extends Model
{
    protected $fillable = [
        'household_id',
        'title',
        'description',
        'room',
        'assigned_user_id',
        'frequency',
        'due_date',
        'last_completed_date',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'last_completed_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}