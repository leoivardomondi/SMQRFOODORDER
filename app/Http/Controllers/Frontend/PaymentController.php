<?php

namespace App\Http\Controllers\Frontend;


use App\Enums\Activity;
use App\Enums\PaymentStatus;
use App\Http\Requests\PaymentRequest;
use App\Libraries\AppLibrary;
use App\Models\Currency;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\ThemeSetting;
use App\Models\WhatsappOrderSetup;
use App\Services\PaymentManagerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Smartisan\Settings\Facades\Settings;

class PaymentController extends Controller
{
    private PaymentManagerService $paymentManagerService;

    public function __construct(PaymentManagerService $paymentManagerService)
    {
        $this->paymentManagerService = $paymentManagerService;
    }

    public function index(
        Order $order,
        Request $request
    ): \Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View | \Illuminate\Contracts\Foundation\Application | \Illuminate\Http\RedirectResponse {
        $this->authorizePaymentPage($request, $order);
        $request->session()->put('payment_order_id', $order->id);
        $credit          = false;
        $paymentGateways = PaymentGateway::with('gatewayOptions')
            ->whereNotIn('id', [1])
            ->whereIn('slug', config('payments.verified_gateways'))
            ->where(['status' => Activity::ENABLE])
            ->get();
        $company         = Settings::group('company')->all();
        $logo            = ThemeSetting::where(['key' => 'theme_logo'])->first();
        $faviconLogo     = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();
        $currency        = Currency::findOrFail(Settings::group('site')->get('site_default_currency'));
        if ($order?->user?->balance >= $order->total) {
            $credit = true;
        }

        $paymentGateways = $paymentGateways->filter(function ($paymentGateway) use ($credit) {
            return $credit || $paymentGateway->slug !== 'credit';
        })->values();

        if (!blank($order->transaction) || $order->payment_status === PaymentStatus::PAID) {
            return redirect()->route('payment.successful', ['order' => $order]);
        }

        if (blank($order->transaction) && $order->payment_status === PaymentStatus::UNPAID) {
            if (!$request->session()->has('error')) {
                // Find enabled online payment gateways (excluding internal 'credit')
                $onlineGateways = $paymentGateways->filter(function ($g) {
                    return $g->slug !== 'credit' && $g->status == Activity::ENABLE;
                })->values();

                if ($onlineGateways->count() > 0) {
                    $primaryGatewaySlug = \Smartisan\Settings\Facades\Settings::group('payment_gateway')->get('primary_payment_gateway', 'pesapal');

                    // Preference order: Primary gateway, then pesapal, then paystack, then first available enabled online gateway
                    $preferredGateway = $onlineGateways->firstWhere('slug', $primaryGatewaySlug)
                        ?? $onlineGateways->firstWhere('slug', 'pesapal')
                        ?? $onlineGateways->firstWhere('slug', 'paystack')
                        ?? $onlineGateways->first();

                    if ($preferredGateway && $this->paymentManagerService->gateway($preferredGateway->slug)->status()) {
                        return $this->paymentManagerService->gateway($preferredGateway->slug)->payment($order, $request);
                    }
                }
            }

            return view('payment', [
                'company'         => $company,
                'logo'            => $logo,
                'currency'        => $currency,
                'faviconLogo'     => $faviconLogo,
                'paymentGateways' => $paymentGateways,
                'order'           => $order,
                'creditAmount'    => AppLibrary::currencyAmountFormat($order?->user?->balance),
                'credit'          => $credit
            ]);
        }
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }

    public function payment(Order $order, PaymentRequest $request)
    {
        $this->authorizePaymentPage($request, $order);
        abort_unless(in_array($request->paymentMethod, config('payments.verified_gateways'), true), Response::HTTP_FORBIDDEN);
        if ($this->paymentManagerService->gateway($request->paymentMethod)->status()) {
            $className = 'App\\Http\\PaymentGateways\\PaymentRequests\\' . ucfirst($request->paymentMethod);
            $gateway   = new $className;
            $request->validate($gateway->rules());
            return $this->paymentManagerService->gateway($request->paymentMethod)->payment($order, $request);
        } else {
            return redirect()->route('payment.index', ['order' => $order])->with(
                'error',
                trans('all.message.payment_gateway_disable')
            );
        }
    }

    public function success(PaymentGateway $paymentGateway, Order $order, Request $request)
    {
        abort_unless(in_array($paymentGateway->slug, config('payments.verified_gateways'), true), Response::HTTP_FORBIDDEN);
        abort_unless($paymentGateway->status === Activity::ENABLE, Response::HTTP_NOT_FOUND);
        abort_unless($order->payment_status === PaymentStatus::UNPAID || !blank($order->transaction), Response::HTTP_CONFLICT);
        return $this->paymentManagerService->gateway($paymentGateway->slug)->success($order, $request);
    }

