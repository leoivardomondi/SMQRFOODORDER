<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
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
        if (!blank($this->order)) {
            $emailSuperAdmins = User::role(Role::SUPER_ADMIN)->whereNotNull('email')->get();
            $emailAllAdmins = User::role(Role::ADMIN)->where(['branch_id' => 0])->whereNotNull('email')->get();
            $emailBranchAdmins = User::role(Role::ADMIN)->where(['branch_id' => $this->order->branch_id])->whereNotNull('email')->get();
            $emailBranchManagers = User::role(Role::BRANCH_MANAGER)->where(['branch_id' => $this->order->branch_id])->whereNotNull('email')->get();

            $i = 0;
            $emailArray = [];
            if (!blank($emailSuperAdmins)) {
                foreach ($emailSuperAdmins as $emailSuperAdmin) {
                    $emailArray[$i] = $emailSuperAdmin->email;
                    $i++;
                }
            }

            if (!blank($emailAllAdmins)) {
                foreach ($emailAllAdmins as $emailAllAdmin) {
                    $emailArray[$i] = $emailAllAdmin->email;
                    $i++;
                }
            }

            if (!blank($emailBranchAdmins)) {
                foreach ($emailBranchAdmins as $emailBranchAdmin) {
                    $emailArray[$i] = $emailBranchAdmin->email;
                    $i++;
                }
            }

            if (!blank($emailBranchManagers)) {
                foreach ($emailBranchManagers as $emailBranchManager) {
                    $emailArray[$i] = $emailBranchManager->email;
                    $i++;
                }
            }

            if (count($emailArray) > 0) {
                try {
                    $notificationAlert = NotificationAlert::where(['language' => 'admin_and_branch_manager_new_order_message'])->first();
                    if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
                        try {
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
                                ->send(new OrderGotMail($this->order, $notificationAlert->mail_message));
                        } catch (Exception $e) {
                            Log::info($e->getMessage());
                        }
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }

        }
    }
}
