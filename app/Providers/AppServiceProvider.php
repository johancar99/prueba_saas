<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\User\UserRepository;
use App\Domain\Auth\AuthServiceInterface;
use App\Infrastructure\Auth\AuthService;
use App\Domain\Plan\PlanRepositoryInterface;
use App\Infrastructure\Plan\PlanRepository;
use App\Domain\Company\CompanyRepositoryInterface;
use App\Infrastructure\Company\CompanyRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // User Module
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        
        // Auth Module
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        
        // Plan Module
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        
        // Company Module
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
