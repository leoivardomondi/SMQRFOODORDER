<?php

namespace App\Listeners;


use App\Events\SendOrderGotMail;
use App\Services\OrderGotMailNotificationBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderGotMailNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    public function handle(SendOrderGotMail $event): void
    {
        try{
            $orderMailNotificationBuilderService = new OrderGotMailNotificationBuilder($event->info['order_id']);
            $orderMailNotificationBuilderService->send();
        } catch(\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
