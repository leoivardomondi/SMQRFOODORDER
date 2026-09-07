<?php

namespace App\Services;

use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Http\Requests\MailRequest;
use App\Http\Requests\MailTestRequest;
use App\Mail\OrderGotMail;
use App\Mail\OrderMail;
use App\Mail\SmtpTestMail;
use App\Models\Branch;
use App\Models\FrontendOrder;
use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Smartisan\Settings\Facades\Settings;

class MailService
{
    public $envService;

    public function __construct(EnvEditor $envEditor)
    {
        $this->envService = $envEditor;
    }

    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return Settings::group('mail')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(MailRequest $request)
    {
        try {
            Settings::group('mail')->set($request->validated());
            $this->envService->addData([
                'MAIL_MAILER'       => 'smtp',
                'MAIL_HOST'         => $request->mail_host,
                'MAIL_PORT'         => $request->mail_port,
                'MAIL_USERNAME'     => $request->mail_username,
                'MAIL_PASSWORD'     => $request->mail_password,
                'MAIL_ENCRYPTION'   => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_email,
                'MAIL_FROM_NAME'    => $request->mail_from_name
            ]);
            Artisan::call('optimize:clear');
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * Fetch sample data (branches with branch managers, recent orders, sample types) for mail testing
     */
    public function sampleData(): array
    {
        try {
            $branches = Branch::select('id', 'name', 'email', 'phone')
                ->get()
                ->map(function ($branch) {
                    $branchManagers = User::role(Role::BRANCH_MANAGER)
                        ->where('branch_id', $branch->id)
                        ->whereNotNull('email')
                        ->get(['id', 'name', 'email', 'phone']);

                    return [
                        'id'              => $branch->id,
                        'name'            => $branch->name,
                        'email'           => $branch->email,
                        'branch_managers' => $branchManagers,
                    ];
                });

            $recentOrders = FrontendOrder::with(['branch', 'user'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id'              => $order->id,
                        'order_serial_no' => $order->order_serial_no,
                        'customer_name'   => $order->user?->name ?? 'Guest',
                        'customer_email'  => $order->user?->email,
                        'total_formatted' => 'KSh ' . number_format((float) $order->total, 2),
                        'branch_name'     => $order->branch?->name ?? 'Main Branch',
                        'branch_id'       => $order->branch_id,
                        'order_datetime'  => $order->order_datetime,
                    ];
                });

            $sampleTypes = [
                [
                    'id'          => 'branch_manager_order',
                    'name'        => 'Branch Manager: Customer Order Details Alert',
                    'description' => 'Rich notification sent to Branch Manager with order items, pricing, customer details, and action link.',
                    'badge'       => 'Order Details',
                ],
                [
                    'id'          => 'customer_order_confirmation',
                    'name'        => 'Customer: Order Confirmation',
                    'description' => 'Customer-facing notification confirming receipt of order.',
                    'badge'       => 'Customer Confirmation',
                ],
                [
                    'id'          => 'customer_order_out_for_delivery',
                    'name'        => 'Customer: Out for Delivery',
                    'description' => 'Customer notification that their order is on the way.',
                    'badge'       => 'Status Alert',
                ],
                [
                    'id'          => 'smtp_connection_test',
                    'name'        => 'Basic SMTP Connection Test',
                    'description' => 'Direct verification email with SMTP server diagnostic details.',
                    'badge'       => 'Diagnostics',
                ],
            ];

            return [
                'branches'      => $branches,
                'recent_orders' => $recentOrders,
                'sample_types'  => $sampleTypes,
            ];
        } catch (Exception $e) {
            Log::error('MailService sampleData Exception: ' . $e->getMessage());
            return [
                'branches'      => [],
                'recent_orders' => [],
                'sample_types'  => [],
            ];
        }
    }

    /**
     * Send test email with selected sample type and credentials
     * @throws Exception
     */
    public function testMail(MailTestRequest $request): array
    {
        $email      = $request->email;
        $sampleType = $request->sample_type;
        $orderId    = $request->order_id;
        $branchId   = $request->branch_id;

        $savedSettings = Settings::group('mail')->all();

        $host       = $request->mail_host ?: ($savedSettings['mail_host'] ?? config('mail.mailers.smtp.host'));
        $port       = $request->mail_port ?: ($savedSettings['mail_port'] ?? config('mail.mailers.smtp.port'));
        $username   = $request->mail_username ?: ($savedSettings['mail_username'] ?? config('mail.mailers.smtp.username'));
        $password   = $request->filled('mail_password') ? $request->mail_password : ($savedSettings['mail_password'] ?? config('mail.mailers.smtp.password'));
        $encryption = $request->mail_encryption ?: ($savedSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'));
        $fromEmail  = $request->mail_from_email ?: ($savedSettings['mail_from_email'] ?? config('mail.from.address'));
        $fromName   = $request->mail_from_name ?: ($savedSettings['mail_from_name'] ?? config('mail.from.name'));

        if (empty($host) || empty($port)) {
            throw new Exception('SMTP Host and Port are required to run an email test.', 422);
        }

        config([
            'mail.default'                 => 'smtp',
            'mail.mailers.smtp.transport'  => 'smtp',
            'mail.mailers.smtp.host'       => $host,
            'mail.mailers.smtp.port'       => (int) $port,
            'mail.mailers.smtp.encryption' => ($encryption === 'none' || empty($encryption)) ? null : $encryption,
            'mail.mailers.smtp.username'   => $username,
            'mail.mailers.smtp.password'   => $password,
            'mail.from.address'            => $fromEmail,
            'mail.from.name'               => $fromName,
        ]);

        Mail::purge('smtp');

        try {
            if ($sampleType === 'branch_manager_order') {
                $order = null;
                if ($orderId) {
                    $order = FrontendOrder::with(['orderItems.orderItem', 'user', 'address', 'branch', 'transaction'])->find($orderId);
                }
                if (!$order && $branchId) {
                    $order = FrontendOrder::with(['orderItems.orderItem', 'user', 'address', 'branch', 'transaction'])
                        ->where('branch_id', $branchId)
                        ->latest()
                        ->first();
                }
                if (!$order) {
                    $order = FrontendOrder::with(['orderItems.orderItem', 'user', 'address', 'branch', 'transaction'])
                        ->latest()
                        ->first();
                }
                if (!$order) {
                    $order = $this->createMockOrder($branchId, $email);
                }

                $branchName = $order->branch?->name ?? 'the branch';
                $alertMessage = "This is a test notification verifying that customer order details are delivered to branch management ({$branchName}).";
                Mail::to($email)->send(new OrderGotMail($order, $alertMessage));

            } elseif ($sampleType === 'customer_order_confirmation') {
                $order = $orderId ? FrontendOrder::find($orderId) : FrontendOrder::latest()->first();
                $orderSerial = $order ? $order->order_serial_no : 'SAMPLE-1001';
                $customerName = $order?->user?->name ?? 'Valued Customer';
                $message = 'Your order has been placed and confirmed successfully.';
                Mail::to($email)->send(new OrderMail($customerName, $orderSerial, $message));

            } elseif ($sampleType === 'customer_order_out_for_delivery') {
                $order = $orderId ? FrontendOrder::find($orderId) : FrontendOrder::latest()->first();
                $orderSerial = $order ? $order->order_serial_no : 'SAMPLE-1001';
                $customerName = $order?->user?->name ?? 'Valued Customer';
                $message = 'Your order is out for delivery with our rider.';
                Mail::to($email)->send(new OrderMail($customerName, $orderSerial, $message));

            } else {
                // smtp_connection_test
                $configDetails = [
                    'host'       => $host,
                    'port'       => $port,
                    'encryption' => $encryption,
                    'from_email' => $fromEmail,
                    'from_name'  => $fromName,
                ];
                Mail::to($email)->send(new SmtpTestMail($configDetails, $email));
            }

            return [
                'status'  => true,
                'message' => "Test email successfully sent to {$email} via {$host}:{$port} ({$encryption})!",
            ];
        } catch (Exception $e) {
            Log::error('MailService testMail Exception: ' . $e->getMessage());
            throw new Exception($e->getMessage(), 422);
        }
    }

    private function createMockOrder($branchId = null, $email = 'manager@bwibo.com')
    {
        $branch = $branchId ? Branch::find($branchId) : Branch::first();

        $order = new FrontendOrder();
        $order->id = 99999;
        $order->order_serial_no = 'SAMPLE-' . rand(100000, 999999);
        $order->payment_status = PaymentStatus::PAID;
        $order->order_type = OrderType::DELIVERY;
        $order->payment_method = 2;
        $order->total = 1450.00;
        $order->order_datetime = now()->format('d M Y, h:i A');
        $order->created_at = now();

        $mockUser = (object)[
            'name'  => 'John Doe (Sample Customer)',
            'phone' => '+254 712 345 678',
            'email' => $email,
        ];
        $order->setRelation('user', $mockUser);

        $mockBranch = $branch ?? (object)['name' => 'Bwibo Restaurant (Main Branch)'];
        $order->setRelation('branch', $mockBranch);

        $mockAddress = (object)[
            'label'     => 'Office',
            'address'   => 'Lavington, James Gichuru Road',
            'apartment' => 'Building 3, 2nd Floor',
            'latitude'  => '-1.286389',
            'longitude' => '36.817223',
        ];
        $order->setRelation('address', $mockAddress);

        $mockItems = collect([
            (object)[
                'quantity'    => 2,
                'total_price' => 1100.00,
                'instruction' => 'Medium rare, extra spicy salsa',
                'orderItem'   => (object)['name' => 'Signature Beef Burger Platter'],
            ],
            (object)[
                'quantity'    => 1,
                'total_price' => 350.00,
                'instruction' => 'Chilled with ice',
                'orderItem'   => (object)['name' => 'Fresh Passion Juice (500ml)'],
            ],
        ]);
        $order->setRelation('orderItems', $mockItems);

        $mockTransaction = (object)[
            'payment_method' => 'M-Pesa',
            'provider_name'  => 'Daraja STK',
            'transaction_no' => 'QGH7' . rand(10000, 99999) . 'TY',
            'created_at'     => now(),
        ];
        $order->setRelation('transaction', $mockTransaction);

        return $order;
    }
}
