<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'reminder_at',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(AppointmentAttachment::class);
    }
}