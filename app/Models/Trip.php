<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TripItem::class)
            ->orderBy('is_checked')
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function shoppingItems(): HasMany
    {
        return $this->hasMany(ShoppingItem::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(TripBudget::class)
            ->orderBy('created_at');
    }

    public function getDurationDaysAttribute(): ?int
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getFoodBudgetValueAttribute(): float
    {
        if (!$this->relationLoaded('budgets')) {
            $this->load('budgets');
        }

        return round((float) $this->budgets
            ->where('category', 'food')
            ->sum(fn ($budget) => (float) $budget->amount), 2);
    }

    public function getSpentShoppingTotalAttribute(): float
    {
        if (array_key_exists('shopping_items_actual_price_sum', $this->attributes)) {
            return round((float) $this->attributes['shopping_items_actual_price_sum'], 2);
        }

        return round((float) $this->shoppingItems()
            ->whereNotNull('actual_price')
            ->sum('actual_price'), 2);
    }

    public function getBudgetDifferenceValueAttribute(): float
    {
        return round($this->food_budget_value - $this->spent_shopping_total, 2);
    }
}