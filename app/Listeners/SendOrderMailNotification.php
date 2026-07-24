<?php

namespace App\Listeners;

use App\Events\SendOrderMail;
use App\Services\OrderMailNotificationBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderMailNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    public function handle(SendOrderMail $event)
    {
        try{
            $orderMailNotificationBuilderService = new OrderMailNotificationBuilder($event->info['order_id'], $event->info['status']);
            $orderMailNotificationBuilderService->send();
        } catch(\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
