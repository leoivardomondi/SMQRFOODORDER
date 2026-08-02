<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abandoned Cart Alert</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f5f7; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .header { background: #dc2626; color: #ffffff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 6px 0 0 0; font-size: 14px; opacity: 0.9; }
        .content { padding: 24px; }
        .alert-box { background: #fef2f2; border-left: 4px solid #dc2626; padding: 16px; border-radius: 6px; margin-bottom: 20px; }
        .alert-box p { margin: 0; font-weight: 600; color: #991b1b; font-size: 14px; }
        .customer-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px; }
        .customer-card h3 { margin: 0 0 12px 0; font-size: 16px; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; }
        .info-row { display: flex; margin-bottom: 8px; font-size: 14px; }
        .info-label { font-weight: 700; width: 120px; color: #475569; }
        .info-value { font-weight: 600; color: #0f172a; flex: 1; }
        .whatsapp-btn { display: inline-block; background: #25d366; color: #ffffff; font-weight: 700; text-decoration: none; padding: 8px 16px; border-radius: 20px; font-size: 13px; margin-top: 8px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #f1f5f9; color: #334155; text-align: left; padding: 10px; font-size: 12px; text-transform: uppercase; }
        .items-table td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        .item-name { font-weight: 700; color: #0f172a; }
        .item-meta { font-size: 11px; color: #64748b; margin-top: 2px; }
        .total-box { background: #0f172a; color: #ffffff; padding: 16px; border-radius: 8px; text-align: right; margin-bottom: 20px; }
        .total-box span { font-size: 18px; font-weight: 800; color: #eab308; }
        .footer { text-align: center; padding: 16px; background: #f8fafc; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>🚨 Abandoned Cart Alert</h1>
        <p>{{ $branch ? $branch->name : 'Branch Staff' }} - Immediate Customer Action Needed</p>
    </div>

    <div class="content">
        <!-- Alert Banner -->
        <div class="alert-box">
            <p>A customer started a checkout order at {{ $branch ? $branch->name : 'your branch' }} but abandoned their cart. Reach out to assist them!</p>
        </div>

        <!-- Customer Contact Details -->
        <div class="customer-card">
            <h3>👤 Customer Details</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $customerName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">
                    <a href="tel:{{ $customerPhone }}" style="color:#0284c7; text-decoration:none; font-weight:bold;">{{ $customerPhone }}</a>
                </span>
            </div>
            @if($customerEmail)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $customerEmail }}</span>
            </div>
            @endif
            @if($branch)
            <div class="info-row">
                <span class="info-label">Branch:</span>
                <span class="info-value">{{ $branch->name }} ({{ $branch->address }})</span>
            </div>
            @endif

            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
                if (strlen($cleanPhone) === 9 && str_starts_with($cleanPhone, '7')) {
                    $cleanPhone = '254' . $cleanPhone;
                } elseif (strlen($cleanPhone) === 10 && str_starts_with($cleanPhone, '07')) {
                    $cleanPhone = '254' . substr($cleanPhone, 1);
                }
            @endphp
            <div style="margin-top:12px;">
                <a href="https://wa.me/{{ $cleanPhone }}?text=Hi%20{{ urlencode($customerName) }},%20we%20noticed%20you%20were%20ordering%20from%20{{ urlencode($branch ? $branch->name : 'Bwibo') }}!%20Can%20we%20help%20you%20complete%20your%20order?" target="_blank" class="whatsapp-btn">
                    💬 Contact via WhatsApp
                </a>
            </div>
        </div>

        <!-- Cart Items -->
        <h3 style="font-size: 15px; color: #0f172a; margin-bottom: 10px;">🛒 Abandoned Items</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item['name'] ?? $item['item_name'] ?? 'Item' }}</div>
                        @if(!empty($item['instruction']))
                            <div class="item-meta">Note: {{ $item['instruction'] }}</div>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $item['quantity'] ?? 1 }}</td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ isset($item['currency_price']) ? $item['currency_price'] : (isset($item['total']) ? 'KES '.$item['total'] : 'N/A') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Box -->
        <div class="total-box">
            Estimated Abandoned Cart Total: <span>KES {{ number_format($total, 2) }}</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Automated Cart Recovery Alert System &bull; {{ date('Y-m-d H:i:s') }}
    </div>
</div>

</body>
</html>
