<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BwiboWeekdayOfferSeeder extends Seeder
{
    public function run(): void
    {
        $offers = [
            [
                'name' => 'Monday Burger Half-Price',
                'description' => 'Start the week with a better mood: buy one burger and get the second at half price, every Monday at Bwibo.',
                'days' => ['monday'],
                'image' => 'monday-burger-half-price.jpeg',
            ],
            [
                'name' => 'Tuesday Lunch Box',
                'description' => 'A complete lunch box for only KSh 600, available every Tuesday while the offer is live.',
                'days' => ['tuesday'],
                'image' => 'tuesday-lunch-box.jpeg',
            ],
            [
                'name' => 'Wednesday Loaded Fries & Juice',
                'description' => 'Loaded fries with a refreshing juice for KSh 1,000. Make Wednesdays your midweek treat.',
                'days' => ['wednesday'],
                'image' => 'wednesday-loaded-fries.jpeg',
            ],
        ];

        foreach ($offers as $offerData) {
            $offer = Offer::firstOrNew(['slug' => Str::slug($offerData['name'])]);
            $offer->fill([
                'name' => $offerData['name'],
                'description' => $offerData['description'],
                'amount' => 0,
                'status' => Status::ACTIVE,
                'start_date' => Carbon::now()->startOfDay(),
                'end_date' => Carbon::now()->addYear()->endOfDay(),
                'visible_days' => $offerData['days'],
            ]);
            $offer->save();

            $imagePath = public_path('images/offers/' . $offerData['image']);
            if (file_exists($imagePath) && !$offer->hasMedia('offer')) {
                $offer->addMedia($imagePath)
                    ->preservingOriginal()
                    ->toMediaCollection('offer');
            }
        }
    }
}
