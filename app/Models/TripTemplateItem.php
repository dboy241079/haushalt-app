<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripTemplateItem extends Model
{
    protected $fillable = [
        'trip_template_id',
        'list_type',
        'category',
        'title',
        'quantity',
        'notes',
        'sort_order',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(TripTemplate::class, 'trip_template_id');
    }
}