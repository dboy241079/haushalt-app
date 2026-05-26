<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HouseholdInsurance extends Model
{
    protected $fillable = [
    'household_id',
    'title',
    'provider',
    'provider_street',
    'provider_zip',
    'provider_city',
    'provider_email',
    'policy_number',
    'insurance_type',
    'starts_at',
    'ends_at',
    'cancellation_notice_days',
    'payment_interval',
    'amount',
    'status',
    'notes',
    'created_by_user_id',
    'insured_first_name',
    'insured_last_name',
    'insured_street',
    'insured_zip',
    'insured_city',
    'insured_email',
    'insured_phone',
    'reminder_event_id',
];

   protected $casts = [
    'starts_at' => 'date',
    'ends_at' => 'date',
    'amount' => 'decimal:2',
];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function documents(): HasMany
{
    return $this->hasMany(HouseholdInsuranceDocument::class)
        ->orderByDesc('created_at');
}
public function reminderEvent(): BelongsTo
{
    return $this->belongsTo(HouseholdEvent::class, 'reminder_event_id');
}
}