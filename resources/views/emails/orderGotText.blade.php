@php
    $isPaid = (int) $order->payment_status === \App\Enums\PaymentStatus::PAID;
    $orderLink = $isPaid && $order->transaction
        ? \Illuminate\Support\Facades\URL::temporarySignedRoute('payment.receipt.preview', now()->addDays(30), ['order' => $order])
        : url('/admin/online-orders/show/' . $order->id);
@endphp
{{ $isPaid ? 'PAID ORDER ALERT' : 'NEW ORDER ALERT' }}

Order #{{ $order->order_serial_no }} requires your attention.
Branch: {{ $order->branch?->name ?? config('app.name') }}
Customer: {{ $order->user?->name ?? 'Guest' }}
Customer phone: {{ $order->user?->phone ?? 'Not provided' }}
Order type: {{ (int) $order->order_type === \App\Enums\OrderType::TAKEAWAY ? 'Pickup' : 'Delivery' }}
Payment status: {{ $isPaid ? 'Paid' : 'Pending / Pay on delivery' }}
Payment method: {{ $order->transaction?->payment_method ?? ((int) $order->payment_method === 1 ? 'Cash on delivery' : 'Online payment') }}
@if((int) $order->order_type !== \App\Enums\OrderType::TAKEAWAY)
Delivery address: {{ $order->address?->label ? $order->address->label . ': ' : '' }}{{ $order->address?->address ?? 'Not provided' }}{{ $order->address?->apartment ? ', ' . $order->address->apartment : '' }}
@if($order->address?->latitude && $order->address?->longitude)GPS: {{ $order->address->latitude }}, {{ $order->address->longitude }}@endif
@endif
Items:
@foreach($order->orderItems as $orderItem)
- {{ $orderItem->quantity }} x {{ $orderItem->orderItem?->name ?? 'Item' }}: KSh {{ number_format((float) $orderItem->total_price, 2) }}
@endforeach
Total: KSh {{ number_format((float) $order->total, 2) }}
@if($order->transaction)
Payment Gateway: {{ ucfirst($order->transaction->payment_method) }}{{ !blank($order->transaction->provider_name) ? ' (' . $order->transaction->provider_name . ')' : '' }}
Transaction Ref: {{ $order->transaction->transaction_no }}
Payment date/time: {{ \App\Libraries\AppLibrary::datetime($order->transaction->created_at, 'd M Y, h:i A') }}
@endif

Open order: {{ $orderLink }}
