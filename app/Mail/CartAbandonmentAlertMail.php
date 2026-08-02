<?php

namespace App\Mail;

use App\Models\ThemeSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CartAbandonmentAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $customerPhone;
    public $customerEmail;
    public $branch;
    public $cartItems;
    public $total;

    public function __construct($customerName, $customerPhone, $customerEmail, $branch, $cartItems, $total)
    {
        $this->customerName  = $customerName;
        $this->customerPhone = $customerPhone;
        $this->customerEmail = $customerEmail;
        $this->branch        = $branch;
        $this->cartItems     = $cartItems;
        $this->total         = $total;
    }

    public function build()
    {
        $logo = ThemeSetting::where('key', 'theme_logo')->first();
        $branchName = $this->branch ? $this->branch->name : 'Default Branch';

        return $this
            ->subject("🚨 ABANDONED CART ALERT: {$this->customerName} ({$this->customerPhone}) - {$branchName}")
            ->view('emails.cartAbandonmentAlertHtml', [
                'logoUrl' => $logo?->logo,
                'customerName' => $this->customerName,
                'customerPhone' => $this->customerPhone,
                'customerEmail' => $this->customerEmail,
                'branch' => $this->branch,
                'cartItems' => $this->cartItems,
                'total' => $this->total,
            ]);
    }
}