    public function fail(PaymentGateway $paymentGateway, Order $order, Request $request)
    {
        return $this->paymentManagerService->gateway($paymentGateway->slug)->fail($order, $request);
    }

    public function cancel(PaymentGateway $paymentGateway, Order $order, Request $request)
    {
        $this->authorizePaymentPage($request, $order);

        // The checkout is client-side and keeps the pending basket/draft in the
        // browser. Returning here lets the customer amend it and try again.
        return redirect('/checkout?payment=cancelled')->with(
            'error',
            trans('all.message.payment_canceled')
        );
    }

    public function ipn(PaymentGateway $paymentGateway, Request $request)
    {
        $orderTrackingId   = $request->get('OrderTrackingId') ?? $request->get('orderTrackingId');
        $merchantReference = $request->get('OrderMerchantReference') ?? $request->get('orderMerchantReference');

        if ($paymentGateway->slug === 'pesapal' && !blank($orderTrackingId) && !blank($merchantReference)) {
            $parts   = explode('-', $merchantReference);
            $orderId = $parts[1] ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                if ($order && $order->payment_status === PaymentStatus::UNPAID) {
                    $pesapalGateway = new \App\Http\PaymentGateways\Gateways\Pesapal();
                    $pesapalGateway->verifyAndCompletePayment($order, $orderTrackingId);
                }
            }
        }

