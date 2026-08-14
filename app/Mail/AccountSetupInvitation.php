<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountSetupInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $userName, public string $setupUrl)
    {
    }

    public function build(): self
    {
        return $this->subject('Set up your ' . config('app.name') . ' account')
            ->markdown('emails.account-setup-invitation');
    }
}
