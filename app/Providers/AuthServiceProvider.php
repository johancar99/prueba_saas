<?php

namespace App\Providers;

use App\Domain\Auth\AuthServiceInterface;
use App\Infrastructure\Auth\AuthService;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }

    public function boot(): void
    {
        //
    }
} 