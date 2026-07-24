# Dependency upgrade blocker

The current verified runtime is Laravel 10.50.2 on PHP 8.2. Composer's advisory audit reports current Laravel framework advisories and two Media Library 10 advisories. The application-level mitigations in this change reduce exposure, but they do not replace patched dependencies.

A clean dependency graph was resolved for PHP 8.3, Laravel 12.63, Sanctum 4, PHPUnit 11, Media Library 11.23.2, Mollie 4.1, Settings 3.0, and Countries 1.0.2. Installing it requires removing the legacy packages for gateways that are already fail-closed in `config/payments.php`.

The installation could not be committed because external tool execution reached its usage limit. Do not deploy the intermediate Laravel 12 manifest without its matching `composer.lock` and vendor tree.

Required follow-up:

1. Provision PHP 8.3 with HTTP, GD, ZIP, EXIF, JSON, and PDO extensions.
2. Remove packages for the disabled legacy gateways from `composer.json`.
3. Upgrade to Laravel `^12.61.1` and Media Library `^11.23` or newer.
4. Run `composer update --with-all-dependencies` and `composer audit`.
5. Run the full test suite and sandbox tests for the four enabled payment gateways.

Until that work is completed, treat the current dependency audit as an open deployment blocker.
