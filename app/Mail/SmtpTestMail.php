<?php

namespace App\Mail;

use App\Models\ThemeSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SmtpTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $configDetails;
    public string $recipientEmail;

    public function __construct(array $configDetails, string $recipientEmail)
    {
        $this->configDetails  = $configDetails;
        $this->recipientEmail = $recipientEmail;
    }

    public function build()
    {
        $logo = ThemeSetting::where('key', 'theme_logo')->first();

        return $this
            ->subject('[SMTP Test] Connection Successful - ' . config('app.name', 'Bwibo Restaurant'))
            ->view('emails.smtpTest', [
                'logoUrl'        => $logo?->logo,
                'configDetails'  => $this->configDetails,
                'recipientEmail' => $this->recipientEmail,
                'timestamp'      => now()->format('d M Y, h:i:s A T'),
            ]);
    }
}
