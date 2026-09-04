<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table): void {
            if (!Schema::hasColumn('offers', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('offers', 'visible_days')) {
                $table->json('visible_days')->nullable()->after('end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table): void {
            $table->dropColumn(['description', 'visible_days']);
        });
    }
};
