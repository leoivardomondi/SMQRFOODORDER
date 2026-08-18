<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paid Order #{{ $order->order_serial_no }} - {{ $company['company_name'] ?? 'Restaurant' }}</title>
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="Paid Order #{{ $order->order_serial_no }} - {{ $company['company_name'] ?? 'Restaurant' }}">
    <meta property="og:description" content="Payment confirmed. Open the receipt to review this paid order.">
    @if($hasImage)
        <meta property="og:image" content="{{ $imageUrl }}">
        <meta property="og:image:secure_url" content="{{ $imageUrl }}">
        <meta property="og:image:type" content="image/png">
        <meta property="og:image:width" content="720">
        <meta property="og:image:alt" content="Paid order receipt #{{ $order->order_serial_no }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px 16px; background: {{ $theme['theme_page_background'] ?? '#f7f7fc' }}; color: {{ $theme['theme_heading_color'] ?? '#1f1f39' }}; font-family: Arial, sans-serif; }
        main { width: min(100%, 760px); margin: 0 auto; text-align: center; }
        h1 { color: {{ $theme['theme_primary_color'] ?? '#0f766e' }}; font-size: 24px; }
        img { display: block; width: 100%; height: auto; margin: 20px auto; border: 1px solid {{ $theme['theme_primary_color'] ?? '#0f766e' }}; border-radius: {{ $theme['theme_border_radius'] ?? '12px' }}; }
        a { display: inline-block; padding: 13px 24px; border-radius: {{ $theme['theme_border_radius'] ?? '12px' }}; background: {{ $theme['theme_primary_color'] ?? '#0f766e' }}; color: {{ $theme['theme_button_text_color'] ?? '#ffffff' }}; font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>
<main>
    <h1>Paid Order #{{ $order->order_serial_no }}</h1>
    <p>Payment confirmed. Review the stored receipt below.</p>
    @if($hasImage)
        <img src="{{ $imageUrl }}" alt="Paid order receipt #{{ $order->order_serial_no }}">
    @else
        <p>The visual receipt is being prepared. The confirmed order is available in Admin.</p>
    @endif
    <a href="{{ $adminOrderUrl }}">Open Order in Admin</a>
</main>
</body>
</html>
