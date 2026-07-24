<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('provider_name')->nullable()->after('provider_receipt');
            $table->string('payer_phone')->nullable()->after('provider_name');
            $table->string('payer_phone_last4')->nullable()->after('payer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['provider_name', 'payer_phone', 'payer_phone_last4']);
        });
    }
};
