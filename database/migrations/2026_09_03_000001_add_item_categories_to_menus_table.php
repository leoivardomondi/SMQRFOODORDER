<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exists = DB::table('menus')->where('url', 'item-categories')->exists();
        if (!$exists) {
            DB::table('menus')->insert([
                'name'       => 'Item Categories',
                'language'   => 'item_categories',
                'url'        => 'item-categories',
                'icon'       => 'lab lab-item-categories',
                'priority'   => 100,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('menus')->where('url', 'item-categories')->delete();
    }
};
