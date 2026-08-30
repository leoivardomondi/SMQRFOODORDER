<?php

namespace App\Services;


use App\Enums\Role;
use App\Mail\OrderGotMail;
use App\Models\FrontendOrder;
use App\Models\NotificationAlert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderGotMailNotificationBuilder
{
    public int $orderId;
    public object $order;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->order = FrontendOrder::find($orderId);
    }

    public function send()
    {
        try {
            if (!blank($this->order)) {
                $superAdminEmails     = $this->getUsersByRole(Role::SUPER_ADMIN)->whereNotNull('email')->pluck('email')->toArray();
                $branchManagerEmails  = $this->getUsersByRole(Role::BRANCH_MANAGER)->where(['branch_id' => $this->order->branch_id])->whereNotNull('email')->pluck('email')->toArray();
                $waiterEmails         = $this->getUsersByRole(Role::WAITER)->where(['branch_id' => $this->order->branch_id])->whereNotNull('email')->pluck('email')->toArray();

                $emailArray = array_values(array_unique(array_filter(array_merge(
                    $superAdminEmails,
                    $branchManagerEmails,
                    $waiterEmails
                ))));

                if (count($emailArray) > 0) {
                    $notificationAlert = NotificationAlert::where(['language' => 'admin_and_branch_manager_new_order_message'])->first();
                    $message = $notificationAlert?->mail_message ?? 'You have received a new order.';

                    // New-order emails to branch recipients are mandatory and must
                    // not depend on the optional notification toggle.
                    $emailArray = array_values(array_unique($emailArray));
                    $this->order->loadMissing([
                        'orderItems.orderItem',
                        'user',
                        'address',
                        'branch',
                        'transaction',
                    ]);

                    Mail::to($emailArray[0])
                        ->cc(array_slice($emailArray, 1))
                        ->send(new OrderGotMail($this->order, $message));
                }
            }
        } catch (Exception $e) {
            Log::error('OrderGotMailNotificationBuilder Exception: ' . $e->getMessage());
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
