<?php

namespace App\Application\DTOs\Auth;

use App\ValueObjects\Auth\Token;
use App\ValueObjects\User\UserId;

class AuthResponseDTO
{
    public function __construct(
        public readonly Token $token,
        public readonly UserId $userId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $tokenType = 'Bearer'
    ) {}

    public function toArray(): array
    {
        return [
            'token' => $this->token->getPlainTextValue() ?? $this->token->getValue(),
            'token_type' => $this->tokenType,
            'expires_in' => 1440 * 60, // 24 hours in seconds
            'user' => [
                'id' => $this->userId->getValue(),
                'name' => $this->name,
                'email' => $this->email,
            ]
        ];
    }
} 