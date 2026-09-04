<?php

namespace App\Console\Commands;

use App\Enums\Ask;
use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Models\NotificationAlert;
use App\Models\Order;
use App\Models\User;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendFutureOrderReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-future-order-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notification and email reminders to admins and branch managers for upcoming future/advance orders needing status updates.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $today = Carbon::today();
            $now   = Carbon::now();

            // Find future / advance orders that are scheduled for today or approaching within 2 hours,
            // and are still in a non-final status (PENDING, ACCEPT, PREPARING)
            $futureOrders = Order::withoutGlobalScopes()
                ->whereIn('status', [OrderStatus::PENDING, OrderStatus::ACCEPT, OrderStatus::PREPARING])
                ->where(function ($query) use ($today, $now) {
                    $query->where('is_advance_order', Ask::YES)
                        ->orWhereDate('order_datetime', $today)
                        ->orWhere('order_datetime', '>=', $now);
                })
                ->get();

            if ($futureOrders->isEmpty()) {
                $this->info('No pending future orders found.');
                return Command::SUCCESS;
            }

            $count = $futureOrders->count();
            $this->info("Found {$count} future order(s) needing attention.");

            foreach ($futureOrders as $order) {
                $branchId = $order->branch_id;

                // Find Super Admins & Branch Managers for this branch
                $superAdmins = User::role(Role::SUPER_ADMIN)
                    ->where(function ($q) {
                        $q->whereNotNull('web_token')->orWhereNotNull('device_token');
                    })->get();

                $branchManagers = User::role(Role::BRANCH_MANAGER)
                    ->where('branch_id', $branchId)
                    ->where(function ($q) {
                        $q->whereNotNull('web_token')->orWhereNotNull('device_token');
                    })->get();

                $fcmTokens = [];
                foreach ([$superAdmins, $branchManagers] as $group) {
                    foreach ($group as $user) {
                        if ($user->web_token) {
                            $fcmTokens[] = $user->web_token;
                        }
                        if ($user->device_token) {
                            $fcmTokens[] = $user->device_token;
                        }
                    }
                }
                $fcmTokens = array_values(array_unique(array_filter($fcmTokens)));

                if (!empty($fcmTokens)) {
                    $pushNotification = (object)[
                        'title'       => "⏰ Future Order Reminder: #{$order->order_serial_no}",
                        'description' => "Order #{$order->order_serial_no} is scheduled for {$order->delivery_time}. Please log in and update its status!",
                        'order_id'    => $order->id
                    ];

                    $firebase = new FirebaseService();
                    $firebase->sendNotification($pushNotification, $fcmTokens, "future-order-reminder");
                }
            }

            Log::info("SendFutureOrderReminders: Reminders dispatched for {$count} order(s).");
            return Command::SUCCESS;
        } catch (Exception $e) {
            Log::error('SendFutureOrderReminders Exception: ' . $e->getMessage());
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
