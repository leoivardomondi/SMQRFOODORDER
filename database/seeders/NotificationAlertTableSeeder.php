<?php

namespace Database\Seeders;

use App\Enums\SwitchBox;
use Illuminate\Database\Seeder;
use App\Models\NotificationAlert;

class NotificationAlertTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public array $notificationAlerts = [
        'name'    => [
            'Order Pending Message',
            'Order Confirmation Message',
            'Order Preparing Message',
            'Order Prepared Message',
            'Order Out For Delivery Message',
            'Order Delivered Message',
            'Order Canceled Message',
            'Order Rejected Message',
            'Order Returned Message',
            'Delivery Boy After Assign Message',
            'Admin And Branch Manager New Order Message',
        ],
        'message' => [
            'Your order has been placed successfully.',
            'Your order has been confirmed.',
            'Your order is being prepared.',
            'Your order has been prepared and is waiting for delivery.',
            'Your order is out for delivery.',
            'Your order has been delivered successfully.',
            'Your order has been canceled.',
            'Your order has been rejected.',
            'Your order has been returned.',
            'You have been assigned an order for delivery.',
            'You have a new order.',
        ]

    ];

    public function run()
    {
        foreach ($this->notificationAlerts['name'] as $key => $notificationAlert) {
            NotificationAlert::create([
                'name'                      => $notificationAlert,
                'language'                  => str_replace(' ', '_', strtolower($notificationAlert)),
                'mail_message'              => $this->notificationAlerts['message'][$key],
                'sms_message'               => $this->notificationAlerts['message'][$key],
                'push_notification_message' => $this->notificationAlerts['message'][$key],
                'mail'                      => SwitchBox::OFF,
                'sms'                       => SwitchBox::OFF,
                'push_notification'         => SwitchBox::OFF,
            ]);
        }
    }
}
