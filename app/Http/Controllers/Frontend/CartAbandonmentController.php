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

            // 3. Resolve Super Admin, Branch, and Company Email Addresses
            $branch = Branch::find($branchId);
            $recipients = [];

            // A. Super Admin Emails
            $superAdminEmails = $this->getUsersByRole(Role::SUPER_ADMIN)->whereNotNull('email')->pluck('email')->toArray();

            // B. Branch Emails (Branch model email + Branch Managers and Waiters assigned to branch)
            $branchEmails = [];
            if (!empty($branch?->email)) {
                $branchEmails[] = trim($branch->email);
            }
            if ($branchId) {
                $branchStaffEmails = User::where('branch_id', $branchId)
                    ->where(function ($q) {
                        $q->whereHas('roles', function ($rq) {
                            $rq->whereIn('name', [Role::BRANCH_MANAGER, Role::WAITER])
                              ->orWhereIn('id', [Role::BRANCH_MANAGER, Role::WAITER]);
                        });
                    })
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->toArray();
                $branchEmails = array_merge($branchEmails, $branchStaffEmails);
            }
            $branchEmails = array_values(array_unique(array_filter($branchEmails)));

            // C. Company Email
            $company = Company::first();
            $companyEmail = !empty($company?->email) ? [trim($company->email)] : [];

            // Combine all into recipients list
            $recipients = array_values(array_unique(array_filter(array_merge(
                $superAdminEmails,
                $branchEmails,
                $companyEmail
            ))));

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
                    'message' => 'Cart abandonment alert sent to Super Admin & branch successfully.'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'No recipient email configured.'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Cart Abandonment Email Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getUsersByRole($role)
    {
        try {
            return User::role($role);
        } catch (\Exception $e) {
            return User::whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role)->orWhere('id', $role);
            });
        }
    }
}
