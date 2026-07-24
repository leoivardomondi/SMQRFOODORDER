<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminSetupInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $adminName, public string $setupUrl)
    {
    }

    public function build(): self
    {
        return $this->subject('Set up your ' . config('app.name') . ' administrator account')
            ->markdown('emails.admin-setup-invitation');
    }
}
