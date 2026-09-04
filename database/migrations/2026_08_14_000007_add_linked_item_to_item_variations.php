<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_variations', function (Blueprint $table): void {
            if (!Schema::hasColumn('item_variations', 'linked_item_id')) {
                $table->unsignedBigInteger('linked_item_id')->nullable()->after('item_id');
                $table->index('linked_item_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('item_variations', function (Blueprint $table): void {
            $table->dropIndex(['linked_item_id']);
            $table->dropColumn('linked_item_id');
        });
    }
};
