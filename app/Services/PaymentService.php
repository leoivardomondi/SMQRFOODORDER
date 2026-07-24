<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;
use App\Events\SendOrderMail;
use App\Events\SendOrderSms;
use App\Events\SendOrderPush;
use App\Events\SendOrderGotMail;
use App\Events\SendOrderGotSms;
use App\Events\SendOrderGotPush;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PaymentService
{
    public function payment($order, $gatewaySlug, $transactionNo, array $providerDetails = [])
    {
        return DB::transaction(function () use ($order, $gatewaySlug, $transactionNo, $providerDetails) {
            $attributes = [
                'order_id' => $order->id,
                'type'     => 'payment',
            ];

            $values = [
                'transaction_no' => $transactionNo,
                'amount'         => $order->total,
                'payment_method' => $gatewaySlug,
                'sign'           => '+',
            ];

            $optionalFields = [
                'provider_receipt'  => $providerDetails['receipt_number'] ?? null,
                'provider_name'     => $providerDetails['provider_name'] ?? null,
                'payer_phone'       => $providerDetails['payer_phone'] ?? null,
                'payer_phone_last4'  => $providerDetails['payer_phone_last4'] ?? null,
                'payment_channel'   => $providerDetails['channel'] ?? null,
            ];

            foreach ($optionalFields as $field => $val) {
                if (Schema::hasColumn('transactions', $field)) {
                    $values[$field] = $val;
                }
            }

            $transaction = Transaction::firstOrCreate($attributes, $values);

            $fillData = [];
            foreach ($optionalFields as $field => $val) {
                if (Schema::hasColumn('transactions', $field)) {
                    $fillData[$field] = $val ?? ($transaction->$field ?? null);
                }
            }
            if (!empty($fillData)) {
                $transaction->fill($fillData)->save();
            }

            if ($order->payment_status !== PaymentStatus::PAID) {
                $order->payment_status = PaymentStatus::PAID;
                $order->save();
                
                SendOrderMail::dispatch(['order_id' => $order->id, 'status' => OrderStatus::PENDING]);
                SendOrderSms::dispatch(['order_id' => $order->id, 'status' => OrderStatus::PENDING]);
                SendOrderPush::dispatch(['order_id' => $order->id, 'status' => OrderStatus::PENDING]);

                SendOrderGotMail::dispatch(['order_id' => $order->id]);
                SendOrderGotSms::dispatch(['order_id' => $order->id]);
                SendOrderGotPush::dispatch(['order_id' => $order->id]);

                $this->sendMetaWhatsappNotification($order);
            }

            return $transaction;
        }, 3);
    }

    protected function sendMetaWhatsappNotification($order)
    {
        $status = \Smartisan\Settings\Facades\Settings::group('whatsapp')->get('whatsapp_status');
        if ($status !== null && (int)$status !== \App\Enums\Activity::ENABLE) {
            return;
        }

        $phoneId = \Smartisan\Settings\Facades\Settings::group('whatsapp')->get('whatsapp_phone_number_id') ?: env('META_WHATSAPP_PHONE_NUMBER_ID');
        $accessToken = \Smartisan\Settings\Facades\Settings::group('whatsapp')->get('whatsapp_access_token') ?: env('META_WHATSAPP_ACCESS_TOKEN');
        $templateName = \Smartisan\Settings\Facades\Settings::group('whatsapp')->get('whatsapp_template_name') ?: env('META_WHATSAPP_TEMPLATE_NAME');
        $recipient = \Smartisan\Settings\Facades\Settings::group('whatsapp')->get('whatsapp_recipient_phone') ?: env('META_WHATSAPP_RECIPIENT_PHONE');

        if (blank($phoneId) || blank($accessToken) || blank($templateName) || blank($recipient)) {
            return;
        }

        try {
            $isTakeaway = ($order->order_type == \App\Enums\OrderType::TAKEAWAY);
            $typeEmoji = $isTakeaway ? '✅🏫' : '✅🚚';
            $typeLabel = $isTakeaway ? 'Pickup' : 'Delivery';

            $itemsString = '';
            $comments = [];

            if (!$order->relationLoaded('orderItems')) {
                $order->load('orderItems');
            }

            foreach ($order->orderItems as $item) {
                $itemsString .= "🔘 " . $item->quantity . " X " . ($item->orderItem?->name ?? 'Item') . " - KSh" . number_format($item->price, 2) . "\n";
                if (!blank($item->instruction)) {
                    $comments[] = $item->instruction;
                }
            }

            $commentText = count($comments) > 0 ? implode(', ', $comments) : 'None';

            $messageText = "{$typeEmoji}\n" .
                           "{$typeLabel} Order No: " . ($order->order_serial_no ?? '') . "\n\n" .
                           "---------\n" .
                           rtrim($itemsString) . "\n\n" .
                           "---------\n" .
                           "🧾 Total: KSh" . number_format($order->total, 2) . "\n" .
                           "---------\n\n" .
                           "🗒️ Comment\n" .
                           " {$commentText}\n\n" .
                           "✅ {$typeLabel} Details\n\n" .
                           "Customer name: " . ($order->user?->name ?? 'Guest') . "\n" .
                           "Customer phone: " . ($order->user?->phone ?? '') . "\n" .
                           "{$typeLabel} time: " . ($order->delivery_time ?? '') . "\n\n" .
                           "Bwibo Restaurant will confirm your order upon receiving the message.\n\n" .
                           "💳 Payment Options\n" .
                           "TO PAY VIA MPESA OR CARD, CLICK ON THE LAST LINK IN THIS TEXT\n\n" .
                           "💳 Pay now\n" .
                           "https://whatsapp.seamlessqrcode.com/business/bwiborestaurant/?pay=" . $order->id;

            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'to'                => $recipient,
                    'type'              => 'template',
                    'template'          => [
                         'name'     => $templateName,
                         'language' => [
                             'code' => 'en_US',
                         ],
                         'components' => [
                             [
                                 'type'       => 'body',
                                 'parameters' => [
                                     [
                                         'type' => 'text',
                                         'text' => $messageText,
                                     ],
                                 ],
                             ],
                         ],
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('Meta WhatsApp notification failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Meta WhatsApp notification error: ' . $e->getMessage());
        }
    }

    public function cashBack($order, $gatewaySlug, $transactionNo)
    {
        return DB::transaction(function () use ($order, $gatewaySlug, $transactionNo) {
            $payment = Transaction::where(['order_id' => $order->id, 'type' => 'payment'])->first();
            if (!$payment) {
                return null;
            }

            $transaction = Transaction::firstOrCreate([
                'order_id' => $order->id,
                'type'     => 'cash_back',
            ], [
                'transaction_no' => $transactionNo,
                'amount'         => $order->total,
                'payment_method' => $gatewaySlug,
                'sign'           => '-',
            ]);

            $user = User::find($order->user_id);
            if ($transaction->wasRecentlyCreated && $user) {
                $user->balance = ($user->balance + $order->total);
                $user->save();
            }

            return $transaction;
        }, 3);
    }
}
