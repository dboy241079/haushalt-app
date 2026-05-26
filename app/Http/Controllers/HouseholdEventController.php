<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\HouseholdEvent;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\HouseholdEventAttachment;
use Illuminate\Support\Facades\Storage;



class HouseholdEventController extends Controller
{
    private const TYPES = [
        'Arzt',
        'Geburtstag',
        'Familie',
        'Schule',
        'Freizeit',
        'Urlaub',
        'Ferien',
        'Sonstiges',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();
        $household = $this->getHousehold($user);

        $upcomingEvents = HouseholdEvent::with('attachments')
    ->where('household_id', $household->id)
    ->where('starts_at', '>=', now())
    ->orderBy('starts_at')
    ->get();

$pastEvents = HouseholdEvent::with('attachments')
    ->where('household_id', $household->id)
    ->where('starts_at', '<', now())
    ->orderByDesc('starts_at')
    ->get();

       return view('events.index', [
    'user' => $user,
    'household' => $household,
    'upcomingEvents' => $upcomingEvents,
    'types' => self::TYPES,
]);
    }

    public function store(Request $request)
{
    $user = auth()->user();
    $household = $user->households()->first();

    if (!$household) {
        return redirect()
            ->route('events.index')
            ->withErrors(['household' => 'Kein Haushalt gefunden.']);
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'type' => ['required', 'string', 'max:100'],
        'starts_at' => ['required', 'date'],
        'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        'all_day' => ['nullable', 'boolean'],
        'location' => ['nullable', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'attachments' => ['nullable', 'array'],
        'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:10240'],
    ]);

    $event = HouseholdEvent::create([
        'household_id' => $household->id,
        'title' => $validated['title'],
        'type' => $validated['type'],
        'description' => $validated['description'] ?? null,
        'location' => $validated['location'] ?? null,
        'starts_at' => $validated['starts_at'],
        'ends_at' => $validated['ends_at'] ?? null,
        'all_day' => $request->boolean('all_day'),
        'recurrence' => null,
        'created_by_user_id' => $user->id,
    ]);

    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            $storedPath = $file->store('events/' . $event->id, 'public');

            $event->attachments()->create([
                'household_event_id' => $event->id,
                'original_name' => $file->getClientOriginalName(),
                'file_name' => basename($storedPath),
                'file_path' => $storedPath,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    return redirect()
        ->route('events.index')
        ->with('status', 'Termin wurde erfolgreich gespeichert.');
}

    public function destroy(Request $request, HouseholdEvent $event): RedirectResponse
    {
        $user = $request->user();
        $household = $this->getHousehold($user);

        abort_unless($event->household_id === $household->id, 403);

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('status', 'Termin wurde gelöscht.');
    }

    public function feed(Request $request)
{
    $user = $request->user();
    $household = $this->getHousehold($user);

    $start = $request->query('start');
    $end = $request->query('end');

    $events = HouseholdEvent::with(['attachments', 'reminderInsurance'])
        ->where('household_id', $household->id)
        ->when($start && $end, function ($query) use ($start, $end) {
            $query
                ->where('starts_at', '<', $end)
                ->where(function ($subQuery) use ($start) {
                    $subQuery
                        ->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', $start);
                });
        })
        ->orderBy('starts_at')
        ->get()
        ->map(function (HouseholdEvent $event) {
            $colors = $this->getEventColors($event->type);

            return [
                'id' => (string) $event->id,
                'title' => $event->title,
                'start' => $event->all_day
                    ? $event->starts_at?->toDateString()
                    : $event->starts_at?->toIso8601String(),
                'end' => $event->all_day && $event->ends_at
                    ? $event->ends_at->toDateString()
                    : $event->ends_at?->toIso8601String(),
                'allDay' => (bool) $event->all_day,
                'backgroundColor' => $colors['background'],
                'borderColor' => $colors['border'],
                'textColor' => $colors['text'],
                'extendedProps' => [
                    'type' => $event->type,
                    'location' => $event->location,
                    'description' => $event->description,
                    'insurance_id' => $event->reminderInsurance?->id,
                    'insurance_url' => $event->reminderInsurance
                        ? route('insurances.show', $event->reminderInsurance)
                        : null,
                    'is_insurance_reminder' => (bool) $event->reminderInsurance,
                ],
            ];
        });

    return response()->json($events);
}
    private function getEventColors(string $type): array
    {
        return match ($type) {
            'Arzt' => [
                'background' => '#ef4444',
                'border' => '#dc2626',
                'text' => '#ffffff',
            ],
            'Geburtstag' => [
                'background' => '#ec4899',
                'border' => '#db2777',
                'text' => '#ffffff',
            ],
            'Familie' => [
                'background' => '#f97316',
                'border' => '#ea580c',
                'text' => '#ffffff',
            ],
            'Schule' => [
                'background' => '#06b6d4',
                'border' => '#0891b2',
                'text' => '#ffffff',
            ],
            'Freizeit' => [
                'background' => '#3b82f6',
                'border' => '#2563eb',
                'text' => '#ffffff',
            ],
            'Urlaub' => [
                'background' => '#22c55e',
                'border' => '#16a34a',
                'text' => '#ffffff',
            ],
            'Ferien' => [
                'background' => '#eab308',
                'border' => '#ca8a04',
                'text' => '#111827',
            ],
            default => [
                'background' => '#8b5cf6',
                'border' => '#7c3aed',
                'text' => '#ffffff',
            ],
        };
    }

    private function getHousehold($user): Household
    {
        $household = $user->households()->first();

        abort_unless($household, 404, 'Kein Haushalt gefunden.');

        return $household;
    }
    public function update(Request $request, HouseholdEvent $event): RedirectResponse
{
    $user = $request->user();
    $household = $this->getHousehold($user);

    abort_unless($event->household_id === $household->id, 403);

    $data = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'type' => ['required', 'string', Rule::in(self::TYPES)],
        'starts_at' => ['required', 'date'],
        'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        'location' => ['nullable', 'string', 'max:255'],
        'description' => ['nullable', 'string', 'max:2000'],
        'all_day' => ['nullable', 'boolean'],
    ]);

    $isAllDay = (bool) ($data['all_day'] ?? false);

    if ($isAllDay) {
        $start = Carbon::parse($data['starts_at'])->startOfDay();

        if (!empty($data['ends_at'])) {
            $end = Carbon::parse($data['ends_at'])->addDay()->startOfDay();
        } else {
            $end = $start->copy()->addDay();
        }
    } else {
        $start = Carbon::parse($data['starts_at']);
        $end = !empty($data['ends_at']) ? Carbon::parse($data['ends_at']) : null;
    }

    $event->update([
        'title' => $data['title'],
        'type' => $data['type'],
        'description' => $data['description'] ?? null,
        'location' => $data['location'] ?? null,
        'starts_at' => $start,
        'ends_at' => $end,
        'all_day' => $isAllDay,
    ]);

    return redirect()
        ->route('events.index')
        ->with('status', 'Termin wurde aktualisiert.');
}
}