<?php

namespace App\Listeners;


use App\Events\SendOrderGotPush;
use App\Services\OrderGotPushNotificationBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderGotPushNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    public function handle(SendOrderGotPush $event)
    {
        try{
            $orderPushNotificationBuilderService = new OrderGotPushNotificationBuilder($event->info['order_id']);
            $orderPushNotificationBuilderService->send();
        } catch(\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
