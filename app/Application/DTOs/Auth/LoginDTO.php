<?php

namespace App\Application\DTOs\Auth;

use App\ValueObjects\User\Email;
use App\ValueObjects\User\Password;

class LoginDTO
{
    public function __construct(
        public readonly Email $email,
        public readonly Password $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            new Email($data['email']),
            new Password($data['password'])
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email->getValue(),
            'password' => $this->password->getValue(),
        ];
    }
} 