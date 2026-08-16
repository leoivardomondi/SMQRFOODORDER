<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('items', 'visible_days')) {
            Schema::table('items', function (Blueprint $table): void {
                $table->json('visible_days')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('items', 'visible_days')) {
            Schema::table('items', function (Blueprint $table): void {
                $table->dropColumn('visible_days');
            });
        }
    }
};
