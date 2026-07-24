@php
    $paystackKey = "";
    $paystackReference = 'order-' . $order->id . '-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10));
    if(!blank($paymentGateways)) {
        foreach($paymentGateways as $paymentGateway) {
            if($paymentGateway->slug === 'paystack') {
                $paymentGatewayOption = $paymentGateway->gatewayOptions->pluck('value', 'option');
                $paystackKey = $paymentGatewayOption['paystack_public_key'] ?? "";
            }
        }
    }
    $email = $order?->user?->email ?? $order->email ?? 'bwibomarketing@gmail.com';
@endphp

<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
    window.paystack_payment = function() {
        const paystackKey = '{{ $paystackKey }}';
        const paystackTotalAmount = parseFloat('{{ $order->total }}');
        const paystackCurrencyCode = '{{ $currency->code }}';
        const paystackUserEmail = '{{ $email }}';
        const paystackReference = '{{ $paystackReference }}';
        const paystackSuccessLink = '{{ route('payment.success', ['paymentGateway' => 'paystack', 'order' => $order]) }}';
        const paystackCancelLink = '{{ route('payment.index', ['order' => $order]) }}';

        if (!paystackKey) {
            alert('Paystack public key is not configured.');
            return;
        }

        let handler = PaystackPop.setup({
            key: paystackKey,
            email: paystackUserEmail,
            amount: Math.round(paystackTotalAmount * 100),
            currency: paystackCurrencyCode,
            ref: paystackReference,
            callback: function(response) {
                // Redirect to success URL with reference
                window.location.href = paystackSuccessLink + '?reference=' + response.reference;
            },
            onClose: function() {
                // User closed the iframe
                window.location.href = paystackCancelLink;
            }
        });

        handler.openIframe();
    };
</script>
