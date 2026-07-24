<?php

namespace App\Listeners;

use App\Events\SendOrderSms;
use App\Services\OrderSmsNotificationBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderSmsNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    public function handle(SendOrderSms $event)
    {
        try{
            $orderSmsNotificationBuilderService = new OrderSmsNotificationBuilder($event->info['order_id'], $event->info['status']);
            $orderSmsNotificationBuilderService->send();
        } catch(\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
