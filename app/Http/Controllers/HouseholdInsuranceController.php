<?php

namespace App\Http\Controllers;

use App\Models\HouseholdInsurance;
use App\Models\HouseholdInsuranceDocument;
use App\Models\HouseholdEvent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HouseholdInsuranceController extends Controller
{
    private const INSURANCE_TYPES = [
        'Haftpflicht',
        'Hausrat',
        'KFZ',
        'Rechtsschutz',
        'Unfall',
        'Lebensversicherung',
        'Krankenversicherung',
        'Zahnzusatz',
        'Gebäudeversicherung',
        'Tierversicherung',
        'Sonstiges',
    ];

    private const PAYMENT_INTERVALS = [
        'monthly' => 'Monatlich',
        'quarterly' => 'Vierteljährlich',
        'half_yearly' => 'Halbjährlich',
        'yearly' => 'Jährlich',
    ];

    private const STATUS_OPTIONS = [
        'active' => 'Aktiv',
        'cancelled' => 'Gekündigt',
        'ended' => 'Beendet',
    ];

    private const DOCUMENT_TYPES = [
        'contract' => 'Vertrag',
        'letter' => 'Brief',
        'invoice' => 'Rechnung',
        'claim' => 'Schaden',
        'other' => 'Sonstiges',
    ];

    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $household = $user->households()->first();

        if (!$household) {
            return redirect()
                ->route('dashboard')
                ->with('status', 'Bitte zuerst einen Haushalt anlegen.');
        }

        $insurances = HouseholdInsurance::query()
            ->with('documents')
            ->where('household_id', $household->id)
            ->orderByRaw("
                CASE
                    WHEN status = 'active' THEN 0
                    WHEN status = 'cancelled' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('ends_at')
            ->orderBy('insurance_type')
            ->get();

        $activeInsurances = $insurances
            ->where('status', 'active')
            ->values();

        $inactiveInsurances = $insurances
            ->reject(fn (HouseholdInsurance $insurance) => $insurance->status === 'active')
            ->values();

        $summary = [
            'active_count' => $activeInsurances->count(),
            'monthly_total' => $activeInsurances->sum(
                fn (HouseholdInsurance $insurance) => $this->toMonthlyAmount((float) $insurance->amount, $insurance->payment_interval)
            ),
            'yearly_total' => $activeInsurances->sum(
                fn (HouseholdInsurance $insurance) => $this->toYearlyAmount((float) $insurance->amount, $insurance->payment_interval)
            ),
            'ending_soon_count' => $activeInsurances->filter(
                fn (HouseholdInsurance $insurance) => $this->isEndingSoon($insurance)
            )->count(),
            'notice_soon_count' => $activeInsurances->filter(
                fn (HouseholdInsurance $insurance) => $this->isNoticePeriodSoon($insurance)
            )->count(),
        ];

        return view('insurances.index', [
            'household' => $household,
            'insuranceTypes' => self::INSURANCE_TYPES,
            'paymentIntervals' => self::PAYMENT_INTERVALS,
            'statusOptions' => self::STATUS_OPTIONS,
            'documentTypes' => self::DOCUMENT_TYPES,
            'activeInsurances' => $activeInsurances,
            'inactiveInsurances' => $inactiveInsurances,
            'summary' => $summary,
        ]);
    }

    public function show(HouseholdInsurance $insurance): View
    {
        $this->ensureBelongsToCurrentHousehold($insurance);

        $insurance->load([
            'documents' => function ($query) {
                $query->with('uploadedByUser')->orderByDesc('created_at');
            },
        ]);

        return view('insurances.show', [
            'insurance' => $insurance,
            'paymentIntervals' => self::PAYMENT_INTERVALS,
            'statusOptions' => self::STATUS_OPTIONS,
            'insuranceTypes' => self::INSURANCE_TYPES,
            'documentTypes' => self::DOCUMENT_TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $household = $user->households()->first();

        if (!$household) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['household' => 'Kein Haushalt gefunden.']);
        }

        $validated = $request->validate([
            'provider' => ['nullable', 'string', 'max:255'],
            'provider_street' => ['nullable', 'string', 'max:255'],
            'provider_zip' => ['nullable', 'string', 'max:20'],
            'provider_city' => ['nullable', 'string', 'max:255'],
            'provider_email' => ['nullable', 'email', 'max:255'],

            'policy_number' => ['nullable', 'string', 'max:255'],
            'insurance_type' => ['required', 'string', 'max:100'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'cancellation_notice_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'payment_interval' => ['required', 'in:monthly,quarterly,half_yearly,yearly'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,cancelled,ended'],
            'notes' => ['nullable', 'string'],

            'insured_first_name' => ['nullable', 'string', 'max:255'],
            'insured_last_name' => ['nullable', 'string', 'max:255'],
            'insured_street' => ['nullable', 'string', 'max:255'],
            'insured_zip' => ['nullable', 'string', 'max:20'],
            'insured_city' => ['nullable', 'string', 'max:255'],
            'insured_email' => ['nullable', 'email', 'max:255'],
            'insured_phone' => ['nullable', 'string', 'max:50'],

            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:10240'],
            'document_types' => ['nullable', 'array'],
            'document_types.*' => ['nullable', 'in:contract,letter,invoice,claim,other'],
        ]);

        $insurance = HouseholdInsurance::create([
            'household_id' => $household->id,
            'title' => $validated['insurance_type'],
            'provider' => $validated['provider'] ?? null,
            'provider_street' => $validated['provider_street'] ?? null,
            'provider_zip' => $validated['provider_zip'] ?? null,
            'provider_city' => $validated['provider_city'] ?? null,
            'provider_email' => $validated['provider_email'] ?? null,

            'policy_number' => $validated['policy_number'] ?? null,
            'insurance_type' => $validated['insurance_type'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?? null,
            'cancellation_notice_days' => $validated['cancellation_notice_days'] ?? null,
            'payment_interval' => $validated['payment_interval'],
            'amount' => $validated['amount'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'created_by_user_id' => $user->id,

            'insured_first_name' => $validated['insured_first_name'] ?? null,
            'insured_last_name' => $validated['insured_last_name'] ?? null,
            'insured_street' => $validated['insured_street'] ?? null,
            'insured_zip' => $validated['insured_zip'] ?? null,
            'insured_city' => $validated['insured_city'] ?? null,
            'insured_email' => $validated['insured_email'] ?? null,
            'insured_phone' => $validated['insured_phone'] ?? null,
        ]);

        $this->syncReminderEvent($insurance);

        if ($request->hasFile('documents')) {
            $documentTypes = $request->input('document_types', []);

            foreach ($request->file('documents') as $index => $file) {
                $storedPath = $file->store('insurances/' . $insurance->id, 'public');

                $insurance->documents()->create([
                    'household_insurance_id' => $insurance->id,
                    'document_type' => $documentTypes[$index] ?? 'other',
                    'document_title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'uploaded_by_user_id' => $user->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name' => basename($storedPath),
                    'file_path' => $storedPath,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('insurances.index')
            ->with('status', 'Versicherung wurde erfolgreich gespeichert.');
    }

    public function update(Request $request, HouseholdInsurance $insurance): RedirectResponse
    {
        $this->ensureBelongsToCurrentHousehold($insurance);

        $validated = $request->validate([
            'provider' => ['nullable', 'string', 'max:255'],
            'provider_street' => ['nullable', 'string', 'max:255'],
            'provider_zip' => ['nullable', 'string', 'max:20'],
            'provider_city' => ['nullable', 'string', 'max:255'],
            'provider_email' => ['nullable', 'email', 'max:255'],

            'policy_number' => ['nullable', 'string', 'max:255'],
            'insurance_type' => ['required', 'string', 'max:100'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'cancellation_notice_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'payment_interval' => ['required', 'in:monthly,quarterly,half_yearly,yearly'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,cancelled,ended'],
            'notes' => ['nullable', 'string'],

            'insured_first_name' => ['nullable', 'string', 'max:255'],
            'insured_last_name' => ['nullable', 'string', 'max:255'],
            'insured_street' => ['nullable', 'string', 'max:255'],
            'insured_zip' => ['nullable', 'string', 'max:20'],
            'insured_city' => ['nullable', 'string', 'max:255'],
            'insured_email' => ['nullable', 'email', 'max:255'],
            'insured_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $insurance->update([
            'title' => $validated['insurance_type'],
            'provider' => $validated['provider'] ?? null,
            'provider_street' => $validated['provider_street'] ?? null,
            'provider_zip' => $validated['provider_zip'] ?? null,
            'provider_city' => $validated['provider_city'] ?? null,
            'provider_email' => $validated['provider_email'] ?? null,

            'policy_number' => $validated['policy_number'] ?? null,
            'insurance_type' => $validated['insurance_type'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?? null,
            'cancellation_notice_days' => $validated['cancellation_notice_days'] ?? null,
            'payment_interval' => $validated['payment_interval'],
            'amount' => $validated['amount'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,

            'insured_first_name' => $validated['insured_first_name'] ?? null,
            'insured_last_name' => $validated['insured_last_name'] ?? null,
            'insured_street' => $validated['insured_street'] ?? null,
            'insured_zip' => $validated['insured_zip'] ?? null,
            'insured_city' => $validated['insured_city'] ?? null,
            'insured_email' => $validated['insured_email'] ?? null,
            'insured_phone' => $validated['insured_phone'] ?? null,
        ]);

        $this->syncReminderEvent($insurance->fresh());

        return redirect()
            ->route('insurances.show', $insurance)
            ->with('status', 'Versicherung wurde aktualisiert.');
    }

    public function destroy(HouseholdInsurance $insurance): RedirectResponse
    {
        $this->ensureBelongsToCurrentHousehold($insurance);

        $insurance->load('documents', 'reminderEvent');

        foreach ($insurance->documents as $document) {
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
        }

        if ($insurance->reminderEvent) {
            $insurance->reminderEvent->delete();
        }

        $insurance->delete();

        return back()->with('status', 'Versicherung wurde gelöscht.');
    }

    public function storeDocument(Request $request, HouseholdInsurance $insurance): RedirectResponse
    {
        $this->ensureBelongsToCurrentHousehold($insurance);

        $validated = $request->validate([
            'document_title' => ['required', 'string', 'max:255'],
            'document_type' => ['nullable', 'in:contract,letter,invoice,claim,other'],
            'documents' => ['required', 'array'],
            'documents.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:10240'],
        ]);

        foreach ($request->file('documents') as $file) {
            $storedPath = $file->store('insurances/' . $insurance->id, 'public');

            $insurance->documents()->create([
                'household_insurance_id' => $insurance->id,
                'document_type' => $validated['document_type'] ?? 'other',
                'document_title' => $validated['document_title'],
                'uploaded_by_user_id' => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'file_name' => basename($storedPath),
                'file_path' => $storedPath,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()
            ->route('insurances.show', $insurance)
            ->with('status', 'Dokument(e) wurden hochgeladen.');
    }

    public function destroyDocument(HouseholdInsurance $insurance, HouseholdInsuranceDocument $document): RedirectResponse
    {
        $this->ensureBelongsToCurrentHousehold($insurance);

        if ($document->household_insurance_id !== $insurance->id) {
            abort(404);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('status', 'Dokument wurde gelöscht.');
    }

    public function downloadCancellationPdf(HouseholdInsurance $insurance)
    {
        $this->ensureBelongsToCurrentHousehold($insurance);

        if (!$this->canPrepareCancellation($insurance)) {
            return redirect()
                ->route('insurances.show', $insurance)
                ->withErrors(['cancellation' => 'Die Kündigungsfrist ist noch nicht erreicht.']);
        }

        $insurance->loadMissing('household');

        $noticeDays = $insurance->cancellation_notice_days ?? 30;
        $today = now();

        $pdf = Pdf::loadView('insurances.pdf.cancellation', [
            'insurance' => $insurance,
            'user' => auth()->user(),
            'today' => $today,
            'noticeDays' => $noticeDays,
        ])->setPaper('a4', 'portrait');

        $filename = 'kuendigung-' . Str::slug($insurance->title ?: 'versicherung') . '-' . $today->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    private function syncReminderEvent(HouseholdInsurance $insurance): void
    {
        if (!$insurance->household_id) {
            return;
        }

        if ($insurance->status !== 'active' || !$insurance->ends_at) {
            if ($insurance->reminderEvent) {
                $insurance->reminderEvent->delete();
                $insurance->updateQuietly(['reminder_event_id' => null]);
            }
            return;
        }

        $noticeDays = $insurance->cancellation_notice_days ?? 30;
        $reminderDate = $insurance->ends_at->copy()->subDays((int) $noticeDays);

        $title = 'Versicherung prüfen: ' . $insurance->insurance_type;

        $descriptionLines = [
            'Versicherung: ' . $insurance->insurance_type,
            'Anbieter: ' . ($insurance->provider ?: '-'),
            'Versicherungsnummer: ' . ($insurance->policy_number ?: '-'),
            'Vertragsende: ' . $insurance->ends_at->format('d.m.Y'),
            'Kündigungsfrist: ' . $noticeDays . ' Tage',
            'Versicherungsnehmer: ' . trim(($insurance->insured_first_name ?? '') . ' ' . ($insurance->insured_last_name ?? '')),
        ];

        if (!empty($insurance->notes)) {
            $descriptionLines[] = 'Notiz: ' . $insurance->notes;
        }

        $description = implode("\n", $descriptionLines);

        $eventData = [
            'household_id' => $insurance->household_id,
            'title' => $title,
            'type' => 'Sonstiges',
            'description' => $description,
            'location' => $insurance->provider,
            'starts_at' => $reminderDate->copy()->startOfDay(),
            'ends_at' => $reminderDate->copy()->addDay()->startOfDay(),
            'all_day' => true,
            'recurrence' => null,
            'created_by_user_id' => $insurance->created_by_user_id,
        ];

        if ($insurance->reminder_event_id) {
            $event = HouseholdEvent::find($insurance->reminder_event_id);

            if ($event) {
                $event->update($eventData);
                return;
            }
        }

        $event = HouseholdEvent::create($eventData);

        $insurance->updateQuietly([
            'reminder_event_id' => $event->id,
        ]);
    }

    private function canPrepareCancellation(HouseholdInsurance $insurance): bool
    {
        if (!$insurance->ends_at) {
            return false;
        }

        $noticeDays = $insurance->cancellation_notice_days ?? 30;
        $noticeDate = $insurance->ends_at->copy()->subDays((int) $noticeDays);

        return now()->startOfDay()->gte($noticeDate->startOfDay());
    }

    private function ensureBelongsToCurrentHousehold(HouseholdInsurance $insurance): void
    {
        $householdIds = auth()->user()->households()->pluck('households.id');

        if (!$householdIds->contains($insurance->household_id)) {
            abort(403);
        }
    }

    private function toMonthlyAmount(float $amount, string $interval): float
    {
        return match ($interval) {
            'monthly' => $amount,
            'quarterly' => $amount / 3,
            'half_yearly' => $amount / 6,
            'yearly' => $amount / 12,
            default => 0,
        };
    }

    private function toYearlyAmount(float $amount, string $interval): float
    {
        return match ($interval) {
            'monthly' => $amount * 12,
            'quarterly' => $amount * 4,
            'half_yearly' => $amount * 2,
            'yearly' => $amount,
            default => 0,
        };
    }

    private function isEndingSoon(HouseholdInsurance $insurance): bool
    {
        return $insurance->ends_at
            && $insurance->ends_at->between(now()->startOfDay(), now()->copy()->addDays(30)->endOfDay());
    }

    private function isNoticePeriodSoon(HouseholdInsurance $insurance): bool
    {
        if (!$insurance->ends_at || !$insurance->cancellation_notice_days) {
            return false;
        }

        $noticeDate = $insurance->ends_at->copy()->subDays((int) $insurance->cancellation_notice_days);

        return $noticeDate->between(now()->startOfDay(), now()->copy()->addDays(30)->endOfDay());
    }
}