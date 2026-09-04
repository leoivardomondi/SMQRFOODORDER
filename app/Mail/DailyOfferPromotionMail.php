<?php

namespace App\Mail;

use App\Models\ThemeSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyOfferPromotionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $offerItems;
    public $title;
    public $timeSlot;

    /**
     * Create a new message instance.
     *
     * @param mixed $offerItems
     * @param string $timeSlot
     * @return void
     */
    public function __construct($offerItems, string $timeSlot = '12PM')
    {
        $this->offerItems = $offerItems;
        $this->timeSlot   = $timeSlot;
        $this->title      = $timeSlot === '12PM' 
            ? "☀️ Lunchtime Special Deals & Daily Offers!" 
            : "🌙 Evening Special Deals & Daily Offers!";
    }

    public function build()
    {
        $logo = ThemeSetting::where('key', 'theme_logo')->first();

        return $this
            ->subject($this->title)
            ->view('emails.daily_offer_promotion', [
                'logoUrl'    => $logo?->logo,
                'offerItems' => $this->offerItems,
                'timeSlot'   => $this->timeSlot,
                'title'      => $this->title,
            ]);
    }
}
