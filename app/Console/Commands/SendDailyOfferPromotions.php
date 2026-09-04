<?php

namespace App\Console\Commands;

use App\Enums\Ask;
use App\Enums\Role;
use App\Enums\Status;
use App\Http\Resources\ItemResource;
use App\Mail\DailyOfferPromotionMail;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Subscriber;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyOfferPromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-offer-promotions {--slot= : Optional time slot (12PM or 7PM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send promotional emails highlighting today\'s offer items at 12PM and 7PM.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $currentHour = (int) date('H');
            $timeSlot    = $this->option('slot') ?: ($currentHour < 15 ? '12PM' : '7PM');

            $today = Carbon::today();
            $now   = Carbon::now();

            // 1. Fetch active offer items for today
            // Active Offer records
            $activeOffers = Offer::where('status', Status::ACTIVE)
                ->where(function ($q) use ($now) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                })
                ->with('items')
                ->get();

            $offerItemIds = [];
            foreach ($activeOffers as $offer) {
                foreach ($offer->items as $item) {
                    $offerItemIds[] = $item->id;
                }
            }

            // Also include items with active compare_at_price > price or featured offer items
            $items = Item::with('media', 'category', 'tax', 'offer')
                ->where('status', Status::ACTIVE)
                ->where(function ($query) use ($offerItemIds) {
                    if (!empty($offerItemIds)) {
                        $query->whereIn('id', $offerItemIds);
                    }
                    $query->orWhere('is_featured', Ask::YES)
                          ->orWhereColumn('compare_at_price', '>', 'price');
                })
                ->limit(10)
                ->get();

            if ($items->isEmpty()) {
                $this->info('No active offer items found for today. Skipping promotional email dispatch.');
                return Command::SUCCESS;
            }

            // Convert items to structured array for email template
            $formattedItems = ItemResource::collection($items)->resolve();

            // 2. Fetch target recipients (Customers & Subscribers)
            $customerEmails = User::role(Role::CUSTOMER)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->pluck('email')
                ->toArray();

            $subscriberEmails = Subscriber::whereNotNull('email')
                ->pluck('email')
                ->toArray();

            $recipients = array_values(array_unique(array_filter(array_merge($customerEmails, $subscriberEmails))));

            if (empty($recipients)) {
                $this->info('No customer or subscriber emails found.');
                return Command::SUCCESS;
            }

            $this->info("Sending {$timeSlot} promotional offer email to " . count($recipients) . " recipients for " . count($items) . " offer item(s)...");

            foreach ($recipients as $email) {
                try {
                    Mail::to($email)->send(new DailyOfferPromotionMail($formattedItems, $timeSlot));
                } catch (Exception $mailException) {
                    Log::warning("Failed sending daily offer promotion email to {$email}: " . $mailException->getMessage());
                }
            }

            Log::info("SendDailyOfferPromotions ({$timeSlot}): Sent to " . count($recipients) . " recipients.");
            $this->info('Promotional emails dispatched successfully.');

            // 3. Send PWA Push Notifications to installed PWA customers / registered device tokens
            $customerPushUsers = User::where(function ($q) {
                    $q->whereNotNull('web_token')->orWhereNotNull('device_token');
                })
                ->get();

            $fcmTokens = [];
            foreach ($customerPushUsers as $user) {
                if (!empty($user->web_token)) {
                    $fcmTokens[] = $user->web_token;
                }
                if (!empty($user->device_token)) {
                    $fcmTokens[] = $user->device_token;
                }
            }
            $fcmTokens = array_values(array_unique(array_filter($fcmTokens)));

            if (!empty($fcmTokens)) {
                $pushTitle = $timeSlot === '12PM' 
                    ? "☀️ Lunchtime Offer Alert!" 
                    : "🌙 Evening Special Deal Alert!";

                $firstItemName = $items->first()?->name ?? 'special offers';
                $itemCount = $items->count();

                $pushDescription = "Hot deals available now! Save on {$firstItemName} and {$itemCount} other item(s). Tap to view & order!";

                $pushNotification = (object)[
                    'title'       => $pushTitle,
                    'description' => $pushDescription,
                    'image'       => $items->first()?->thumb ?? null,
                ];

                $firebase = new \App\Services\FirebaseService();
                $firebase->sendNotification($pushNotification, $fcmTokens, "daily-offer-promotion");
                $this->info("PWA Push notification dispatched to " . count($fcmTokens) . " installed PWA device token(s).");
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            Log::error('SendDailyOfferPromotions Exception: ' . $e->getMessage());
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
