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
        // 1. Ensure Items exists at top-level
        $itemsExists = DB::table('menus')->where('url', 'items')->exists();
        if (!$itemsExists) {
            DB::table('menus')->insert([
                'name'       => 'Items',
                'language'   => 'items',
                'url'        => 'items',
                'icon'       => 'lab lab-items',
                'priority'   => 100,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            DB::table('menus')->where('url', 'items')->update(['parent' => 0]);
        }

        // 2. Ensure Item Categories exists at top-level
        $catExists = DB::table('menus')->where('url', 'item-categories')->exists();
        if (!$catExists) {
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
        } else {
            DB::table('menus')->where('url', 'item-categories')->update(['parent' => 0]);
        }

        // 3. Ensure Attributes exists at top-level
        $attrExists = DB::table('menus')->where('url', 'settings/item-attributes')->exists();
        if (!$attrExists) {
            DB::table('menus')->insert([
                'name'       => 'Attributes',
                'language'   => 'attributes',
                'url'        => 'settings/item-attributes',
                'icon'       => 'lab lab-item-attributes',
                'priority'   => 100,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            DB::table('menus')->where('url', 'settings/item-attributes')->update(['parent' => 0]);
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
