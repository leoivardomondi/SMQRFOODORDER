@php
    $isPaid = (int) $order->payment_status === \App\Enums\PaymentStatus::PAID;
    $orderLink = $isPaid && $order->transaction
        ? \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'payment.receipt.preview',
            now()->addDays(30),
            ['order' => $order]
        )
        : url('/admin/online-orders/show/' . $order->id);
    $orderType = (int) $order->order_type === \App\Enums\OrderType::TAKEAWAY ? 'Pickup' : 'Delivery';
    $paymentStatus = $isPaid ? 'Paid' : 'Pending / Pay on delivery';
    $address = $order->address;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isPaid ? 'Paid Order Alert' : 'New Order Alert' }}</title>
    <style>
        @media only screen and (max-width: 620px) {
            .email-shell { width: 100% !important; }
            .email-padding { padding-left: 20px !important; padding-right: 20px !important; }
            .summary-label, .summary-value { display: block !important; width: 100% !important; text-align: left !important; }
            .summary-value { padding-top: 3px !important; }
            .action-button { display: block !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background:#f2f2f2; color:#171717; font-family:Arial, Helvetica, sans-serif;">
<div style="display:none; max-height:0; overflow:hidden; opacity:0;">
    {{ $isPaid ? 'Payment confirmed' : 'New order received' }} for order #{{ $order->order_serial_no }}.
</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f2f2f2;">
    <tr>
        <td align="center" style="padding:28px 12px;">
            <table role="presentation" class="email-shell" width="600" cellspacing="0" cellpadding="0" border="0" style="width:600px; max-width:600px; background:#ffffff; border:1px solid #ded7c1; border-radius:14px; overflow:hidden;">
                <tr>
                    <td align="center" style="background:#080808; border-top:8px solid #c7ad5b; padding:26px 24px 22px;">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" width="96" alt="Bwibo Restaurant" style="display:block; width:96px; max-width:96px; height:auto; margin:0 auto 14px; border:0;">
                        @endif
                        <div style="color:#ffffff; font-size:23px; line-height:30px; font-weight:700; letter-spacing:.4px;">{{ $order->branch?->name ?? config('app.name') }}</div>
                        <div style="color:#c7ad5b; font-size:13px; line-height:20px; text-transform:uppercase; letter-spacing:2px;">Order Notification</div>
                    </td>
                </tr>
                <tr>
                    <td class="email-padding" style="padding:32px 38px 12px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td>
                                    <span style="display:inline-block; padding:7px 14px; border-radius:999px; background:{{ $isPaid ? '#daf5e2' : '#fff1cc' }}; color:{{ $isPaid ? '#147a38' : '#7a5a00' }}; font-size:13px; line-height:18px; font-weight:700; letter-spacing:.5px;">
                                        {{ $isPaid ? 'PAYMENT CONFIRMED' : 'ACTION REQUIRED' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top:18px; font-size:28px; line-height:35px; font-weight:700; color:#171717;">
                                    {{ $isPaid ? 'Paid order received' : 'New order received' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top:10px; font-size:16px; line-height:25px; color:#555555;">
                                    {{ $isPaid
                                        ? 'Payment has been confirmed. Open the secure receipt to review and process the order.'
                                        : 'A new order requires your attention. Open it to review and continue processing.' }}
                                </td>
                            </tr>
                            @if(!blank($message))
                                <tr>
                                    <td style="padding-top:12px; font-size:14px; line-height:22px; color:#777777;">{{ $message }}</td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="email-padding" style="padding:18px 38px 8px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e7e1d1; border-radius:10px; border-collapse:separate; overflow:hidden;">
                            <tr style="background:#faf7ee;">
                                <td class="summary-label" width="45%" style="padding:15px 18px; border-bottom:1px solid #ece7da; color:#777777; font-size:14px;">Order number</td>
                                <td class="summary-value" width="55%" align="right" style="padding:15px 18px; border-bottom:1px solid #ece7da; color:#171717; font-size:16px; font-weight:700;">#{{ $order->order_serial_no }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#777777; font-size:14px;">Customer</td>
                                <td class="summary-value" align="right" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px; font-weight:600;">{{ $order->user?->name ?? 'Guest' }}{{ $order->user?->phone ? ' · ' . $order->user->phone : '' }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#777777; font-size:14px;">Order type</td>
                                <td class="summary-value" align="right" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px; font-weight:600;">{{ $orderType }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#777777; font-size:14px;">Payment status</td>
                                <td class="summary-value" align="right" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px; font-weight:600;">{{ $paymentStatus }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#777777; font-size:14px;">Payment method</td>
                                <td class="summary-value" align="right" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px; font-weight:600;">{{ $order->transaction?->payment_method ?? ((int) $order->payment_method === 1 ? 'Cash on delivery' : 'Online payment') }}</td>
                            </tr>
                            @if($order->transaction)
                                <tr>
                                    <td class="summary-label" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#777777; font-size:14px;">Payment gateway</td>
                                    <td class="summary-value" align="right" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px; font-weight:600;">
                                        {{ ucfirst($order->transaction->payment_method) }}{{ !blank($order->transaction->provider_name) ? ' (' . $order->transaction->provider_name . ')' : '' }}
                                    </td>
                                </tr>
                                @if(!blank($order->transaction->transaction_no))
                                    <tr>
                                        <td class="summary-label" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#777777; font-size:14px;">Transaction ref</td>
                                        <td class="summary-value" align="right" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px; font-weight:600;">
                                            {{ $order->transaction->transaction_no }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="summary-label" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#777777; font-size:14px;">Payment date</td>
                                    <td class="summary-value" align="right" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px; font-weight:600;">{{ \App\Libraries\AppLibrary::datetime($order->transaction->created_at, 'd M Y, h:i A') }}</td>
                                </tr>
                            @endif
                            @if($orderType === 'Delivery')
                                <tr style="background:#fffdf7;">
                                    <td class="summary-label" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#777777; font-size:14px; vertical-align:top;">Delivery address</td>
                                    <td class="summary-value" align="right" style="padding:13px 18px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px; font-weight:600;">
                                        {{ $address?->label ? $address->label . ': ' : '' }}{{ $address?->address ?? 'Not provided' }}{{ $address?->apartment ? ', ' . $address->apartment : '' }}
                                        @if($address?->latitude && $address?->longitude)<br><span style="font-size:12px; color:#777777; font-weight:400;">GPS: {{ $address->latitude }}, {{ $address->longitude }}</span>@endif
                                    </td>
                                </tr>
                            @endif
                            <tr style="background:#faf7ee;">
                                <td class="summary-label" style="padding:16px 18px; color:#171717; font-size:16px; font-weight:700;">Total{{ $isPaid ? ' paid' : '' }}</td>
                                <td class="summary-value" align="right" style="padding:16px 18px; color:#171717; font-size:20px; font-weight:800;">KSh {{ number_format((float) $order->total, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="email-padding" style="padding:18px 38px 8px;">
                        <div style="font-size:17px; line-height:24px; font-weight:700; color:#171717; padding-bottom:10px;">Order items</div>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e7e1d1; border-radius:10px; border-collapse:separate; overflow:hidden;">
                            @foreach($order->orderItems as $orderItem)
                                <tr>
                                    <td style="padding:11px 14px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px;">{{ $orderItem->quantity }} × {{ $orderItem->orderItem?->name ?? 'Item' }}@if($orderItem->instruction)<br><span style="color:#777777; font-size:12px;">Note: {{ $orderItem->instruction }}</span>@endif</td>
                                    <td align="right" style="padding:11px 14px; border-bottom:1px solid #ece7da; color:#171717; font-size:14px; font-weight:600;">KSh {{ number_format((float) $orderItem->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="email-padding" align="center" style="padding:24px 38px 36px;">
                        <a href="{{ $orderLink }}" class="action-button" style="display:inline-block; min-width:260px; padding:15px 24px; border-radius:999px; background:#c7ad5b; color:#080808; font-size:16px; line-height:20px; font-weight:800; text-align:center; text-decoration:none;">
                            {{ $isPaid ? 'View Paid Order Receipt' : 'Open Order in Admin' }}
                        </a>
                        <p style="margin:16px 0 0; color:#777777; font-size:12px; line-height:19px;">If the button does not open, copy this secure link:<br><a href="{{ $orderLink }}" style="color:#725d20; word-break:break-all;">{{ $orderLink }}</a></p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="background:#111111; padding:18px 24px; color:#bdbdbd; font-size:12px; line-height:18px;">
                        Automated order alert from {{ config('app.name') }}.<br>Please confirm and process the order promptly.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
