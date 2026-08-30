<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Models\FrontendOrder;
use App\Models\NotificationAlert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderGotPushNotificationBuilder
{
    public int $orderId;
    public object $order;

    public function __construct($orderId,)
    {
        $this->orderId = $orderId;
        $this->order   = FrontendOrder::find($orderId);
    }

    public function send(): void
    {
        try {
            if (!blank($this->order)) {
                $superAdmins = $this->getUsersByRole(Role::SUPER_ADMIN)
                    ->where(function ($q) {
                        $q->whereNotNull('web_token')->orWhereNotNull('device_token');
                    })
                    ->get();

                $branchManagers = $this->getUsersByRole(Role::BRANCH_MANAGER)
                    ->where(['branch_id' => $this->order->branch_id])
                    ->where(function ($q) {
                        $q->whereNotNull('web_token')->orWhereNotNull('device_token');
                    })
                    ->get();

                $waiters = $this->getUsersByRole(Role::WAITER)
                    ->where(['branch_id' => $this->order->branch_id])
                    ->where(function ($q) {
                        $q->whereNotNull('web_token')->orWhereNotNull('device_token');
                    })
                    ->get();

                $fcmTokenArray = [];
                foreach ([$superAdmins, $branchManagers, $waiters] as $userGroup) {
                    if (!blank($userGroup)) {
                        foreach ($userGroup as $user) {
                            if (!blank($user->web_token)) {
                                $fcmTokenArray[] = $user->web_token;
                            }
                            if (!blank($user->device_token)) {
                                $fcmTokenArray[] = $user->device_token;
                            }
                        }
                    }
                }

                $fcmTokenArray = array_values(array_unique(array_filter($fcmTokenArray)));

                if (count($fcmTokenArray) > 0) {
                    $notificationAlert = NotificationAlert::where(['language' => 'admin_and_branch_manager_new_order_message'])->first();
                    if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
                        $pushNotification = (object)[
                            'title'       => 'New Order Notification',
                            'description' => $notificationAlert->push_notification_message,
                            'order_id'    => $this->orderId
                        ];
                        $firebase         = new FirebaseService();
                        $firebase->sendNotification($pushNotification, $fcmTokenArray,  "new-order-found");
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('OrderGotPushNotificationBuilder Exception: ' . $e->getMessage());
        }
    }

    private function getUsersByRole($role)
    {
        try {
            return User::role($role);
        } catch (Exception $e) {
            return User::whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role)->orWhere('id', $role);
            });
        }
    }
}
