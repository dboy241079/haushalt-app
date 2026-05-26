<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Trip;

class Household extends Model
{
    protected $fillable = [
        'name',
        'created_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(HouseholdMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'household_members')
            ->withPivot(['role', 'display_name', 'is_active'])
            ->withTimestamps();
    }

    public function chores(): HasMany
    {
        return $this->hasMany(Chore::class);
    }

    public function shoppingItems(): HasMany
    {
        return $this->hasMany(ShoppingItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(HouseholdEvent::class);
    }
    public function costItems()
{
    return $this->hasMany(\App\Models\HouseholdCostItem::class);
}
public function incomeItems()
{
    return $this->hasMany(\App\Models\HouseholdIncomeItem::class);
}
public function trips()
{
    return $this->hasMany(Trip::class);
}
public function tripTemplates()
{
    return $this->hasMany(\App\Models\TripTemplate::class);
}

}