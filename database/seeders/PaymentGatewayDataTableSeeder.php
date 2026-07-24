<?php

namespace Database\Seeders;

use App\Enums\GatewayMode;
use App\Enums\Activity;
use App\Models\GatewayOption;
use App\Models\PaymentGateway;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;

class PaymentGatewayDataTableSeeder extends Seeder
{

    public array $gateways = [
        [
            "slug"    => "paypal",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'paypal_app_id',
                    "value"  => 'sb-qzxs18789565@business.example.com',
                ],
                [
                    "option" => 'paypal_client_id',
                    "value"  => ''
                ],
                [
                    "option" => 'paypal_client_secret',
                    "value"  => ''
                ],
                [
                    "option" => 'paypal_mode',
                    "value"  => GatewayMode::SANDBOX
                ],
                [
                    "option" => 'paypal_status',
                    "value"  => Activity::ENABLE
                ],
            ]
        ],
        [
            "slug"    => "stripe",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'stripe_key',
                    "value"  => '',
                ],
                [
                    "option" => 'stripe_secret',
                    "value"  => '',
                ],
                [
                    "option" => 'stripe_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'stripe_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "flutterwave",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'flutterwave_public_key',
                    "value"  => '',
                ],
                [
                    "option" => 'flutterwave_secret_key',
                    "value"  => '',
                ],
                [
                    "option" => 'flutterwave_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'flutterwave_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "paystack",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'paystack_public_key',
                    "value"  => '',
                ],
                [
                    "option" => 'paystack_secret_key',
                    "value"  => '',
                ],
                [
                    "option" => 'paystack_payment_url',
                    "value"  => 'https://api.paystack.co',
                ],
                [
                    "option" => 'paystack_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'paystack_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "sslcommerz",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'sslcommerz_store_name',
                    "value"  => 'testfoodkp1pi',
                ],
                [
                    "option" => 'sslcommerz_store_id',
                    "value"  => 'foodk6472ed754a400',
                ],
                [
                    "option" => 'sslcommerz_store_password',
                    "value"  => '',
                ],
                [
                    "option" => 'sslcommerz_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'sslcommerz_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "mollie",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'mollie_api_key',
                    "value"  => '',
                ],
                [
                    "option" => 'mollie_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'mollie_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "senangpay",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'senangpay_merchant_id',
                    "value"  => '',
                ],
                [
                    "option" => 'senangpay_secret_key',
                    "value"  => '',
                ],
                [
                    "option" => 'senangpay_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'senangpay_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "bkash",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'bkash_app_key',
                    "value"  => '',
                ],
                [
                    "option" => 'bkash_app_secret',
                    "value"  => '',
                ],
                [
                    "option" => 'bkash_username',
                    "value"  => ''
                ],
                [
                    "option" => 'bkash_password',
                    "value"  => ''
                ],
                [
                    "option" => 'bkash_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'bkash_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "paytm",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'paytm_merchant_id',
                    "value"  => '',
                ],
                [
                    "option" => 'paytm_merchant_key',
                    "value"  => '',
                ],
                [
                    "option" => 'paytm_merchant_website',
                    "value"  => 'WEBSTAGING',
                ],
                [
                    "option" => 'paytm_channel',
                    "value"  => 'WEB',
                ],
                [
                    "option" => 'paytm_industry_type',
                    "value"  => 'Retail',
                ],
                [
                    "option" => 'paytm_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'paytm_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "razorpay",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'razorpay_key',
                    "value"  => 'rzp_test_eeBR6yhSmKHB65',
                ],
                [
                    "option" => 'razorpay_secret',
                    "value"  => '',
                ],
                [
                    "option" => 'razorpay_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'razorpay_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "mercadopago",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'mercadopago_client_id',
                    "value"  => '',
                ],
                [
                    "option" => 'mercadopago_client_secret',
                    "value"  => '',
                ],
                [
                    "option" => 'mercadopago_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'mercadopago_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "cashfree",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'cashfree_app_id',
                    "value"  => 'TEST408093487955435d9cfa4c9493390804',
                ],
                [
                    "option" => 'cashfree_secret_key',
                    "value"  => '',
                ],
                [
                    "option" => 'cashfree_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'cashfree_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "payfast",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'payfast_merchant_id',
                    "value"  => '',
                ],
                [
                    "option" => 'payfast_merchant_key',
                    "value"  => '',
                ],
                [
                    "option" => 'payfast_passphrase',
                    "value"  => 'payfast',
                ],
                [
                    "option" => 'payfast_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'payfast_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "skrill",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'skrill_merchant_email',
                    "value"  => '',
                ],
                [
                    "option" => 'skrill_merchant_api_password',
                    "value"  => '',
                ],
                [
                    "option" => 'skrill_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'skrill_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "phonepe",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'phonepe_merchant_id',
                    "value"  => '',
                ],
                [
                    "option" => 'phonepe_merchant_user_id',
                    "value"  => '',
                ],
                [
                    "option" => 'phonepe_key_index',
                    "value"  => '1',
                ],
                [
                    "option" => 'phonepe_key',
                    "value"  => '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399',
                ],
                [
                    "option" => 'phonepe_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'phonepe_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "pesapal",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'pesapal_consumer_key',
                    "value"  => '',
                ],
                [
                    "option" => 'pesapal_consumer_secret',
                    "value"  => '',
                ],
                [
                    "option" => 'pesapal_ipn_id',
                    "value"  => 'a2d7ab30-5527-4f18-97f0-dd28b08682b7',
                ],
                [
                    "option" => 'pesapal_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'pesapal_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug"    => "iyzico",
            "status"  => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'iyzico_api_key',
                    "value"  => '',
                ],
                [
                    "option" => 'iyzico_secret_key',
                    "value"  => '',
                ],
                [
                    "option" => 'iyzico_mode',
                    "value"  => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'iyzico_status',
                    "value"  => Activity::ENABLE,
                ],
            ]
        ],
        [
            "slug" => "midtrans",
            "status" => Activity::ENABLE,
            "options" => [
                [
                    "option" => 'midtrans_server_key',
                    "value" => 'SB-Mid-server-EToGodhQrotc5PtXM-FVDeuZ',
                ],
                [
                    "option" => 'midtrans_client_key',
                    "value" => 'SB-Mid-client-atvQnnDtM4Ih31c8',
                ],
                [
                    "option" => 'midtrans_mode',
                    "value" => GatewayMode::SANDBOX,
                ],
                [
                    "option" => 'midtrans_status',
                    "value" => Activity::ENABLE,
                ],
            ]
        ]
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($this->gateways as $gateway) {
                $payment = PaymentGateway::where(['slug' => $gateway['slug']])->first();
                if ($payment) {
                    $payment->status = $gateway['status'];
                    $payment->save();
                }
                $this->gatewayOption($gateway['options']);
            }
        }
    }

    public function gatewayOption($options): void
    {
        if (!blank($options)) {
            foreach ($options as $option) {
                $gatewayOption = GatewayOption::where(['option' => $option['option']])->first();
                if ($gatewayOption) {
                    $gatewayOption->value = $option['value'];
                    $gatewayOption->save();
                }
            }
        }
    }
}
