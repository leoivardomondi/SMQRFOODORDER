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
Total: KSh {{ number_format((float) $order->total, 2) }}
@if($isPaid && $order->transaction)
Payment Gateway: {{ ucfirst($order->transaction->payment_method) }}{{ !blank($order->transaction->provider_name) ? ' (' . $order->transaction->provider_name . ')' : '' }}
Transaction Ref: {{ $order->transaction->transaction_no }}
Payment date/time: {{ \App\Libraries\AppLibrary::datetime($order->transaction->created_at, 'd M Y, h:i A') }}
@endif

Open order: {{ $orderLink }}