        return response()->json([
            'orderNotificationType'  => 'IPNCHANGE',
            'orderTrackingId'        => $orderTrackingId,
            'orderMerchantReference' => $merchantReference,
            'status'                 => '200',
        ]);
    }

    public function successful(
        Order $order,
        Request $request
    ): \Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View | \Illuminate\Contracts\Foundation\Application | \Illuminate\Http\RedirectResponse {
        $this->authorizePaymentPage($request, $order);
        $company     = Settings::group('company')->all();
        $logo        = ThemeSetting::where(['key' => 'theme_logo'])->first();
        $faviconLogo = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();

        if (!blank($order->transaction)) {
            $receiptPreviewUrl = URL::temporarySignedRoute(
                'payment.receipt.preview',
                now()->addDays(30),
                ['order' => $order]
            );
            $whatsappData = $this->paidOrderWhatsappData($order, $receiptPreviewUrl);
            return view('paymentSuccess', [
                'company'     => $company,
                'logo'        => $logo,
                'faviconLogo' => $faviconLogo,
                'order'       => $order,
                'whatsappUrl' => $whatsappData
                    ? 'https://api.whatsapp.com/send?phone=' . $whatsappData['phone'] . '&text=' . rawurlencode(implode("\n", $whatsappData['message_lines']))
                    : null,
                'receiptLines' => $whatsappData['receipt_lines'] ?? [],
                'receiptStoreUrl' => route('payment.receipt.store', ['order' => $order]),
            ]);
        }
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }

    public function storeReceiptImage(Order $order, Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorizePaymentPage($request, $order);
        abort_unless(!blank($order->transaction), Response::HTTP_CONFLICT);

        $request->validate([
            'receipt' => ['required', 'file', 'mimes:png', 'max:5120'],
        ]);

        Storage::disk('private')->putFileAs(
            'paid-order-receipts',
            $request->file('receipt'),
            'order-' . $order->id . '.png'
        );

        return response()->json(['saved' => true]);
    }

    public function receiptPreview(Order $order): \Illuminate\Contracts\View\View
    {
        abort_unless(!blank($order->transaction), Response::HTTP_NOT_FOUND);
        $receiptPath = 'paid-order-receipts/order-' . $order->id . '.png';

        return view('paymentReceiptPreview', [
            'order' => $order,
            'imageUrl' => URL::temporarySignedRoute(
                'payment.receipt.image',
                now()->addMinutes(10),
                ['order' => $order]
            ),
            'adminOrderUrl' => url('/admin/online-orders/show/' . $order->id),
            'hasImage' => Storage::disk('private')->exists($receiptPath),
        ]);
    }

    public function receiptImage(Order $order): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        abort_unless(!blank($order->transaction), Response::HTTP_NOT_FOUND);
        $path = 'paid-order-receipts/order-' . $order->id . '.png';
        abort_unless(Storage::disk('private')->exists($path), Response::HTTP_NOT_FOUND);

        return response()->file(Storage::disk('private')->path($path), [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=600',
        ]);
    }

    private function paidOrderWhatsappData(Order $order, string $receiptPreviewUrl): ?array
    {
        $setup = WhatsappOrderSetup::where('branch_id', $order->branch_id)
            ->where('status', Activity::ENABLE)
            ->first();

        if (blank($setup?->phone)) {
            return null;
        }

        $order->loadMissing(['orderItems.orderItem', 'user', 'address', 'branch', 'transaction']);
        $phone = preg_replace('/\D+/', '', $setup->phone);
        if (str_starts_with($phone, '0')) {
            $callingCode = preg_replace('/\D+/', '', (string) ($order->user?->country_code ?? ''));
            if ($callingCode !== '') {
                $phone = $callingCode . ltrim($phone, '0');
            }
        }

        $gatewayName = ucfirst($order->transaction?->payment_method ?? 'Online');
        $providerName = $order->transaction?->provider_name;

        $lines = [
            'PAID ORDER - ' . ($order->branch?->name ?? Settings::group('company')->get('company_name') ?? 'Bwibo Restaurant'),
            '****************************************************',
            'Order: #' . $order->order_serial_no,
            'Payment status: PAID',
            'Payment Gateway: ' . $gatewayName . (!blank($providerName) ? ' (' . $providerName . ')' : ''),
            'Payment date/time: ' . AppLibrary::datetime(
                $order->transaction?->created_at ?? $order->updated_at,
                'd M Y, h:i A'
            ),
            'Transaction Ref: ' . ($order->transaction?->transaction_no ?? 'Confirmed'),
        ];

        if (!blank($order->transaction?->provider_receipt) && $order->transaction->provider_receipt !== $order->transaction->transaction_no) {
            $lines[] = 'M-PESA receipt: ' . $order->transaction->provider_receipt;
        }
        if (!blank($order->transaction?->payer_phone)) {
            $lines[] = 'Paying contact: ' . $order->transaction->payer_phone;
        }

        array_push($lines,
            'Order type: ' . ((int) $order->order_type === \App\Enums\OrderType::TAKEAWAY ? 'Pickup' : 'Delivery'),
            'Delivery/Pickup time: ' . ($order->delivery_time ?: 'As soon as possible'),
            '',
            'ORDER DETAILS',
            '--------------------------'
        );

        foreach ($order->orderItems as $index => $item) {
            $lines[] = ($index + 1) . ') ' . $item->quantity . ' x ' . ($item->orderItem?->name ?? 'Item') .
                ' - KSh ' . number_format((float) $item->total_price, 2);

            foreach ((array) json_decode($item->item_variations ?: '[]', true) as $variation) {
                $lines[] = '   ' . ($variation['variation_name'] ?? 'Option') . ': ' . ($variation['name'] ?? '');
            }
            foreach ((array) json_decode($item->item_extras ?: '[]', true) as $extra) {
                $lines[] = '   Extra: ' . ($extra['name'] ?? '');
            }
            if (!blank($item->instruction)) {
                $lines[] = '   Note: ' . $item->instruction;
            }
        }

        array_push($lines,
            '--------------------------',
            'Subtotal: KSh ' . number_format((float) $order->subtotal, 2),
            'Discount: KSh ' . number_format((float) $order->discount, 2),
            'Delivery: KSh ' . number_format((float) $order->delivery_charge, 2),
            'TOTAL PAID: KSh ' . number_format((float) $order->total, 2),
            '',
            'CUSTOMER',
            'Name: ' . ($order->user?->name ?? 'Guest'),
            'Phone: ' . $this->internationalPhone(
                (string) ($order->user?->country_code ?? ''),
                (string) ($order->user?->phone ?? '')
            ),
            'Address: ' . ($order->address?->address ?? 'Pickup'),
            'Apartment: ' . ($order->address?->apartment ?? 'N/A'),
            '',
            'Payment has been completed. Please confirm this paid order.',
            '',
            'View receipt and open order in Admin:',
            $receiptPreviewUrl
        );

        return [
            'phone' => $phone,
            'message_lines' => [
                'PAID ORDER ALERT - ' . ($order->branch?->name ?? 'Bwibo Restaurant'),
                '',
                'Payment has been confirmed for order #' . $order->order_serial_no . '.',
                'Please open the secure receipt below to review and process the order:',
                '',
                $receiptPreviewUrl,
            ],
            'receipt_lines' => $lines,
        ];
    }

    private function internationalPhone(string $countryCode, string $localPhone): string
    {
        $code = preg_replace('/\D+/', '', $countryCode);
        $number = preg_replace('/\D+/', '', $localPhone);

        if ($code !== '' && str_starts_with($number, $code)) {
            $number = substr($number, strlen($code));
        }

        return ($code !== '' ? '+' . $code : '') . ltrim($number, '0');
    }

    private function authorizePaymentPage(Request $request, Order $order): void
    {
        $ownsOrder = optional($request->user())->id === $order->user_id;
        $hasSession = (int) $request->session()->get('payment_order_id') === $order->id;
        abort_unless($request->hasValidSignature() || $ownsOrder || $hasSession, Response::HTTP_FORBIDDEN);
    }
}
