<?php

namespace App\Mail;

use App\Enums\PaymentStatus;
use App\Models\ThemeSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderGotMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public mixed $order;
    public mixed $alertMessage;

    public function __construct($order, $alertMessage = null)
    {
        $this->order = $order;
        $this->alertMessage = $alertMessage;
    }

    public function build()
    {
        $label = (int) $this->order->payment_status === PaymentStatus::PAID
            ? 'Paid order alert'
            : 'New order alert';
        $logo = ThemeSetting::where('key', 'theme_logo')->first();

        return $this
            ->subject("{$label} #{$this->order->order_serial_no}")
            ->view('emails.orderGotHtml', [
                'logoUrl' => $logo?->logo,
                'alertMessage' => $this->alertMessage,
            ])
            ->text('emails.orderGotText');
    }
}
