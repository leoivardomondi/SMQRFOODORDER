<?php

namespace App\Services;

use Smartisan\Settings\Facades\Settings;
use App\Models\PaymentGateway;

class SettingService
{
    public function list() : array
    {
        $array = [];
        $array = array_merge($array, Settings::group('company')->all());
        $array = array_merge($array, Settings::group('site')->all());
        $array = array_merge($array, Settings::group('theme')->all());
        $array = array_merge($array, Settings::group('otp')->all());
        $array = array_merge($array, Settings::group('social_media')->all());
        $array = array_merge($array, Settings::group('order_setup')->all());
        $array = array_merge($array, Settings::group('notification')->all());
        $array = array_merge($array, Settings::group('whatsapp')->all());
        $array = array_merge($array, Settings::group('cookies')->all());
        $array['primary_payment_gateway'] = Settings::group('payment_gateway')->get('primary_payment_gateway', 'pesapal');

        // Add Paystack public key
        $paystack = PaymentGateway::with('gatewayOptions')->where(['slug' => 'paystack'])->first();
        if ($paystack) {
            $options = $paystack->gatewayOptions->pluck('value', 'option');
            $array['paystack_public_key'] = $options['paystack_public_key'] ?? '';
            $array['paystack_status'] = $paystack->status;
        }

        return $array;
    }
}
