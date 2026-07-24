<?php

namespace Tests\Unit;

use Illuminate\Contracts\Queue\ShouldQueue;
use Tests\TestCase;

class QueueAndGatewayConfigurationTest extends TestCase
{
    public function test_all_application_notification_listeners_are_queued(): void
    {
        foreach (glob(app_path('Listeners/*.php')) as $file) {
            $class = 'App\\Listeners\\'.pathinfo($file, PATHINFO_FILENAME);
            $this->assertTrue(is_subclass_of($class, ShouldQueue::class), $class.' must be queued.');
        }
    }

    public function test_only_verified_payment_callbacks_are_enabled(): void
    {
        $this->assertSame(['credit', 'mollie', 'paystack', 'razorpay', 'stripe'], config('payments.verified_gateways'));
    }
}
