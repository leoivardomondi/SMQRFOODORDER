<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WhatsappApiRequest;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;
use Exception;

class WhatsappApiController extends AdminController
{
    protected EnvEditor $envService;

    public function __construct(EnvEditor $envEditor)
    {
        parent::__construct();
        $this->envService = $envEditor;
        $this->middleware(['permission:settings'])->only('index', 'update', 'test');
    }

    public function index()
    {
        try {
            return response([
                'status' => true,
                'data'   => Settings::group('whatsapp')->all()
            ], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(WhatsappApiRequest $request)
    {
        try {
            if ($this->envService->getValue('DEMO')) {
                throw new Exception(trans('all.message.feature_disable'), 422);
            }

            Settings::group('whatsapp')->set($request->validated());

            $this->envService->addData([
                'META_WHATSAPP_PHONE_NUMBER_ID' => $request->whatsapp_phone_number_id ?? '',
                'META_WHATSAPP_ACCESS_TOKEN'    => $request->whatsapp_access_token ?? '',
                'META_WHATSAPP_TEMPLATE_NAME'   => $request->whatsapp_template_name ?? '',
                'META_WHATSAPP_RECIPIENT_PHONE' => $request->whatsapp_recipient_phone ?? '',
            ]);

            Artisan::call('optimize:clear');

            return response([
                'status'  => true,
                'message' => trans('all.message.setting_update'),
                'data'    => Settings::group('whatsapp')->all()
            ], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function test(Request $request)
    {
        try {
            $phoneId = $request->whatsapp_phone_number_id;
            $accessToken = $request->whatsapp_access_token;
            $templateName = $request->whatsapp_template_name;
            $recipient = $request->whatsapp_recipient_phone;

            if (blank($phoneId) || blank($accessToken) || blank($templateName) || blank($recipient)) {
                throw new Exception('Please fill in all WhatsApp API credentials before testing.', 422);
            }

            $messageText = "✅🏫\n" .
                           "TEST Pickup Order No: TEST_ORDER_123456\n\n" .
                           "---------\n" .
                           "🔘 2 X TEST ITEM - KSh100.00\n\n" .
                           "---------\n" .
                           "🧾 Total: KSh200.00\n" .
                           "---------\n\n" .
                           "🗒️ Comment\n" .
                           " This is a test order comment\n\n" .
                           "✅ TEST Details\n\n" .
                           "Customer name: Test Customer\n" .
                           "Customer phone: +254700000000\n" .
                           "TEST time: 12:00 PM - 12:30 PM\n\n" .
                           "Bwibo Restaurant will confirm your order upon receiving the message.\n\n" .
                           "💳 Payment Options\n" .
                           "TO PAY VIA MPESA OR CARD, CLICK ON THE LAST LINK IN THIS TEXT\n\n" .
                           "💳 Pay now\n" .
                           "https://whatsapp.seamlessqrcode.com/business/bwiborestaurant/?pay=test";

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

            if ($response->successful()) {
                return response([
                    'status'  => true,
                    'message' => 'Test WhatsApp message sent successfully!'
                ], 200);
            } else {
                throw new Exception('Meta Graph API Error: ' . $response->json('error.message', $response->body()), 422);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
