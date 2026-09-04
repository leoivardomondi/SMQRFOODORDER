<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasColumn('items', 'branch_id')) {
            Schema::table('items', function (Blueprint $table): void {
                $table->unsignedBigInteger('branch_id')->default(0)->nullable()->after('item_category_id');
            });
        }
        if (!Schema::hasColumn('item_categories', 'branch_id')) {
            Schema::table('item_categories', function (Blueprint $table): void {
                $table->unsignedBigInteger('branch_id')->default(0)->nullable()->after('slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'branch_id')) {
            Schema::table('items', function (Blueprint $table): void {
                $table->dropColumn('branch_id');
            });
        }
        if (Schema::hasColumn('item_categories', 'branch_id')) {
            Schema::table('item_categories', function (Blueprint $table): void {
                $table->dropColumn('branch_id');
            });
        }
    }
};
