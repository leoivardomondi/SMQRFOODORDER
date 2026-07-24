<?php

namespace App\Http\PaymentGateways\Gateways;

use App\Enums\Activity;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Services\PaymentAbstract;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Smartisan\Settings\Facades\Settings;

class Paystack extends PaymentAbstract
{
    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);

        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'paystack'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
        }
    }

    public function status(): bool
    {
        return PaymentGateway::where(['slug' => 'paystack', 'status' => Activity::ENABLE])->exists();
    }

    public function payment($order, $request): RedirectResponse
    {
        try {
            $secretKey = trim($this->paymentGatewayOption['paystack_secret_key'] ?? '');
            $baseUrl = rtrim($this->paymentGatewayOption['paystack_payment_url'] ?? 'https://api.paystack.co', '/');
            $email = $order?->user?->email ?? $order->email ?? 'bwibomarketing@gmail.com';

            if (blank($secretKey)) {
                return redirect()->route('payment.index', ['order' => $order])->with(
                    'error',
                    'Paystack Secret Key is missing or not configured in settings.'
                );
            }

            if (blank($email)) {
                return redirect()->route('payment.index', ['order' => $order])->with(
                    'error',
                    trans('all.message.something_wrong')
                );
            }

            $currencyCode = 'NGN';
            $currencyId = Settings::group('site')->get('site_default_currency');
            if (!blank($currencyId)) {
                $currency = Currency::find($currencyId);
                if ($currency && !blank($currency->code)) {
                    $currencyCode = $currency->code;
                }
            }

            $reference = 'order-' . $order->id . '-' . Str::upper(Str::random(10));
            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->post($baseUrl . '/transaction/initialize', [
                    'email'        => $email,
                    'amount'       => (int) round(((float) $order->total) * 100),
                    'currency'     => $currencyCode,
                    'reference'    => $reference,
                    'callback_url' => route('payment.success', ['paymentGateway' => 'paystack', 'order' => $order]),
                    'metadata'     => [
                        'order_id' => $order->id,
                    ],
                ]);

            $payload = $response->json();

            if ($response->successful() && ($payload['status'] ?? false) && !blank($payload['data']['authorization_url'] ?? null)) {
                return redirect()->away($payload['data']['authorization_url']);
            }

            return redirect()->route('payment.index', ['order' => $order])->with(
                'error',
                $payload['message'] ?? trans('all.message.something_wrong')
            );
        } catch (Exception $e) {
            Log::info($e->getMessage());

            return redirect()->route('payment.index', ['order' => $order])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function success($order, $request): RedirectResponse
    {
        try {
            $reference = $request->get('reference') ?? $request->get('trxref');
            $secretKey = trim($this->paymentGatewayOption['paystack_secret_key'] ?? '');
            $baseUrl = rtrim($this->paymentGatewayOption['paystack_payment_url'] ?? 'https://api.paystack.co', '/');

            if (blank($secretKey)) {
                return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'paystack'])->with(
                    'error',
                    'Paystack Secret Key is missing or not configured in settings.'
                );
            }

            if (blank($reference)) {
                return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'paystack'])->with(
                    'error',
                    'Payment reference missing from callback.'
                );
            }

            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->get($baseUrl . '/transaction/verify/' . urlencode($reference));

            $payload = $response->json();
            $expectedAmount = (int) round(((float) $order->total) * 100);
            $paidAmount = (int) ($payload['data']['amount'] ?? 0);

            $paid = $response->successful()
                && ($payload['status'] ?? false)
                && (($payload['data']['status'] ?? null) === 'success')
                && ($paidAmount >= $expectedAmount);

            if ($paid) {
                $this->paymentService->payment($order, 'paystack', $reference, [
                    'receipt_number' => $payload['data']['receipt_number'] ?? null,
                    'channel'        => $payload['data']['channel'] ?? null,
                    'provider_name'  => $payload['data']['authorization']['bank'] ?? null,
                    'payer_phone'    => $payload['data']['authorization']['mobile_money_number'] ?? null,
                    'payer_phone_last4' => $payload['data']['authorization']['last4'] ?? null,
                ]);

                return redirect()->route('payment.successful', ['order' => $order])->with(
                    'success',
                    trans('all.message.payment_successful')
                );
            }

            return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'paystack'])->with(
                'error',
                $payload['message'] ?? trans('all.message.something_wrong')
            );
        } catch (Exception $e) {
            Log::info($e->getMessage());

            return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'paystack'])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function fail($order, $request): RedirectResponse
    {
        $error = session('error') ?? $request->get('error') ?? trans('all.message.something_wrong');

        return redirect()->route('payment.index', ['order' => $order])->with(
            'error',
            $error
        );
    }

    public function cancel($order, $request): RedirectResponse
    {
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }
}
