<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodProductCache extends Model
{
    protected $table = 'food_products_cache';

    protected $fillable = [
        'barcode',
        'product_name',
        'brand',
        'image_url',
        'nutrition_grade',
        'categories',
        'nutriments',
        'raw_payload',
        'last_synced_at',
    ];

    protected $casts = [
        'categories' => 'array',
        'nutriments' => 'array',
        'raw_payload' => 'array',
        'last_synced_at' => 'datetime',
    ];
}