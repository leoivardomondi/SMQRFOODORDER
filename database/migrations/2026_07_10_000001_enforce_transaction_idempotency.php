<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unique(['order_id', 'type'], 'transactions_order_type_unique');
            $table->unique(['payment_method', 'transaction_no'], 'transactions_gateway_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('transactions_order_type_unique');
            $table->dropUnique('transactions_gateway_number_unique');
        });
    }
};
