<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 0; color: #333; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1AB759 0%, #0d8a3f 100%); padding: 30px 20px; text-align: center; color: #ffffff; }
        .header img { max-height: 50px; margin-bottom: 12px; }
        .header h1 { font-size: 22px; margin: 0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .content { padding: 25px 20px; }
        .intro { font-size: 15px; line-height: 1.6; color: #555; text-align: center; margin-bottom: 25px; }
        .item-card { display: flex; align-items: center; border: 1px solid #eef2f5; border-radius: 10px; padding: 15px; margin-bottom: 15px; background: #fafbfc; }
        .item-img { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; margin-right: 15px; }
        .item-details { flex: 1; }
        .item-name { font-size: 16px; font-weight: 700; color: #111; margin: 0 0 5px 0; }
        .item-desc { font-size: 13px; color: #666; margin: 0 0 8px 0; line-height: 1.4; }
        .price-badge { font-size: 15px; font-weight: 800; color: #1AB759; }
        .compare-price { font-size: 13px; color: #999; text-decoration: line-through; margin-left: 8px; }
        .discount-tag { display: inline-block; background-color: #ffebe9; color: #e53935; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 12px; margin-left: 8px; }
        .cta-container { text-align: center; margin-top: 30px; margin-bottom: 10px; }
        .cta-btn { display: inline-block; background-color: #1AB759; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 30px; font-size: 16px; font-weight: 700; box-shadow: 0 4px 10px rgba(26,183,89,0.3); }
        .footer { background-color: #f8fafc; text-align: center; padding: 20px; font-size: 12px; color: #888; border-top: 1px solid #edf2f7; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(!empty($logoUrl))
                <img src="{{ $logoUrl }}" alt="Logo">
            @endif
            <h1>{{ $title }}</h1>
        </div>

        <div class="content">
            <p class="intro">
                @if($timeSlot === '12PM')
                    Hungry for lunch? Check out today's special promotional offers, freshly prepared and ready for delivery!
                @else
                    Treat yourself to dinner! Here are today's top deals and discounted items available now.
                @endif
            </p>

            @foreach($offerItems as $item)
                <div class="item-card">
                    <img class="item-img" src="{{ $item['cover'] ?? asset('images/default/item.png') }}" alt="{{ $item['name'] }}">
                    <div class="item-details">
                        <h2 class="item-name">{{ $item['name'] }}</h2>
                        @if(!empty($item['description']))
                            <p class="item-desc">{{ Str::limit($item['description'], 75) }}</p>
                        @endif
                        <div>
                            <span class="price-badge">{{ $item['currency_price'] }}</span>
                            @if(!empty($item['compare_at_currency_price']))
                                <span class="compare-price">{{ $item['compare_at_currency_price'] }}</span>
                                @if(!empty($item['glovo_comparison_discount_percentage']))
                                    <span class="discount-tag">{{ $item['glovo_comparison_discount_percentage'] }}% OFF</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="cta-container">
                <a href="{{ url('/') }}" class="cta-btn">Order Now & Save</a>
            </div>
        </div>

        <div class="footer">
            <p>You are receiving this daily promotional email because you are a registered customer or subscriber.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
