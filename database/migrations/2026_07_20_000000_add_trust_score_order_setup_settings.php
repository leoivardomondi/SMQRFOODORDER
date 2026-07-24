<?php

use App\Enums\Activity;
use Illuminate\Database\Migrations\Migration;
use Smartisan\Settings\Facades\Settings;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Settings::group('order_setup')->set([
            'order_setup_trust_score_enable'     => Activity::ENABLE,
            'order_setup_trust_score_min_orders' => "1",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Settings::group('order_setup')->forget('order_setup_trust_score_enable');
        Settings::group('order_setup')->forget('order_setup_trust_score_min_orders');
    }
};
