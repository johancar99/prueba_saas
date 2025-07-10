<?php

namespace App\Application\UseCases\Auth;

use App\Domain\Auth\AuthServiceInterface;
use App\ValueObjects\Auth\Token;

class LogoutAllTokensUseCase
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    public function execute(Token $token): void
    {
        $this->authService->logoutAllTokens($token);
    }
} 