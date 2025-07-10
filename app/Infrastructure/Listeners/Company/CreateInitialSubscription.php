<?php

namespace App\Infrastructure\Listeners\Company;

use App\Domain\Events\Company\CompanyCreated;
use App\Domain\Company\Subscription;

use Illuminate\Support\Str;
use Carbon\Carbon;

class CreateInitialSubscription
{
    public function __construct()
    {}

    public function handle(CompanyCreated $event): void
    {
        // Crear la suscripción inicial
        $subscription = new Subscription();
        $subscription->company_id = $event->company->id;
        $subscription->plan_id = $event->planId->getValue();
        $subscription->is_active = true;
        $subscription->starts_at = Carbon::now();
        $subscription->ends_at = Carbon::now()->addMonth();

        $subscription->save();
    }
} 