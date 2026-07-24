<?php

return [
    /*
     * Only gateways whose callback is bound to this order and verified using
     * a server-side provider lookup or a one-time server-side token belong here.
     */
    'verified_gateways' => ['credit', 'mollie', 'paystack', 'pesapal', 'razorpay', 'stripe'],
];
