<?php

namespace App\Http\Controllers;

use App\Models\Chore;
use App\Models\Household;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\HouseholdQuickEntry;

class ChoreController extends Controller
{

public function index(Request $request): View|RedirectResponse
{
    $user = auth()->user();
    $household = $user->households()->first();

    if (!$household) {
        return redirect()
            ->route('dashboard')
            ->withErrors(['household' => 'Kein Haushalt gefunden.']);
    }

    $chores = \App\Models\Chore::with('assignedUser')
        ->where('household_id', $household->id)
        ->where('is_active', true)
        ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
        ->orderBy('due_date')
        ->orderBy('title')
        ->get();

    $members = $household->users()
        ->orderBy('name')
        ->get();

    $quickPrimaryOptions = [
        'Geschirrspüler eingeräumt',
        'Geschirrspüler ausgeräumt',
        'Staubsaugen',
        'Wäsche gewaschen',
        'Müll entsorgt',
        'Essen gekocht',
    ];

    $quickSecondaryOptions = [
        'Wäsche aufgehängt',
        'Wäsche abgehängt',
        'Wischen',
        'Bad geputzt',
        'Küche sauber',
        'Ordnung gemacht',
        'Gebaut (Handwerk)',
        'Auto gewaschen',
        'Auto getankt',
        'Gartenarbeit',
        'Einkauf erledigt',
    ];

    $quickRoomOptions = [
        'Küche',
        'Esszimmer',
        'Wohnzimmer',
        'Schlafzimmer',
        'Bad',
        'Flur',
        'Kinderzimmer',
        'Büro',
        'Hauswirtschaftsraum',
        'Garten',
        'Auto',
        'Sonstiges',
    ];

    $quickFilter = $request->query('quick_filter', 'today');
    if (!in_array($quickFilter, ['today', 'week'], true)) {
        $quickFilter = 'today';
    }

    $today = now()->startOfDay();
    $weekStart = now()->startOfWeek();
    $weekEnd = now()->endOfWeek();

    $quickEntriesQuery = \App\Models\HouseholdQuickEntry::with('user')
        ->where('household_id', $household->id);

    if ($quickFilter === 'week') {
        $quickEntriesQuery->whereBetween('done_on', [
            $weekStart->toDateString(),
            $weekEnd->toDateString(),
        ]);
    } else {
        $quickEntriesQuery->whereDate('done_on', $today->toDateString());
    }

    $quickEntries = $quickEntriesQuery
        ->orderByDesc('done_on')
        ->orderByDesc('created_at')
        ->get();

    $quickEntriesWeek = \App\Models\HouseholdQuickEntry::with('user')
        ->where('household_id', $household->id)
        ->whereBetween('done_on', [
            $weekStart->toDateString(),
            $weekEnd->toDateString(),
        ])
        ->orderByDesc('done_on')
        ->orderByDesc('created_at')
        ->get();

    $quickStatsWeek = $quickEntriesWeek
        ->groupBy('quick_type')
        ->map(fn ($items, $type) => [
            'label' => $type,
            'count' => $items->count(),
        ])
        ->sortByDesc('count')
        ->values();

    $quickStatsByUserWeek = $quickEntriesWeek
        ->groupBy(fn ($entry) => $entry->user?->name ?? 'Unbekannt')
        ->map(fn ($items, $name) => [
            'label' => $name,
            'count' => $items->count(),
        ])
        ->sortByDesc('count')
        ->values();

    $quickStatsByRoomWeek = $quickEntriesWeek
        ->groupBy(fn ($entry) => $entry->room ?: 'Ohne Raum')
        ->map(fn ($items, $room) => [
            'label' => $room,
            'count' => $items->count(),
        ])
        ->sortByDesc('count')
        ->values();

    return view('chores.index', [
        'household' => $household,
        'chores' => $chores,
        'members' => $members,
        'quickPrimaryOptions' => $quickPrimaryOptions,
        'quickSecondaryOptions' => $quickSecondaryOptions,
        'quickRoomOptions' => $quickRoomOptions,
        'quickEntries' => $quickEntries,
        'quickFilter' => $quickFilter,
        'quickStatsWeek' => $quickStatsWeek,
        'quickStatsByUserWeek' => $quickStatsByUserWeek,
        'quickStatsByRoomWeek' => $quickStatsByRoomWeek,
        'weekRangeLabel' => $weekStart->format('d.m.') . ' - ' . $weekEnd->format('d.m.Y'),
    ]);
}

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $household = $this->getHousehold($user);

