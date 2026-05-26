<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdEventAttachment extends Model
{
    protected $fillable = [
        'household_event_id',
        'original_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(HouseholdEvent::class, 'household_event_id');
    }
}