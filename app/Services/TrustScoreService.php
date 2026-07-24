<?php

namespace App\Services;

use App\Enums\Activity;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Smartisan\Settings\Facades\Settings;

class TrustScoreService
{
    /**
     * Get trust score statistics for a user.
     *
     * @param User|null $user
     * @return array
     */
    public function getUserMetrics(?User $user): array
    {
        $minOrdersRequired = (int) Settings::group('order_setup')->get('order_setup_trust_score_min_orders', 1);
        $isTrustScoreEnabled = (int) Settings::group('order_setup')->get('order_setup_trust_score_enable', Activity::ENABLE) === Activity::ENABLE;

        if (!$user) {
            return [
                'successful_orders'   => 0,
                'canceled_orders'     => 0,
                'trust_score'         => 0,
                'min_required_orders' => $minOrdersRequired,
                'trust_score_enabled' => $isTrustScoreEnabled,
                'can_pay_on_delivery' => !$isTrustScoreEnabled,
            ];
        }

        $successfulOrders = Order::where('user_id', $user->id)
            ->where('status', OrderStatus::DELIVERED)
            ->count();

        $canceledOrders = Order::where('user_id', $user->id)
            ->whereIn('status', [OrderStatus::CANCELED, OrderStatus::RETURNED])
            ->count();

        $canPayOnDelivery = !$isTrustScoreEnabled || ($successfulOrders >= $minOrdersRequired);

        return [
            'successful_orders'   => $successfulOrders,
            'canceled_orders'     => $canceledOrders,
            'trust_score'         => $successfulOrders,
            'min_required_orders' => $minOrdersRequired,
            'trust_score_enabled' => $isTrustScoreEnabled,
            'can_pay_on_delivery' => $canPayOnDelivery,
        ];
    }

    /**
     * Determine if the user can pay on delivery.
     *
     * @param User|null $user
     * @return bool
     */
    public function canPayOnDelivery(?User $user): bool
    {
        return $this->getUserMetrics($user)['can_pay_on_delivery'];
    }
}
