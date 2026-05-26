<?php

namespace App\Http\Controllers;

use App\Mail\HouseholdInvitationMail;
use App\Models\Household;
use App\Models\HouseholdInvitation;
use App\Models\HouseholdMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class HouseholdController extends Controller
{
    public function settings(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $household = $user->households()->first();

        if (!$household) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['household' => 'Kein Haushalt gefunden.']);
        }

        $membership = HouseholdMember::query()
            ->where('household_id', $household->id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        $isAdmin = $membership?->role === 'admin';

        $members = HouseholdMember::query()
            ->with('user')
            ->where('household_id', $household->id)
            ->where('is_active', true)
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('display_name')
            ->get();

        $pendingInvitations = HouseholdInvitation::query()
            ->where('household_id', $household->id)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        return view('household.settings', [
            'user' => $user,
            'household' => $household,
            'membership' => $membership,
            'isAdmin' => $isAdmin,
            'members' => $members,
            'pendingInvitations' => $pendingInvitations,
        ]);
    }

    public function updateName(Request $request): RedirectResponse
    {
        $user = $request->user();
        $household = $this->getCurrentHouseholdOrAbort($user);
        $membership = $this->getCurrentMembershipOrAbort($user, $household);

        $this->ensureAdmin($membership);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $household->update([
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('household.settings')
            ->with('status', 'Haushaltsname wurde aktualisiert.');
    }

    public function addMember(Request $request): RedirectResponse
    {
        $user = $request->user();
        $household = $this->getCurrentHouseholdOrAbort($user);
        $membership = $this->getCurrentMembershipOrAbort($user, $household);

        $this->ensureAdmin($membership);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', Rule::in(['member', 'admin'])],
        ]);

        $email = mb_strtolower(trim($validated['email']));

        $existingUser = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($existingUser) {
            $alreadyMember = HouseholdMember::query()
                ->where('household_id', $household->id)
                ->where('user_id', $existingUser->id)
                ->where('is_active', true)
                ->exists();

            if ($alreadyMember) {
                return redirect()
                    ->route('household.settings')
                    ->withErrors(['email' => 'Dieser Benutzer ist bereits Mitglied im Haushalt.']);
            }
        }

        $invitation = HouseholdInvitation::query()
            ->where('household_id', $household->id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('status', 'pending')
            ->first();

        $isNew = false;

        if ($invitation) {
            $invitation->update([
                'role' => $validated['role'],
                'token' => Str::random(64),
                'invited_by_user_id' => $user->id,
                'expires_at' => now()->addDays(7),
            ]);
        } else {
            $invitation = HouseholdInvitation::create([
                'household_id' => $household->id,
                'email' => $email,
                'role' => $validated['role'],
                'token' => Str::random(64),
                'status' => 'pending',
                'invited_by_user_id' => $user->id,
                'expires_at' => now()->addDays(7),
            ]);

            $isNew = true;
        }

        $mailSent = $this->sendInvitationMail($invitation, $household, $user);

        return redirect()
            ->route('household.settings')
            ->with(
                'status',
                $mailSent
                    ? ($isNew ? 'Einladung wurde versendet.' : 'Einladung wurde aktualisiert und erneut versendet.')
                    : ($isNew ? 'Einladung wurde gespeichert. Mailversand ist aktuell nicht aktiv.' : 'Einladung wurde aktualisiert. Mailversand ist aktuell nicht aktiv.')
            );
    }

    public function resendInvitation(Request $request, HouseholdInvitation $invitation): RedirectResponse
    {
        $user = $request->user();
        $household = $this->getCurrentHouseholdOrAbort($user);
        $membership = $this->getCurrentMembershipOrAbort($user, $household);

        $this->ensureAdmin($membership);
        $this->ensureInvitationBelongsToHousehold($invitation, $household);

        if ($invitation->status !== 'pending') {
            return redirect()
                ->route('household.settings')
                ->withErrors(['invitation' => 'Nur offene Einladungen können erneut versendet werden.']);
        }

        $invitation->update([
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
            'invited_by_user_id' => $user->id,
        ]);

        $mailSent = $this->sendInvitationMail($invitation, $household, $user);

        return redirect()
            ->route('household.settings')
            ->with(
                'status',
                $mailSent
                    ? 'Einladung wurde erneut versendet.'
                    : 'Einladung wurde aktualisiert, aber Mailversand ist aktuell nicht aktiv.'
            );
    }

    public function destroyInvitation(Request $request, HouseholdInvitation $invitation): RedirectResponse
    {
        $user = $request->user();
        $household = $this->getCurrentHouseholdOrAbort($user);
        $membership = $this->getCurrentMembershipOrAbort($user, $household);

        $this->ensureAdmin($membership);
        $this->ensureInvitationBelongsToHousehold($invitation, $household);

        $invitation->delete();

        return redirect()
            ->route('household.settings')
            ->with('status', 'Einladung wurde entfernt.');
    }

    public function acceptInvitation(Request $request, string $token): RedirectResponse
    {
        $user = $request->user();

        $invitation = HouseholdInvitation::query()
            ->where('token', $token)
            ->firstOrFail();

        if ($invitation->status !== 'pending') {
            return redirect()
                ->route('dashboard')
                ->withErrors(['invitation' => 'Diese Einladung ist nicht mehr gültig.']);
        }

        if ($invitation->expires_at && now()->gt($invitation->expires_at)) {
            $invitation->update([
                'status' => 'expired',
            ]);

            return redirect()
                ->route('dashboard')
                ->withErrors(['invitation' => 'Diese Einladung ist abgelaufen.']);
        }

        if (mb_strtolower($user->email) !== mb_strtolower($invitation->email)) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['invitation' => 'Diese Einladung gehört zu einer anderen E-Mail-Adresse.']);
        }

        $member = HouseholdMember::query()
            ->where('household_id', $invitation->household_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            HouseholdMember::create([
                'household_id' => $invitation->household_id,
                'user_id' => $user->id,
                'role' => $invitation->role,
                'display_name' => $user->name,
                'is_active' => true,
            ]);
        } else {
            $member->update([
                'display_name' => $user->name,
                'is_active' => true,
            ]);
        }

        $invitation->update([
            'status' => 'accepted',
            'accepted_by_user_id' => $user->id,
            'accepted_at' => now(),
        ]);

        return redirect()
            ->route('household.settings')
            ->with('status', 'Einladung wurde angenommen. Du bist jetzt Mitglied im Haushalt.');
    }

    public function updateMemberRole(Request $request, HouseholdMember $member): RedirectResponse
    {
        $user = $request->user();
        $household = $this->getCurrentHouseholdOrAbort($user);
        $membership = $this->getCurrentMembershipOrAbort($user, $household);

        $this->ensureAdmin($membership);
        $this->ensureMemberBelongsToHousehold($member, $household);

        $validated = $request->validate([
            'role' => ['required', Rule::in(['member', 'admin'])],
        ]);

        if ($member->role === 'admin' && $validated['role'] !== 'admin') {
            $adminCount = HouseholdMember::query()
                ->where('household_id', $household->id)
                ->where('is_active', true)
                ->where('role', 'admin')
                ->count();

            if ($adminCount <= 1) {
                return redirect()
                    ->route('household.settings')
                    ->withErrors(['role' => 'Es muss mindestens ein Admin im Haushalt bleiben.']);
            }
        }

        $member->update([
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('household.settings')
            ->with('status', 'Mitgliedsrolle wurde aktualisiert.');
    }

    public function destroyMember(Request $request, HouseholdMember $member): RedirectResponse
    {
        $user = $request->user();
        $household = $this->getCurrentHouseholdOrAbort($user);
        $membership = $this->getCurrentMembershipOrAbort($user, $household);

        $this->ensureAdmin($membership);
        $this->ensureMemberBelongsToHousehold($member, $household);

        if ((int) $member->user_id === (int) $user->id) {
            return redirect()
                ->route('household.settings')
                ->withErrors(['member' => 'Du kannst dich nicht selbst aus dem Haushalt entfernen.']);
        }

        if ($member->role === 'admin') {
            $adminCount = HouseholdMember::query()
                ->where('household_id', $household->id)
                ->where('is_active', true)
                ->where('role', 'admin')
                ->count();

            if ($adminCount <= 1) {
                return redirect()
                    ->route('household.settings')
                    ->withErrors(['member' => 'Der letzte Admin kann nicht entfernt werden.']);
            }
        }

        $member->delete();

        return redirect()
            ->route('household.settings')
            ->with('status', 'Mitglied wurde entfernt.');
    }

    private function sendInvitationMail(HouseholdInvitation $invitation, Household $household, User $inviter): bool
{
    $acceptUrl = route('household.invitations.accept', ['token' => $invitation->token]);

    Mail::to($invitation->email)->send(
        new HouseholdInvitationMail(
            invitation: $invitation,
            acceptUrl: $acceptUrl,
            householdName: $household->name,
            inviterName: $inviter->name
        )
    );

    return true;
}

    private function getCurrentHouseholdOrAbort(User $user): Household
    {
        $household = $user->households()->first();

        abort_unless($household, 404, 'Kein Haushalt gefunden.');

        return $household;
    }

    private function getCurrentMembershipOrAbort(User $user, Household $household): HouseholdMember
    {
        $membership = HouseholdMember::query()
            ->where('household_id', $household->id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        abort_unless($membership, 403, 'Keine Berechtigung.');

        return $membership;
    }

    private function ensureAdmin(HouseholdMember $membership): void
    {
        abort_unless($membership->role === 'admin', 403, 'Nur Admins dürfen das.');
    }

    private function ensureMemberBelongsToHousehold(HouseholdMember $member, Household $household): void
    {
        abort_unless((int) $member->household_id === (int) $household->id, 403);
    }

    private function ensureInvitationBelongsToHousehold(HouseholdInvitation $invitation, Household $household): void
    {
        abort_unless((int) $invitation->household_id === (int) $household->id, 403);
    }
}