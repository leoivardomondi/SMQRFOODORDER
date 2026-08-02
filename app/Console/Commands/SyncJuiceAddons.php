<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ItemCategory;
use App\Models\Item;
use App\Models\ItemAddon;

class SyncJuiceAddons extends Command
{
    protected $signature = 'app:sync-juice-addons';
    protected $description = 'Add all drinks under Cold-Pressed Juices as addons to items under all categories except Daily Offers and Group Deals';

    public function handle()
    {
        $this->info('Starting Juice Addons Sync...');

        $juiceCategory = ItemCategory::where('name', 'LIKE', '%COLD-PRESSED%')
            ->orWhere('name', 'LIKE', '%JUICE%')
            ->first();

        if (!$juiceCategory) {
            $this->error('Cold-Pressed Juices category not found!');
            return 1;
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

        $addedCount = 0;
        foreach ($targetItems as $targetItem) {
            foreach ($juiceItems as $juiceItem) {
                $exists = ItemAddon::where('item_id', $targetItem->id)
                    ->where('addon_item_id', $juiceItem->id)
                    ->exists();

                if (!$exists) {
                    ItemAddon::create([
                        'item_id' => $targetItem->id,
                        'addon_item_id' => $juiceItem->id,
                        'addon_item_variation' => null,
                    ]);
                    $addedCount++;
                }
            }
        }

        $this->info("Successfully added {$addedCount} Juice Addons!");
        return 0;
    }
}
