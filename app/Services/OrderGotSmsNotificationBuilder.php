<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Models\FrontendOrder;
use App\Models\NotificationAlert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderGotSmsNotificationBuilder
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
                $superAdmins    = $this->getUsersByRole(Role::SUPER_ADMIN)->whereNotNull('phone')->get();
                $branchManagers = $this->getUsersByRole(Role::BRANCH_MANAGER)->where(['branch_id' => $this->order->branch_id])->whereNotNull('phone')->get();
                $waiters        = $this->getUsersByRole(Role::WAITER)->where(['branch_id' => $this->order->branch_id])->whereNotNull('phone')->get();

                $smsArrays = [];
                foreach ([$superAdmins, $branchManagers, $waiters] as $userGroup) {
                    if (!blank($userGroup)) {
                        foreach ($userGroup as $user) {
                            $smsArrays[$user->phone] = [
                                'code'  => $user->country_code,
                                'phone' => $user->phone,
                            ];
                        }
                    }
                }
                $smsArrays = array_values($smsArrays);

                if (count($smsArrays) > 0) {
                    $notificationAlert = NotificationAlert::where(['language' => 'admin_and_branch_manager_new_order_message'])->first();
                    if ($notificationAlert && $notificationAlert->sms == SwitchBox::ON) {
                        $message = 'Order ID : '.$this->order->order_serial_no . ' '.$notificationAlert->sms_message;
                        foreach ($smsArrays as $smsArray) {
                            $this->sms($smsArray['code'], $smsArray['phone'], $message);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('OrderGotSmsNotificationBuilder Exception: ' . $e->getMessage());
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

    private function sms($code, $phone, $message): void
    {
        try {
            $smsManagerService = new SmsManagerService();
            $smsService        = new SmsService();
            if ($smsService->gateway() && $smsManagerService->gateway($smsService->gateway())->status()) {
                $smsManagerService->gateway($smsService->gateway())->send($code, $phone, $message);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
