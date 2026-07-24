<?php

namespace App\Listeners;

use App\Events\SendOrderDeliveryBoySms;
use App\Services\OrderDeliveryBoySmsNotificationBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderDeliveryBoySmsNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    public function handle(SendOrderDeliveryBoySms $event)
    {
        try{
            $orderDeliveryBoySmsNotificationBuilderService = new OrderDeliveryBoySmsNotificationBuilder($event->info['order_id'], $event->info['status']);
            $orderDeliveryBoySmsNotificationBuilderService->send();
        } catch(\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
