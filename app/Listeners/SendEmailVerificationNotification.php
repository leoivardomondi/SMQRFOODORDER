<?php

namespace App\Listeners;

use App\Events\SendEmailVerification;
use App\Mail\VerifyEmail;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailVerificationNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\SendEmailVerification $event
     * @return void
     */
    public function handle(SendEmailVerification $event)
    {
        try {
            Mail::to($event->info['email'])->send(new VerifyEmail($event->info['pin']));
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
