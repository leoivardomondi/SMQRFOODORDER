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
        // 1. Ensure section headers have clean priorities
        DB::table('menus')->where('url', '#')->where('language', 'pos_and_orders')->update(['priority' => 10]);
        DB::table('menus')->where('url', '#')->where('language', 'promo')->update(['priority' => 20]);
        DB::table('menus')->where('url', '#')->where('language', 'communications')->update(['priority' => 30]);
        DB::table('menus')->where('url', '#')->where('language', 'users')->update(['priority' => 40]);
        DB::table('menus')->where('url', '#')->where('language', 'accounts')->update(['priority' => 50]);
        DB::table('menus')->where('url', '#')->where('language', 'reports')->update(['priority' => 60]);
        DB::table('menus')->where('url', '#')->where('language', 'setup')->update(['priority' => 70]);

        $setupHeader = DB::table('menus')->where('url', '#')->where('language', 'setup')->first();

        // 2. Ensure Dashboard is priority 1, parent 0
        DB::table('menus')->where('url', 'dashboard')->update(['priority' => 1, 'parent' => 0]);

        // 3. Ensure Items exists and is priority 2, parent 0
        $itemsExists = DB::table('menus')->where('url', 'items')->exists();
        if (!$itemsExists) {
            DB::table('menus')->insert([
                'name'       => 'Items',
                'language'   => 'items',
                'url'        => 'items',
                'icon'       => 'lab lab-items',
                'priority'   => 2,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            DB::table('menus')->where('url', 'items')->update(['parent' => 0, 'priority' => 2]);
        }

        // 4. Ensure Item Categories exists and is priority 3, parent 0
        $catExists = DB::table('menus')->where('url', 'item-categories')->exists();
        if (!$catExists) {
            DB::table('menus')->insert([
                'name'       => 'Item Categories',
                'language'   => 'item_categories',
                'url'        => 'item-categories',
                'icon'       => 'lab lab-item-categories',
                'priority'   => 3,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            DB::table('menus')->where('url', 'item-categories')->update(['parent' => 0, 'priority' => 3]);
        }

        // 5. Ensure Attributes exists and is priority 4, parent 0
        DB::table('menus')->where('url', 'settings/item-attributes')->update(['url' => 'item-attributes']);
        $attrExists = DB::table('menus')->where('url', 'item-attributes')->exists();
        if (!$attrExists) {
            DB::table('menus')->insert([
                'name'       => 'Attributes',
                'language'   => 'attributes',
                'url'        => 'item-attributes',
                'icon'       => 'lab lab-item-attributes',
                'priority'   => 4,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            DB::table('menus')->where('url', 'item-attributes')->update(['parent' => 0, 'priority' => 4]);
        }

        // 6. Ensure Settings is under Setup section
        if ($setupHeader) {
            DB::table('menus')->where('url', 'settings')->update(['parent' => $setupHeader->id, 'priority' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
