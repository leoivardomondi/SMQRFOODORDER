<?php
namespace App\Policies;
use App\Models\FrontendOrder;
use App\Models\User;
class FrontendOrderPolicy
{
    public function view(User $user, FrontendOrder $order): bool { return $order->user_id === $user->id; }
    public function update(User $user, FrontendOrder $order): bool { return $this->view($user, $order); }
}
