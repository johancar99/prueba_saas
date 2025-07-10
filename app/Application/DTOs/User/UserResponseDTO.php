<?php

namespace App\Application\DTOs\User;

use App\Domain\User\User as UserEntity;
use App\ValueObjects\User\UserId;

class UserResponseDTO
{
    public function __construct(
        public readonly UserId $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?int $companyId,
        public readonly ?string $emailVerifiedAt,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {}

    public static function fromEntity(UserEntity $user): self
    {
        return new self(
            $user->getId(),
            $user->getName()->getValue(),
            $user->getEmail()->getValue(),
            $user->getCompanyId()?->getValue(),
            $user->getEmailVerifiedAt()?->format('Y-m-d H:i:s'),
            $user->getCreatedAt()->format('Y-m-d H:i:s'),
            $user->getUpdatedAt()->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'email' => $this->email,
            'company_id' => $this->companyId,
            'email_verified_at' => $this->emailVerifiedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
} 