<?php

namespace App\Listeners;


use App\Events\SendOrderDeliveryBoyPush;
use App\Services\OrderDeliveryBoyPushNotificationBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderDeliveryBoyPushNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    public function handle(SendOrderDeliveryBoyPush $event)
    {
        try {
            $orderDeliveryBoyPushNotificationBuilderService = new OrderDeliveryBoyPushNotificationBuilder(
                $event->info['order_id'], $event->info['status']
            );
            $orderDeliveryBoyPushNotificationBuilderService->send();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
