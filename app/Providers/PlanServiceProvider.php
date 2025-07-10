<?php

namespace App\Providers;

use App\Domain\Plan\PlanRepositoryInterface;
use App\Infrastructure\Plan\PlanRepository;
use Illuminate\Support\ServiceProvider;

class PlanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
    }

    public function boot(): void
    {
        //
    }
} 