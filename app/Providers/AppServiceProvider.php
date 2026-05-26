<?php

namespace App\Providers;

use App\Models\Household;
use App\Models\HouseholdMember;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Registered::class, function (Registered $event) {
            $user = $event->user;

            if ($user->households()->exists()) {
                return;
            }

            DB::transaction(function () use ($user) {
                $household = Household::create([
                    'name' => $user->name . ' Haushalt',
                    'created_by' => $user->id,
                ]);

                HouseholdMember::create([
                    'household_id' => $household->id,
                    'user_id' => $user->id,
                    'role' => 'admin',
                    'display_name' => $user->name,
                    'is_active' => true,
                ]);
            });
        });
    }
}