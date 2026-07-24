<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PaymentUrlTest extends TestCase
{
    public function test_payment_entry_url_is_temporary_and_signed(): void
    {
        $order = new Order;
        $order->id = 42;
        $url = URL::temporarySignedRoute('payment.index', now()->addMinutes(30), ['order' => $order]);

        $this->assertStringContainsString('/payment/42/pay', $url);
        $this->assertStringContainsString('expires=', $url);
        $this->assertStringContainsString('signature=', $url);
    }
}
