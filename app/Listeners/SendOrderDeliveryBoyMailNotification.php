<?php

namespace App\Listeners;

use App\Events\SendOrderDeliveryBoyMail;
use App\Services\OrderDeliveryBoyMailNotificationBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderDeliveryBoyMailNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    public function handle(SendOrderDeliveryBoyMail $event)
    {
        try {
            $orderDeliveryBoyMailNotificationBuilderService = new OrderDeliveryBoyMailNotificationBuilder(
                $event->info['order_id'],
                $event->info['status']
            );
            $orderDeliveryBoyMailNotificationBuilderService->send();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
