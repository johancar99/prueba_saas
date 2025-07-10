<?php

namespace App\Application\DTOs\User;

use App\ValueObjects\User\Name;
use App\ValueObjects\User\Email;
use App\ValueObjects\User\Password;
use App\ValueObjects\User\CompanyId;

class UpdateUserDTO
{
    public function __construct(
        public readonly ?Name $name = null,
        public readonly ?Email $email = null,
        public readonly ?Password $password = null,
        public readonly ?CompanyId $companyId = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['name']) ? new Name($data['name']) : null,
            isset($data['email']) ? new Email($data['email']) : null,
            isset($data['password']) ? new Password($data['password']) : null,
            isset($data['company_id']) ? new CompanyId($data['company_id']) : null
        );
    }

    public function toArray(): array
    {
        $array = [];
        
        if ($this->name !== null) {
            $array['name'] = $this->name->getValue();
        }
        
        if ($this->email !== null) {
            $array['email'] = $this->email->getValue();
        }
        
        if ($this->password !== null) {
            $array['password'] = $this->password->getValue();
        }
        
        if ($this->companyId !== null) {
            $array['company_id'] = $this->companyId->getValue();
        }
        
        return $array;
    }

    public function hasChanges(): bool
    {
        return $this->name !== null || $this->email !== null || $this->password !== null || $this->companyId !== null;
    }
} 