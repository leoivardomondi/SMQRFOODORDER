<?php

namespace App\Listeners;


use App\Events\SendOrderPush;
use App\Services\OrderPushNotificationBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderPushNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    public function handle(SendOrderPush $event)
    {
        try{
            $orderPushNotificationBuilderService = new OrderPushNotificationBuilder($event->info['order_id'], $event->info['status']);
            $orderPushNotificationBuilderService->send();
        } catch(\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
