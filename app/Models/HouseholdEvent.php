<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HouseholdEvent extends Model
{
    protected $fillable = [
        'household_id',
        'title',
        'type',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'all_day',
        'recurrence',
        'created_by_user_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'all_day' => 'boolean',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(HouseholdEventAttachment::class);
    }
    public function reminderInsurance(): HasOne
{
    return $this->hasOne(HouseholdInsurance::class, 'reminder_event_id');
}
}