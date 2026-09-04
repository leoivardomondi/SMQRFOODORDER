<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'provider_name')) {
                $table->string('provider_name')->nullable()->after('provider_receipt');
            }
            if (!Schema::hasColumn('transactions', 'payer_phone')) {
                $table->string('payer_phone')->nullable()->after('provider_name');
            }
            if (!Schema::hasColumn('transactions', 'payer_phone_last4')) {
                $table->string('payer_phone_last4')->nullable()->after('payer_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['provider_name', 'payer_phone', 'payer_phone_last4']);
        });
    }
};
