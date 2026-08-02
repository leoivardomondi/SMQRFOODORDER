<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemCategory;
use App\Models\Item;
use App\Models\ItemAddon;

class SyncJuiceAddonsSeeder extends Seeder
{
    public function run()
    {
        $juiceCategory = ItemCategory::where('name', 'LIKE', '%COLD-PRESSED%')
            ->orWhere('name', 'LIKE', '%JUICE%')
            ->first();

        if (!$juiceCategory) {
            return;
        }

        $juiceItems = Item::where('item_category_id', $juiceCategory->id)->get();

        $excludedCategories = ItemCategory::where(function($query) {
            $query->where('name', 'LIKE', '%DAILY OFFER%')
                  ->orWhere('name', 'LIKE', '%OFFER%')
                  ->orWhere('name', 'LIKE', '%GROUP DEAL%')
                  ->orWhere('name', 'LIKE', '%COLD-PRESSED%')
                  ->orWhere('name', 'LIKE', '%JUICE%');
        })->pluck('id')->toArray();

        $targetItems = Item::whereNotIn('item_category_id', $excludedCategories)->get();

        foreach ($targetItems as $targetItem) {
            foreach ($juiceItems as $juiceItem) {
                ItemAddon::firstOrCreate([
                    'item_id' => $targetItem->id,
                    'addon_item_id' => $juiceItem->id,
                ], [
                    'addon_item_variation' => null,
                ]);
            }
        }
    }
}
