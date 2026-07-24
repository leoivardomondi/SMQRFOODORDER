<?php

namespace App\Listeners;


use App\Events\SendOrderGotSms;
use App\Services\OrderGotSmsNotificationBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderGotSmsNotification implements ShouldQueue
{
    use InteractsWithQueue;
    public int $tries = 3;
    public function handle(SendOrderGotSms $event): void
    {
        try {
            $orderGotSmsNotificationBuilderService = new OrderGotSmsNotificationBuilder($event->info['order_id']);
            $orderGotSmsNotificationBuilderService->send();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            throw $e;
        }
    }
}
