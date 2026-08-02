<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\CartAbandonmentAlertMail;
use App\Models\Branch;
use App\Models\Company;
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

            // 3. Resolve Branch and Target Email Address
            $branch = Branch::find($branchId);
            $targetEmail = $branch?->email;

            if (empty($targetEmail)) {
                $company = Company::first();
                $targetEmail = $company?->email ?? config('mail.from.address');
            }

            if (!empty($targetEmail)) {
                Mail::to($targetEmail)->send(new CartAbandonmentAlertMail(
                    $customerName,
                    $customerPhone,
                    $customerEmail,
                    $branch,
                    $cartItems,
                    $total
                ));

                // Lock for 30 minutes to avoid spamming the branch
                Cache::put($cacheKey, true, now()->addMinutes(30));

                return response()->json([
                    'status' => true,
                    'message' => 'Cart abandonment alert sent to branch successfully.'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'No recipient email configured for branch or company.'
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
