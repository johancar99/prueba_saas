<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Domain\Events\Company\CompanyCreated;
use App\Infrastructure\Listeners\Company\CreateInitialSubscription;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CompanyCreated::class => [
            CreateInitialSubscription::class,
        ],
    ];
} 