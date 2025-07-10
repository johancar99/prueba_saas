<?php

namespace App\Domain\User;

use App\ValueObjects\User\Email;
use App\ValueObjects\User\Password;
use App\ValueObjects\User\Name;
use App\ValueObjects\User\UserId;
use App\ValueObjects\User\CompanyId;

class User
{
    private UserId $id;
    private Name $name;
    private Email $email;
    private Password $password;
    private ?CompanyId $companyId;
    private ?\DateTimeImmutable $emailVerifiedAt;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;
    private ?\DateTimeImmutable $deletedAt;

    public function __construct(
        UserId $id,
        Name $name,
        Email $email,
        Password $password,
        ?CompanyId $companyId = null,
        ?\DateTimeImmutable $emailVerifiedAt = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
        ?\DateTimeImmutable $deletedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->companyId = $companyId;
        $this->emailVerifiedAt = $emailVerifiedAt;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
        $this->deletedAt = $deletedAt;
    }

    public static function create(
        Name $name,
        Email $email,
        Password $password,
        ?CompanyId $companyId = null
    ): self {
        return new self(
            UserId::generate(),
            $name,
            $email,
            $password,
            $companyId
        );
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getCompanyId(): ?CompanyId
    {
        return $this->companyId;
    }

    public function getEmailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function updateName(Name $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateEmail(Email $email): void
    {
        $this->email = $email;
        $this->emailVerifiedAt = null; // Reset verification when email changes
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updatePassword(Password $password): void
    {
        $this->password = $password;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateCompanyId(?CompanyId $companyId): void
    {
        $this->companyId = $companyId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function verifyEmail(): void
    {
        $this->emailVerifiedAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function delete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }
} 