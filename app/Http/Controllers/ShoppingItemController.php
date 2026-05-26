<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\ShoppingItem;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShoppingItemController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $household = $user->households()->first();

        if (!$household) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['household' => 'Kein Haushalt gefunden.']);
        }

        $shoppingItems = ShoppingItem::query()
            ->with(['addedByUser', 'boughtByUser'])
            ->where('household_id', $household->id)
            ->orderBy('is_bought')
            ->orderByDesc('created_at')
            ->get();

        $trips = Trip::query()
            ->where('household_id', $household->id)
            ->orderBy('start_date')
            ->orderBy('title')
            ->get();

        $groupedItems = collect();
        $groupedItems['household'] = $shoppingItems->whereNull('trip_id')->values();

        foreach ($trips as $trip) {
            $groupedItems['trip_' . $trip->id] = $shoppingItems
                ->where('trip_id', $trip->id)
                ->values();
        }

        return view('shopping.index', [
            'household' => $household,
            'shoppingItems' => $shoppingItems,
            'trips' => $trips,
            'groupedItems' => $groupedItems,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $household = $this->getHousehold($user);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'quantity' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'trip_id' => [
                'nullable',
                'integer',
                Rule::exists('trips', 'id')->where(function ($query) use ($household) {
                    $query->where('household_id', $household->id);
                }),
            ],
        ]);

        $item = ShoppingItem::create([
            'household_id' => $household->id,
            'trip_id' => $data['trip_id'] ?? null,
            'title' => $data['title'],
            'quantity' => $data['quantity'] ?? null,
            'category' => $data['category'] ?? null,
            'note' => $data['note'] ?? null,
            'added_by_user_id' => $user->id,
            'bought_by_user_id' => null,
            'is_bought' => false,
            'bought_at' => null,
            'actual_price' => null,
        ]);

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'Artikel wurde hinzugefügt.',
                'item' => $this->buildShoppingItemResponse($item),
                'list_count' => $this->getListCount($household->id, $item->trip_id),
            ]);
        }

        return redirect()
            ->route('shopping.index')
            ->with('status', 'Artikel wurde zur Einkaufsliste hinzugefügt.');
    }

    public function toggleBought(Request $request, ShoppingItem $shoppingItem): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $household = $this->getHousehold($user);

        abort_unless($shoppingItem->household_id === $household->id, 403);

        if ($shoppingItem->is_bought) {
            $shoppingItem->update([
                'is_bought' => false,
                'bought_by_user_id' => null,
                'bought_at' => null,
                'actual_price' => null,
            ]);

            if ($this->wantsJson($request)) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Artikel ist wieder offen.',
                    'item_id' => $shoppingItem->id,
                    'is_bought' => false,
                    'actual_price' => null,
                    'list_key' => $this->buildListKey($shoppingItem->trip_id),
                    'list_count' => $this->getListCount($household->id, $shoppingItem->trip_id),
                ]);
            }

            return redirect()
                ->route('shopping.index')
                ->with('status', 'Artikel ist wieder offen.');
        }

        $validated = $request->validate([
            'actual_price' => ['required', 'numeric', 'min:0'],
        ]);

        $shoppingItem->update([
            'is_bought' => true,
            'bought_by_user_id' => $user->id,
            'bought_at' => now(),
            'actual_price' => $validated['actual_price'],
        ]);

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'Artikel wurde als gekauft markiert.',
                'item_id' => $shoppingItem->id,
                'is_bought' => true,
                'actual_price' => (float) $shoppingItem->actual_price,
                'list_key' => $this->buildListKey($shoppingItem->trip_id),
                'list_count' => $this->getListCount($household->id, $shoppingItem->trip_id),
            ]);
        }

        return redirect()
            ->route('shopping.index')
            ->with('status', 'Artikel wurde als gekauft markiert.');
    }

    public function destroy(Request $request, ShoppingItem $shoppingItem): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $household = $this->getHousehold($user);

        abort_unless($shoppingItem->household_id === $household->id, 403);

        $tripId = $shoppingItem->trip_id;
        $itemId = $shoppingItem->id;

        $shoppingItem->delete();

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'Artikel wurde gelöscht.',
                'item_id' => $itemId,
                'list_key' => $this->buildListKey($tripId),
                'list_count' => $this->getListCount($household->id, $tripId),
            ]);
        }

        return redirect()
            ->route('shopping.index')
            ->with('status', 'Artikel wurde gelöscht.');
    }

    private function buildShoppingItemResponse(ShoppingItem $item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'quantity' => $item->quantity,
            'category' => $item->category,
            'note' => $item->note,
            'is_bought' => (bool) $item->is_bought,
            'actual_price' => $item->actual_price !== null ? (float) $item->actual_price : null,
            'list_key' => $this->buildListKey($item->trip_id),
            'toggle_url' => route('shopping.toggleBought', $item),
            'delete_url' => route('shopping.destroy', $item),
        ];
    }

    private function buildListKey(?int $tripId): string
    {
        return $tripId ? 'trip_' . $tripId : 'household';
    }

    private function getListCount(int $householdId, ?int $tripId): int
    {
        return ShoppingItem::query()
            ->where('household_id', $householdId)
            ->when(
                $tripId,
                fn ($query) => $query->where('trip_id', $tripId),
                fn ($query) => $query->whereNull('trip_id')
            )
            ->count();
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || $request->wantsJson()
            || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    private function getHousehold($user): Household
    {
        $household = $user->households()->first();

        abort_unless($household, 404, 'Kein Haushalt gefunden.');

        return $household;
    }
}