<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\FrontendOrder;
use App\Models\Message;
use App\Models\Order;
use App\Policies\AddressPolicy;
use App\Policies\FrontendOrderPolicy;
use App\Policies\MessagePolicy;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Address::class       => AddressPolicy::class,
        FrontendOrder::class => FrontendOrderPolicy::class,
        Message::class       => MessagePolicy::class,
        Order::class         => OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
