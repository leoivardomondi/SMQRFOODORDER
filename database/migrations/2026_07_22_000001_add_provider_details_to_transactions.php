<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'provider_receipt')) {
                $table->string('provider_receipt')->nullable()->after('transaction_no');
            }
            if (!Schema::hasColumn('transactions', 'payment_channel')) {
                $table->string('payment_channel')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['provider_receipt', 'payment_channel']);
        });
    }
};