        $memberIds = $household->users()->pluck('users.id')->map(fn ($id) => (int) $id)->all();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'room' => ['nullable', 'string', 'max:100'],
            'frequency' => ['required', Rule::in(['daily', 'weekly', 'biweekly', 'monthly'])],
            'assigned_user_id' => ['nullable', 'integer', Rule::in($memberIds)],
            'due_date' => ['nullable', 'date'],
        ]);

        $nextSortOrder = (int) Chore::query()
            ->where('household_id', $household->id)
            ->max('sort_order');

        Chore::create([
            'household_id' => $household->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'room' => $data['room'] ?? null,
            'assigned_user_id' => $data['assigned_user_id'] ?? null,
            'frequency' => $data['frequency'],
            'due_date' => $data['due_date'] ?? now()->toDateString(),
            'last_completed_date' => null,
            'is_active' => true,
            'sort_order' => $nextSortOrder + 1,
        ]);

        return redirect()
            ->route('chores.index')
            ->with('status', 'Aufgabe wurde angelegt.');
    }

   public function quickStore(Request $request): RedirectResponse
{
    $user = auth()->user();
    $household = $user->households()->first();

    if (!$household) {
        return redirect()
            ->route('chores.index')
            ->withErrors(['household' => 'Kein Haushalt gefunden.']);
    }

    $quickPrimaryOptions = [
    'Geschirrspüler eingeräumt',
    'Geschirrspüler ausgeräumt',
    'Staubsaugen',
    'Wäsche gewaschen',
    'Müll entsorgt',
    'Essen gekocht',
];

$quickSecondaryOptions = [
    'Wäsche aufgehängt',
    'Wäsche abgehängt',
    'Wischen',
    'Bad geputzt',
    'Küche sauber',
    'Ordnung gemacht',
    'Gebaut (Handwerk)',
    'Auto gewaschen',
    'Auto getankt',
    'Gartenarbeit',
    'Einkauf erledigt',
];

    $allQuickOptions = array_merge($quickPrimaryOptions, $quickSecondaryOptions);

    $validated = $request->validate([
        'quick_type_primary' => ['nullable', 'in:' . implode(',', $allQuickOptions)],
        'quick_type_secondary' => ['nullable', 'in:' . implode(',', $allQuickOptions)],
        'room' => ['nullable', 'string', 'max:100'],
        'note' => ['nullable', 'string', 'max:255'],
    ]);

    $quickType = $validated['quick_type_primary'] ?? null;

    if (empty($quickType)) {
        $quickType = $validated['quick_type_secondary'] ?? null;
    }

    if (empty($quickType)) {
        return redirect()
            ->route('chores.index')
            ->withErrors(['quick_type_primary' => 'Bitte wähle eine Aktion aus.'])
            ->withInput();
    }

    HouseholdQuickEntry::create([
        'household_id' => $household->id,
        'user_id' => $user->id,
        'quick_type' => $quickType,
        'room' => $validated['room'] ?? null,
        'note' => $validated['note'] ?? null,
        'done_on' => now()->toDateString(),
    ]);

    return redirect()
        ->route('chores.index')
        ->with('status', 'Schnelle Erledigung wurde gespeichert.');
}

public function quickDestroy(HouseholdQuickEntry $entry): RedirectResponse
{
    $user = auth()->user();
    $household = $user->households()->first();

    if (!$household || $entry->household_id !== $household->id) {
        abort(403);
    }

    $entry->delete();

    return redirect()
        ->route('chores.index')
        ->with('status', 'Eintrag wurde entfernt.');
}

    public function complete(Request $request, Chore $chore): RedirectResponse
    {
        $user = $request->user();
        $household = $this->getHousehold($user);

        abort_unless($chore->household_id === $household->id, 403);

        $today = Carbon::today();
        $nextDueDate = $this->calculateNextDueDate($chore->frequency, $today);

        DB::transaction(function () use ($chore, $today, $nextDueDate) {
            $chore->update([
                'last_completed_date' => $today->toDateString(),
                'due_date' => $nextDueDate?->toDateString(),
            ]);
        });

        return redirect()
            ->route('chores.index')
            ->with('status', 'Aufgabe als erledigt markiert.');
    }

    private function getHousehold($user): Household
    {
        $household = $user->households()->first();

        abort_unless($household, 404, 'Kein Haushalt gefunden.');

        return $household;
    }

    private function calculateNextDueDate(string $frequency, Carbon $from): ?Carbon
    {
        return match ($frequency) {
            'daily' => $from->copy()->addDay(),
            'weekly' => $from->copy()->addWeek(),
            'biweekly' => $from->copy()->addWeeks(2),
            'monthly' => $from->copy()->addMonthNoOverflow(),
            default => null,
        };
    }
}