<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $messages = [
            'order_pending_message'             => 'Your order has been placed successfully.',
            'order_confirmation_message'        => 'Your order has been confirmed.',
            'order_preparing_message'           => 'Your order is being prepared.',
            'order_prepared_message'            => 'Your order has been prepared and is waiting for delivery.',
            'order_out_for_delivery_message'    => 'Your order is out for delivery.',
            'order_delivered_message'           => 'Your order has been delivered successfully.',
            'order_canceled_message'            => 'Your order has been canceled.',
            'order_rejected_message'            => 'Your order has been rejected.',
            'order_returned_message'            => 'Your order has been returned.',
            'delivery_boy_after_assign_message' => 'You have been assigned an order for delivery.',
            'admin_and_branch_manager_new_order_message' => 'You have received a new order.',
        ];

        foreach ($messages as $language => $message) {
            DB::table('notification_alerts')
                ->where('language', $language)
                ->update([
                    'mail_message' => $message,
                    'sms_message' => $message,
                    'push_notification_message' => $message,
                ]);
        }
    }

    public function down(): void
    {
        // Grammar corrections are intentionally retained.
    }
};
