# Security and operations

## Credential rotation required

The Firebase service-account key formerly stored below `public/file` must be treated as compromised. Revocation cannot be performed from this source tree.

1. Disable and delete the exposed key in Google Cloud IAM.
2. Create a least-privilege replacement service account/key.
3. Upload/store it on the private media disk outside the web root.
4. Rotate the database, mail, SMS, payment, AWS, Google Maps, API, and application keys from the production deployment copy.
5. Invalidate active sessions and Sanctum tokens after changing `APP_KEY`.
6. Purge old deployment archives, backups, CI artifacts, and server logs containing secrets.

Never commit production `.env` files or service-account JSON files.

## Queue worker

Production uses the database queue. After migrating, supervise at least one worker:

    php artisan queue:work database --sleep=1 --tries=3 --timeout=120

Restart workers after each deployment with `php artisan queue:restart`.

## Payments

Payment entry links are temporary signed URLs. Gateway callbacks must verify payment status and amount with the provider before recording a transaction, and transaction writes must remain idempotent. New Stripe work should use Checkout Sessions rather than the legacy Charges API.
