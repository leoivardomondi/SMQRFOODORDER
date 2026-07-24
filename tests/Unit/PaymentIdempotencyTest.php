<?php

namespace Tests\Unit;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PaymentIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_and_cashback_are_idempotent(): void
    {
        Event::fake();

        $branch = \App\Models\Branch::create([
            'name' => 'Main Branch',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'address' => '123 Main St',
        ]);
        $user = User::withoutGlobalScopes()->create([
            'name' => 'Customer',
            'username' => 'customer',
            'password' => 'password',
            'balance' => 0,
        ]);
        $order = Order::withoutGlobalScopes()->create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'subtotal' => 50,
            'total' => 50,
            'status' => 1,
            'payment_status' => PaymentStatus::UNPAID,
        ]);
        $service = new PaymentService;

        $service->payment($order, 'stripe', 'pay-1');
        $service->payment($order->fresh(), 'stripe', 'pay-1');
        $service->cashBack($order, 'credit', 'refund-1');
        $service->cashBack($order, 'credit', 'refund-1');

        $this->assertSame(2, Transaction::count());
        $this->assertEquals(50, $user->fresh()->balance);
    }
}
