<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Chore;
use App\Models\ShoppingItem;
use App\Models\HouseholdEvent;
use App\Models\HouseholdInsurance;

use App\Http\Controllers\ChoreController;
use App\Http\Controllers\ShoppingItemController;
use App\Http\Controllers\HouseholdEventController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\HouseholdInsuranceController;
use App\Http\Controllers\HouseholdCostController;

use App\Http\Controllers\TripController;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->get('/test-haushalt', function () {
    $user = auth()->user();

    return response()->json([
        'user' => $user->name,
        'households_count' => $user->households()->count(),
    ]);
})->name('test-haushalt');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $household = $user->households()->first();
        $today = \Carbon\Carbon::today();
        $now = now();

        $openChores = collect();
        $openShoppingItems = collect();
        $nextEvents = collect();

        $quickSummaryWeek = collect();
        $quickWeekRangeLabel = now()->startOfWeek()->format('d.m.') . ' - ' . now()->endOfWeek()->format('d.m.Y');

        $stats = [
            'open_chores' => 0,
            'open_shopping' => 0,
            'next_events' => 0,
            'overdue_chores' => 0,
        ];

        $insuranceSummary = [
            'active_count' => 0,
            'monthly_total' => 0,
            'yearly_total' => 0,
            'ending_soon_count' => 0,
        ];

        $costSummary = [
    'cost_count' => 0,
    'income_count' => 0,
    'monthly_cost_total' => 0,
    'monthly_income_total' => 0,
    'monthly_net_total' => 0,
    'show_income_section' => false,
];

        if ($household) {
            $activeInsurances = \App\Models\HouseholdInsurance::query()
                ->where('household_id', $household->id)
                ->where('status', 'active')
                ->get();

            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();

            $quickEntriesWeek = \App\Models\HouseholdQuickEntry::query()
                ->where('household_id', $household->id)
                ->whereBetween('done_on', [
                    $weekStart->toDateString(),
                    $weekEnd->toDateString(),
                ])
                ->get();

            $quickSummaryWeek = $quickEntriesWeek
                ->groupBy('quick_type')
                ->map(fn ($items, $type) => [
                    'label' => $type,
                    'count' => $items->count(),
                ])
                ->sortByDesc('count')
                ->values();

            $insuranceSummary['active_count'] = $activeInsurances->count();

            $insuranceSummary['monthly_total'] = $activeInsurances->sum(function ($insurance) {
                return match ($insurance->payment_interval) {
                    'monthly' => (float) $insurance->amount,
                    'quarterly' => (float) $insurance->amount / 3,
                    'half_yearly' => (float) $insurance->amount / 6,
                    'yearly' => (float) $insurance->amount / 12,
                    default => 0,
                };
            });

            $insuranceSummary['yearly_total'] = $activeInsurances->sum(function ($insurance) {
                return match ($insurance->payment_interval) {
                    'monthly' => (float) $insurance->amount * 12,
                    'quarterly' => (float) $insurance->amount * 4,
                    'half_yearly' => (float) $insurance->amount * 2,
                    'yearly' => (float) $insurance->amount,
                    default => 0,
                };
            });

            $insuranceSummary['ending_soon_count'] = $activeInsurances->filter(function ($insurance) {
                return $insurance->ends_at
                    && \Carbon\Carbon::parse($insurance->ends_at)->between(
                        now()->startOfDay(),
                        now()->copy()->addDays(30)->endOfDay()
                    );
            })->count();

            $costItems = \App\Models\HouseholdCostItem::query()
    ->where('household_id', $household->id)
    ->where('is_active', true)
    ->get();

$incomeItems = \App\Models\HouseholdIncomeItem::query()
    ->where('household_id', $household->id)
    ->where('is_active', true)
    ->get();

$monthlyCostTotal = $costItems->sum(function ($item) {
    return match ($item->interval) {
        'monthly' => (float) $item->amount,
        'quarterly' => (float) $item->amount / 3,
        'half_yearly' => (float) $item->amount / 6,
        'yearly' => (float) $item->amount / 12,
        default => 0,
    };
});

$monthlyIncomeTotal = $incomeItems->sum(function ($item) {
    return match ($item->interval) {
        'monthly' => (float) $item->amount,
        'quarterly' => (float) $item->amount / 3,
        'half_yearly' => (float) $item->amount / 6,
        'yearly' => (float) $item->amount / 12,
        default => 0,
    };
});

$showIncomeSection = in_array($household->house_usage, ['partial_rent', 'full_rent'], true)
    || $incomeItems->isNotEmpty();

$costSummary = [
    'cost_count' => $costItems->count(),
    'income_count' => $incomeItems->count(),
    'monthly_cost_total' => $monthlyCostTotal,
    'monthly_income_total' => $monthlyIncomeTotal,
    'monthly_net_total' => $monthlyIncomeTotal - $monthlyCostTotal,
    'show_income_section' => $showIncomeSection,
];

            $openChores = \App\Models\Chore::query()
                ->where('household_id', $household->id)
                ->where('is_active', true)
                ->where(function ($query) use ($today) {
                    $query->whereNull('last_completed_date')
                        ->orWhereDate('last_completed_date', '<', $today);
                })
                ->where(function ($query) use ($today) {
                    $query->whereNull('due_date')
                        ->orWhereDate('due_date', '<=', $today);
                })
                ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('due_date')
                ->orderBy('sort_order')
                ->limit(8)
                ->get();

            $openShoppingItems = \App\Models\ShoppingItem::query()
                ->where('household_id', $household->id)
                ->where('is_bought', false)
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            $nextEvents = \App\Models\HouseholdEvent::with('attachments')
                ->where('household_id', $household->id)
                ->where('starts_at', '>=', $now)
                ->orderBy('starts_at')
                ->limit(5)
                ->get();

            $stats['open_chores'] = $openChores->count();

            $stats['open_shopping'] = \App\Models\ShoppingItem::query()
                ->where('household_id', $household->id)
                ->where('is_bought', false)
                ->count();

            $stats['next_events'] = \App\Models\HouseholdEvent::query()
                ->where('household_id', $household->id)
                ->where('starts_at', '>=', $now)
                ->count();

            $stats['overdue_chores'] = \App\Models\Chore::query()
                ->where('household_id', $household->id)
                ->where('is_active', true)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->where(function ($query) use ($today) {
                    $query->whereNull('last_completed_date')
                        ->orWhereDate('last_completed_date', '<', $today);
                })
                ->count();
        }

        return view('dashboard', [
    'user' => $user,
    'household' => $household,
    'openChores' => $openChores,
    'openShoppingItems' => $openShoppingItems,
    'nextEvents' => $nextEvents,
    'stats' => $stats,
    'today' => $today,
    'insuranceSummary' => $insuranceSummary,
    'costSummary' => $costSummary,
    'quickSummaryWeek' => $quickSummaryWeek,
    'quickWeekRangeLabel' => $quickWeekRangeLabel,
]);
    })->name('dashboard');

    Route::get('/shopping', [ShoppingItemController::class, 'index'])->name('shopping.index');
    Route::post('/shopping', [ShoppingItemController::class, 'store'])->name('shopping.store');
    Route::post('/shopping/{shoppingItem}/toggle-bought', [ShoppingItemController::class, 'toggleBought'])->name('shopping.toggleBought');
    Route::delete('/shopping/{shoppingItem}', [ShoppingItemController::class, 'destroy'])->name('shopping.destroy');

    Route::get('/chores', [ChoreController::class, 'index'])->name('chores.index');
    Route::post('/chores', [ChoreController::class, 'store'])->name('chores.store');
    Route::post('/chores/{chore}/complete', [ChoreController::class, 'complete'])->name('chores.complete');

    Route::get('/events', [HouseholdEventController::class, 'index'])->name('events.index');
    Route::post('/events', [HouseholdEventController::class, 'store'])->name('events.store');
    Route::delete('/events/{event}', [HouseholdEventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/feed', [HouseholdEventController::class, 'feed'])->name('events.feed');
    Route::patch('/events/{event}', [HouseholdEventController::class, 'update'])->name('events.update');

    Route::get('/household/settings', [HouseholdController::class, 'settings'])->name('household.settings');
    Route::patch('/household', [HouseholdController::class, 'updateName'])->name('household.updateName');

    Route::post('/household/members', [HouseholdController::class, 'addMember'])->name('household.members.store');
    Route::patch('/household/members/{member}', [HouseholdController::class, 'updateMemberRole'])->name('household.members.update');
    Route::delete('/household/members/{member}', [HouseholdController::class, 'destroyMember'])->name('household.members.destroy');

    Route::post('/household/invitations/{invitation}/resend', [HouseholdController::class, 'resendInvitation'])->name('household.invitations.resend');
    Route::delete('/household/invitations/{invitation}', [HouseholdController::class, 'destroyInvitation'])->name('household.invitations.destroy');
    Route::get('/household/invitations/accept/{token}', [HouseholdController::class, 'acceptInvitation'])->name('household.invitations.accept');

    Route::get('/insurances/{insurance}', [HouseholdInsuranceController::class, 'show'])->name('insurances.show');
    Route::patch('/insurances/{insurance}', [HouseholdInsuranceController::class, 'update'])->name('insurances.update');
    Route::post('/insurances/{insurance}/documents', [HouseholdInsuranceController::class, 'storeDocument'])->name('insurances.documents.store');

    
    Route::get('/costs', [HouseholdCostController::class, 'index'])->name('costs.index');
    Route::post('/costs/setup', [HouseholdCostController::class, 'runSetup'])->name('costs.setup');

    Route::post('/costs/items', [HouseholdCostController::class, 'storeItem'])->name('costs.items.store');
    Route::patch('/costs/items/{costItem}', [HouseholdCostController::class, 'updateItem'])->name('costs.items.update');
    Route::delete('/costs/items/{costItem}', [HouseholdCostController::class, 'destroyItem'])->name('costs.items.destroy');

    Route::post('/costs/incomes', [HouseholdCostController::class, 'storeIncomeItem'])->name('costs.incomes.store');
    Route::patch('/costs/incomes/{incomeItem}', [HouseholdCostController::class, 'updateIncomeItem'])->name('costs.incomes.update');
    Route::delete('/costs/incomes/{incomeItem}', [HouseholdCostController::class, 'destroyIncomeItem'])->name('costs.incomes.destroy');

    Route::get('/insurances', [HouseholdInsuranceController::class, 'index'])->name('insurances.index');
    Route::post('/insurances', [HouseholdInsuranceController::class, 'store'])->name('insurances.store');
    Route::delete('/insurances/{insurance}', [HouseholdInsuranceController::class, 'destroy'])->name('insurances.destroy');
    Route::delete('/insurances/{insurance}/documents/{document}', [HouseholdInsuranceController::class, 'destroyDocument'])->name('insurances.documents.destroy');

    Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::patch('/trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    Route::post('/trips/{trip}/items', [TripController::class, 'storeItem'])->name('trips.items.store');
    Route::patch('/trip-items/{item}/toggle', [TripController::class, 'toggleItem'])->name('trips.items.toggle');
    Route::delete('/trip-items/{item}', [TripController::class, 'destroyItem'])->name('trips.items.destroy');

    Route::post('/trips/{trip}/templates', [TripController::class, 'storeTemplate'])->name('trips.templates.store');
    Route::post('/trip-templates/apply', [TripController::class, 'applyTemplate'])->name('trips.templates.apply');
    
    Route::post('/trips/{trip}/budgets', [TripController::class, 'storeBudget'])->name('trips.budgets.store');
    Route::patch('/trip-budgets/{budget}/toggle-paid', [TripController::class, 'toggleBudget'])->name('trips.budgets.toggle');
    Route::delete('/trip-budgets/{budget}', [TripController::class, 'destroyBudget'])->name('trips.budgets.destroy');

    Route::view('/food-check', 'food-check')->name('food-check');

    Route::post('/insurances/{insurance}/cancellation-pdf', [HouseholdInsuranceController::class, 'downloadCancellationPdf'])
        ->name('insurances.cancellation-pdf');

    Route::post('/chores/quick-store', [ChoreController::class, 'quickStore'])->name('chores.quickStore');
    Route::delete('/chores/quick-entry/{entry}', [ChoreController::class, 'quickDestroy'])->name('chores.quickDestroy');

    
    Route::get('/setup-household', function () {
        $user = auth()->user();

        if ($user->households()->exists()) {
            return response()->json([
                'message' => 'Haushalt existiert bereits.',
                'households_count' => $user->households()->count(),
            ]);
        }

        $household = \App\Models\Household::create([
            'name' => $user->name . ' Haushalt',
            'created_by' => $user->id,
        ]);

        \App\Models\HouseholdMember::create([
            'household_id' => $household->id,
            'user_id' => $user->id,
            'role' => 'admin',
            'display_name' => $user->name,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Haushalt wurde angelegt.',
            'household' => $household->name,
            'households_count' => $user->fresh()->households()->count(),
        ]);
    })->name('setup-household');
});

require __DIR__.'/settings.php';