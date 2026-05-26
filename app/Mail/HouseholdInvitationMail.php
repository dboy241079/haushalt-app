<?php

namespace App\Mail;

use App\Models\HouseholdInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HouseholdInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public HouseholdInvitation $invitation;
    public string $acceptUrl;
    public string $householdName;
    public string $inviterName;

    public function __construct(
        HouseholdInvitation $invitation,
        string $acceptUrl,
        string $householdName,
        string $inviterName
    ) {
        $this->invitation = $invitation;
        $this->acceptUrl = $acceptUrl;
        $this->householdName = $householdName;
        $this->inviterName = $inviterName;
    }

    public function build(): self
    {
        return $this
            ->subject('Einladung zu FamilyHelper')
            ->view('emails.household-invitation');
    }
}