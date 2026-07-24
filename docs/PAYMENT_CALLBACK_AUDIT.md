# Payment callback audit

All callbacks pass through `PaymentController`, which rejects disabled or unverified gateways and prevents a second unpaid-to-paid transition after an order has already been completed. Payment entry and result pages require a temporary signed URL, order ownership, or the payment session established by a signed URL.

The fail-closed allowlist is `config/payments.php`. Only credit, Mollie, Razorpay, and Stripe are exposed until the remaining integrations receive order-bound server-side verification and sandbox tests. The standalone SenangPay webhook route was removed.

Provider callbacks must retain their provider-side verification before `PaymentService::payment` is called:

- Mollie retrieves the order-bound provider payment and requires paid status.
- Razorpay re-fetches the captured payment, validates its amount, and transaction identifiers are globally unique per gateway.
- Stripe and credit use one-time, order-bound server-side capture records. Stripe remains a legacy Charges integration and should migrate to Checkout Sessions.
- bKash, Cashfree, Flutterwave, Iyzico, Mercado Pago, Midtrans, PayPal, Paystack, Paytm, Pesapal, PhonePe, SSLCommerz, Telr, PayFast, SenangPay, and Skrill are fail-closed because their current callbacks do not consistently bind provider status, amount, currency, and provider order identity to the local order.

Every gateway must be tested in its sandbox after deployment. A browser redirect alone must never be accepted as proof of payment.
