<?php

namespace App\Http\PaymentGateways\Gateways;

use App\Enums\Activity;
use App\Models\Currency;
use App\Models\GatewayOption;
use App\Models\PaymentGateway;
use App\Services\PaymentAbstract;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Smartisan\Settings\Facades\Settings;

class Pesapal extends PaymentAbstract
{
    protected string $baseUrl;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);

        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'pesapal'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
        }

        $mode = (int) ($this->paymentGatewayOption['pesapal_mode'] ?? 5);
        // Mode 5 = Sandbox, Mode 10 = Live
        $this->baseUrl = ($mode === 10)
            ? 'https://pay.pesapal.com/v3'
            : 'https://cyb3r.pesapal.com/pesapalv3';
    }

    public function status(): bool
    {
        return PaymentGateway::where(['slug' => 'pesapal', 'status' => Activity::ENABLE])->exists();
    }

    /**
     * Get OAuth token from Pesapal v3 API
     *
     * @throws Exception
     */
    protected function getAccessToken(): string
    {
        $consumerKey    = trim($this->paymentGatewayOption['pesapal_consumer_key'] ?? '');
        $consumerSecret = trim($this->paymentGatewayOption['pesapal_consumer_secret'] ?? '');

        if (blank($consumerKey) || blank($consumerSecret)) {
            throw new Exception('Pesapal Consumer Key or Secret is missing in payment gateway settings.');
        }

        $response = Http::acceptJson()
            ->post($this->baseUrl . '/api/Auth/RequestToken', [
                'consumer_key'    => $consumerKey,
                'consumer_secret' => $consumerSecret,
            ]);

        $data = $response->json();

        if ($response->successful() && !empty($data['token'])) {
            return $data['token'];
        }

        $errorMsg = $data['error']['message'] ?? $data['message'] ?? 'Failed to authenticate with Pesapal API v3.';
        throw new Exception($errorMsg);
    }

    /**
     * Get or dynamically register Pesapal IPN ID
     *
     * @throws Exception
     */
    protected function getOrRegisterIpnId(string $token): string
    {
        $ipnId = trim($this->paymentGatewayOption['pesapal_ipn_id'] ?? '');

        if (!blank($ipnId)) {
            return $ipnId;
        }

        // Register IPN URL dynamically if not configured
        $ipnUrl   = route('payment.ipn', ['paymentGateway' => 'pesapal']);
        $response = Http::withToken($token)
            ->acceptJson()
            ->post($this->baseUrl . '/api/URLSetup/RegisterIPN', [
                'url'                   => $ipnUrl,
                'ipn_notification_type' => 'GET',
            ]);

        $data = $response->json();

        if ($response->successful() && !empty($data['ipn_id'])) {
            $newIpnId = $data['ipn_id'];

            // Persist the generated IPN ID into GatewayOption
            $option = GatewayOption::where('option', 'pesapal_ipn_id')->first();
            if ($option) {
                $option->value = $newIpnId;
                $option->save();
            }

            return $newIpnId;
        }

        throw new Exception($data['error']['message'] ?? 'Failed to register Pesapal IPN notification URL.');
    }

    public function payment($order, $request): RedirectResponse
    {
        try {
            $token = $this->getAccessToken();
            $ipnId = $this->getOrRegisterIpnId($token);

            $currencyCode = 'KES';
            $currencyId   = Settings::group('site')->get('site_default_currency');
            if (!blank($currencyId)) {
                $currency = Currency::find($currencyId);
                if ($currency && !blank($currency->code)) {
                    $currencyCode = $currency->code;
                }
            }

            $reference = 'order-' . $order->id . '-' . Str::upper(Str::random(6));

            $userName  = trim($order?->user?->name ?? $order->name ?? 'Customer');
            $nameParts = explode(' ', $userName, 2);
            $firstName = $nameParts[0] ?? 'Customer';
            $lastName  = $nameParts[1] ?? 'User';

            $userEmail = $order?->user?->email ?? $order->email ?? '';
            if (blank($userEmail)) {
                $userEmail = 'customer' . $order->id . '@bwibo.restaurant';
            }

            $userPhone = $order?->user?->phone ?? $order->phone ?? '';

            $payload = [
                'id'              => $reference,
                'currency'        => $currencyCode,
                'amount'          => (float) number_format((float) $order->total, 2, '.', ''),
                'description'     => 'Payment for Order #' . ($order->order_serial_no ?? $order->id),
                'callback_url'    => route('payment.success', ['paymentGateway' => 'pesapal', 'order' => $order]),
                'notification_id' => $ipnId,
                'billing_address' => [
                    'email_address' => $userEmail,
                    'phone_number'  => $userPhone,
                    'first_name'    => $firstName,
                    'last_name'     => $lastName,
                ],
            ];

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->baseUrl . '/api/Transactions/SubmitOrderRequest', $payload);

            $data = $response->json();

            if ($response->successful() && !empty($data['redirect_url'])) {
                session()->put('pesapal_reference_' . $order->id, $reference);
                if (!empty($data['order_tracking_id'])) {
                    session()->put('pesapal_tracking_id_' . $order->id, $data['order_tracking_id']);
                }

                return redirect()->away($data['redirect_url']);
            }

            $errorMessage = $data['error']['message'] ?? $data['message'] ?? trans('all.message.something_wrong');

            return redirect()->route('payment.index', ['order' => $order])->with('error', $errorMessage);

        } catch (Exception $e) {
            Log::error('Pesapal Payment Error: ' . $e->getMessage());

            return redirect()->route('payment.index', ['order' => $order])->with('error', $e->getMessage());
        }
    }

    public function success($order, $request): RedirectResponse
    {
        try {
            $orderTrackingId = $request->get('OrderTrackingId')
                ?? $request->get('orderTrackingId')
                ?? session('pesapal_tracking_id_' . $order->id);

            if (blank($orderTrackingId)) {
                return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'pesapal'])->with(
                    'error',
                    'Pesapal transaction tracking ID missing from callback.'
                );
            }

            $token    = $this->getAccessToken();
            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->baseUrl . '/api/Transactions/GetTransactionStatus?orderTrackingId=' . urlencode($orderTrackingId));

            $data = $response->json();

            $statusDescription = strtolower($data['payment_status_description'] ?? '');
            $statusCode        = (int) ($data['status_code'] ?? 0);

            // Pesapal v3 status_code 1 = Completed
            $isPaid = $response->successful() && ($statusCode === 1 || $statusDescription === 'completed');

            if ($isPaid) {
                $confirmationCode = $data['confirmation_code'] ?? null;
                $paymentAccount   = $data['payment_account'] ?? $order?->user?->phone ?? $order->phone ?? null;
                $providerName     = $data['payment_method'] ?? 'Pesapal';
                $transactionNo   = !blank($confirmationCode) ? $confirmationCode : $orderTrackingId;
                $last4           = (!blank($paymentAccount) && strlen($paymentAccount) >= 4) ? substr($paymentAccount, -4) : null;

                $this->paymentService->payment($order, 'pesapal', $transactionNo, [
                    'receipt_number'    => $confirmationCode ?? $transactionNo,
                    'provider_name'     => $providerName,
                    'payer_phone'       => $paymentAccount,
                    'payer_phone_last4' => $last4,
                    'channel'           => $data['payment_choice'] ?? $providerName,
                    'amount'            => $data['amount'] ?? $order->total,
                    'created_date'      => $data['created_date'] ?? null,
                ]);

                return redirect()->route('payment.successful', ['order' => $order])->with(
                    'success',
                    trans('all.message.payment_successful')
                );
            }

            return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'pesapal'])->with(
                'error',
                $data['payment_status_description'] ?? trans('all.message.something_wrong')
            );
        } catch (Exception $e) {
            Log::error('Pesapal Verification Error: ' . $e->getMessage());

            return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'pesapal'])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function verifyAndCompletePayment($order, string $orderTrackingId): bool
    {
        try {
            $token    = $this->getAccessToken();
            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->baseUrl . '/api/Transactions/GetTransactionStatus?orderTrackingId=' . urlencode($orderTrackingId));

            $data              = $response->json();
            $statusDescription = strtolower($data['payment_status_description'] ?? '');
            $statusCode        = (int) ($data['status_code'] ?? 0);

            $isPaid = $response->successful() && ($statusCode === 1 || $statusDescription === 'completed');

            if ($isPaid) {
                $confirmationCode = $data['confirmation_code'] ?? null;
                $paymentAccount   = $data['payment_account'] ?? $order?->user?->phone ?? $order->phone ?? null;
                $providerName     = $data['payment_method'] ?? 'Pesapal';
                $transactionNo   = !blank($confirmationCode) ? $confirmationCode : $orderTrackingId;
                $last4           = (!blank($paymentAccount) && strlen($paymentAccount) >= 4) ? substr($paymentAccount, -4) : null;

                $this->paymentService->payment($order, 'pesapal', $transactionNo, [
                    'receipt_number'    => $confirmationCode ?? $transactionNo,
                    'provider_name'     => $providerName,
                    'payer_phone'       => $paymentAccount,
                    'payer_phone_last4' => $last4,
                    'channel'           => $data['payment_choice'] ?? $providerName,
                    'amount'            => $data['amount'] ?? $order->total,
                    'created_date'      => $data['created_date'] ?? null,
                ]);

                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error('Pesapal IPN Error: ' . $e->getMessage());
            return false;
        }
    }

    public function fail($order, $request): RedirectResponse
    {
        $error = session('error') ?? $request->get('error') ?? trans('all.message.something_wrong');

        return redirect()->route('payment.index', ['order' => $order])->with('error', $error);
    }

    public function cancel($order, $request): RedirectResponse
    {
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }
}
