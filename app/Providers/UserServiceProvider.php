<?php

namespace App\Providers;

use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\User\UserRepository;
use App\Domain\Company\CompanyRepositoryInterface;
use App\Infrastructure\Company\CompanyRepository;
use App\Application\UseCases\User\CreateUserUseCase;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);

        $this->app->bind(CreateUserUseCase::class, function ($app) {
            return new CreateUserUseCase(
                $app->make(UserRepositoryInterface::class),
                $app->make(CompanyRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
} 