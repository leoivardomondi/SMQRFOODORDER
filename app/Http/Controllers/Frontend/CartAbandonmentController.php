<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Mail\CartAbandonmentAlertMail;
use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CartAbandonmentController extends Controller
{
    public function sendAlert(Request $request)
    {
        try {
            $customerName = trim($request->input('customer_name', ''));
            $customerPhone = trim($request->input('customer_phone', ''));
            $customerEmail = trim($request->input('customer_email', ''));
            $branchId = $request->input('branch_id');
            $cartItems = $request->input('cart_items', []);
            $total = (float) $request->input('total', 0);

            // 1. MUST have customer_name and customer_phone
            if (empty($customerName) || empty($customerPhone) || empty($cartItems)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer name, phone number, and cart items are required.'
                ], 422);
            }

            // 2. Prevent duplicate alerts within 30 minutes for the same phone number
            $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
            $cacheKey = "cart_abandonment_{$cleanPhone}_{$branchId}";

            if (Cache::has($cacheKey)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Cart abandonment alert already sent recently.'
                ]);
            }

            // 3. Resolve Branch, Super Admin, and Company Email Addresses
            $branch = Branch::find($branchId);
            $recipients = [];

            // Add Branch Email
            if (!empty($branch?->email)) {
                $recipients[] = trim($branch->email);
            }

            // Add Super Admin Emails (Admins with role ADMIN)
            try {
                $superAdminEmails = User::role(Role::ADMIN)->whereNotNull('email')->pluck('email')->toArray();
                foreach ($superAdminEmails as $adminEmail) {
                    if (!empty($adminEmail)) {
                        $recipients[] = trim($adminEmail);
                    }
                }
            } catch (\Exception $e) {
                Log::info('Error fetching Super Admin emails for cart abandonment: ' . $e->getMessage());
            }

            // Add Company Email
            $company = Company::first();
            if (!empty($company?->email)) {
                $recipients[] = trim($company->email);
            }

            // Clean & Deduplicate recipient email array
            $recipients = array_values(array_unique(array_filter($recipients)));

            if (empty($recipients)) {
                $fallback = config('mail.from.address');
                if ($fallback) {
                    $recipients = [$fallback];
                }
            }

            if (!empty($recipients)) {
                $primaryRecipient = array_shift($recipients);
                $mailable = new CartAbandonmentAlertMail(
                    $customerName,
                    $customerPhone,
                    $customerEmail,
                    $branch,
                    $cartItems,
                    $total
                );

                if (count($recipients) > 0) {
                    Mail::to($primaryRecipient)->cc($recipients)->send($mailable);
                } else {
                    Mail::to($primaryRecipient)->send($mailable);
                }

                // Lock for 30 minutes to avoid spamming
                Cache::put($cacheKey, true, now()->addMinutes(30));

                return response()->json([
                    'status' => true,
                    'message' => 'Cart abandonment alert sent to branch & super admin successfully.'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'No recipient email configured for branch, super admin, or company.'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Cart Abandonment Email Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
