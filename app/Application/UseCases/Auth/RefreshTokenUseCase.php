<?php

namespace App\Application\UseCases\Auth;

use App\Domain\Auth\AuthServiceInterface;
use App\Application\DTOs\Auth\AuthResponseDTO;
use App\ValueObjects\Auth\Token;

class RefreshTokenUseCase
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    public function execute(Token $token): AuthResponseDTO
    {
        $newToken = $this->authService->refresh($token);

        return new AuthResponseDTO(
            $newToken,
            // En un caso real, obtendrías el userId del token actual
            new \App\ValueObjects\User\UserId(1), // Placeholder
            '', // Placeholder
            '' // Placeholder
        );
    }
} 