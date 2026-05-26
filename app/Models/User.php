<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    public function householdMemberships()
{
    return $this->hasMany(\App\Models\HouseholdMember::class);
}

public function households()
{
    return $this->belongsToMany(\App\Models\Household::class, 'household_members')
        ->withPivot(['role', 'display_name', 'is_active'])
        ->withTimestamps();
}

public function assignedChores()
{
    return $this->hasMany(\App\Models\Chore::class, 'assigned_user_id');
}

public function shoppingItemsAdded()
{
    return $this->hasMany(\App\Models\ShoppingItem::class, 'added_by_user_id');
}

public function shoppingItemsBought()
{
    return $this->hasMany(\App\Models\ShoppingItem::class, 'bought_by_user_id');
}

public function createdHouseholdEvents()
{
    return $this->hasMany(\App\Models\HouseholdEvent::class, 'created_by_user_id');
}
}
