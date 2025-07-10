<?php

namespace App\Application\DTOs\User;

use App\ValueObjects\User\Name;
use App\ValueObjects\User\Email;
use App\ValueObjects\User\Password;
use App\ValueObjects\User\CompanyId;

class CreateUserDTO
{
    public function __construct(
        public readonly Name $name,
        public readonly Email $email,
        public readonly Password $password,
        public readonly ?CompanyId $companyId = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            new Name($data['name']),
            new Email($data['email']),
            new Password($data['password']),
            isset($data['company_id']) ? new CompanyId($data['company_id']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name->getValue(),
            'email' => $this->email->getValue(),
            'password' => $this->password->getValue(),
            'company_id' => $this->companyId?->getValue(),
        ];
    }
} 