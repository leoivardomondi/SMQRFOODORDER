<?php
namespace App\Policies;
use App\Models\Order;
use App\Models\User;
class OrderPolicy
{
    public function deliver(User $user, Order $order): bool { return $order->delivery_boy_id === $user->id; }
}
