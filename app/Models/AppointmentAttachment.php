<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentAttachment extends Model
{
    protected $fillable = [
        'appointment_id',
        'original_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}