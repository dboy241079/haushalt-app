<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodSuggestion extends Model
{
    protected $table = 'food_suggestions';

    protected $fillable = [
        'trigger_term',
        'alternative',
        'alternative_label',
        'alternative_search_term',
        'alternative_barcode',
        'goal',
        'reason',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}