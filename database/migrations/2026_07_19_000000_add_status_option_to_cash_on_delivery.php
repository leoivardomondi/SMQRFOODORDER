<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\Activity;
use App\Enums\InputType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $gateway = DB::table('payment_gateways')->where('slug', 'cash-on-delivery')->first();
        if ($gateway) {
            $exists = DB::table('gateway_options')
                ->where('model_id', $gateway->id)
                ->where('model_type', 'App\Models\PaymentGateway')
                ->where('option', 'cash_on_delivery_status')
                ->exists();

            if (!$exists) {
                DB::table('gateway_options')->insert([
                    'model_id'   => $gateway->id,
                    'model_type' => 'App\Models\PaymentGateway',
                    'option'     => 'cash_on_delivery_status',
                    'value'      => Activity::ENABLE,
                    'type'       => InputType::SELECT,
                    'activities' => json_encode([
                        Activity::ENABLE  => 'enable',
                        Activity::DISABLE => 'disable'
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $gateway = DB::table('payment_gateways')->where('slug', 'cash-on-delivery')->first();
        if ($gateway) {
            DB::table('gateway_options')
                ->where('model_id', $gateway->id)
                ->where('model_type', 'App\Models\PaymentGateway')
                ->where('option', 'cash_on_delivery_status')
                ->delete();
        }
    }
};
