<?php

namespace App\Domain\Plan;

use App\ValueObjects\Plan\PlanId;
use App\ValueObjects\Plan\PlanName;
use App\ValueObjects\Plan\MonthlyPrice;
use App\ValueObjects\Plan\UserLimit;
use App\ValueObjects\Plan\Features;

class Plan
{
    private PlanId $id;
    private PlanName $name;
    private MonthlyPrice $monthlyPrice;
    private UserLimit $userLimit;
    private Features $features;
    private bool $isActive;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;
    private ?\DateTimeImmutable $deletedAt;

    public function __construct(
        PlanId $id,
        PlanName $name,
        MonthlyPrice $monthlyPrice,
        UserLimit $userLimit,
        Features $features,
        bool $isActive = true,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
        ?\DateTimeImmutable $deletedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->monthlyPrice = $monthlyPrice;
        $this->userLimit = $userLimit;
        $this->features = $features;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
        $this->deletedAt = $deletedAt;
    }

    public static function create(
        PlanName $name,
        MonthlyPrice $monthlyPrice,
        UserLimit $userLimit,
        Features $features
    ): self {
        return new self(
            new PlanId(0), // ID temporal que será reemplazado por la BD
            $name,
            $monthlyPrice,
            $userLimit,
            $features
        );
    }

    public function getId(): PlanId
    {
        return $this->id;
    }

    public function getName(): PlanName
    {
        return $this->name;
    }

    public function getMonthlyPrice(): MonthlyPrice
    {
        return $this->monthlyPrice;
    }

    public function getUserLimit(): UserLimit
    {
        return $this->userLimit;
    }

    public function getFeatures(): Features
    {
        return $this->features;
    }

    public function isActive(): bool
    {
        return $this->isActive;
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

    public function updateName(PlanName $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateMonthlyPrice(MonthlyPrice $monthlyPrice): void
    {
        $this->monthlyPrice = $monthlyPrice;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateUserLimit(UserLimit $userLimit): void
    {
        $this->userLimit = $userLimit;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateFeatures(Features $features): void
    {
        $this->features = $features;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
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

    public function hasFeature(string $feature): bool
    {
        return $this->features->hasFeature($feature);
    }

    public function getAnnualPrice(): float
    {
        return $this->monthlyPrice->getValue() * 12;
    }
} 