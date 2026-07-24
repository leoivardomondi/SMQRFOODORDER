<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\FrontendOrder;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use App\Policies\AddressPolicy;
use App\Policies\FrontendOrderPolicy;
use App\Policies\MessagePolicy;
use App\Policies\OrderPolicy;
use Tests\TestCase;

class OwnershipPolicyTest extends TestCase
{
    public function test_customer_cannot_access_another_customers_records(): void
    {
        $user = new User(['name' => 'Owner']);
        $user->id = 10;

        $address = new Address(['user_id' => 11]);
        $message = new Message(['user_id' => 11]);
        $order = new FrontendOrder(['user_id' => 11]);

        $this->assertFalse((new AddressPolicy)->view($user, $address));
        $this->assertFalse((new MessagePolicy)->view($user, $message));
        $this->assertFalse((new FrontendOrderPolicy)->view($user, $order));
    }

    public function test_owner_and_assigned_driver_are_authorized(): void
    {
        $user = new User(['name' => 'Owner']);
        $user->id = 10;

        $address = new Address(['user_id' => 10]);
        $order = new FrontendOrder(['user_id' => 10]);
        $delivery = new Order;
        $delivery->delivery_boy_id = 10;

        $this->assertTrue((new AddressPolicy)->update($user, $address));
        $this->assertTrue((new FrontendOrderPolicy)->update($user, $order));
        $this->assertTrue((new OrderPolicy)->deliver($user, $delivery));
    }
}
