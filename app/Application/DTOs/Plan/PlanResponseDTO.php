<?php

namespace App\Application\DTOs\Plan;

use App\Domain\Plan\Plan as PlanEntity;
use App\ValueObjects\Plan\PlanId;

class PlanResponseDTO
{
    public function __construct(
        public readonly PlanId $id,
        public readonly string $name,
        public readonly float $monthlyPrice,
        public readonly int $userLimit,
        public readonly array $features,
        public readonly bool $isActive,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {}

    public static function fromEntity(PlanEntity $plan): self
    {
        return new self(
            $plan->getId(),
            $plan->getName()->getValue(),
            $plan->getMonthlyPrice()->getValue(),
            $plan->getUserLimit()->getValue(),
            $plan->getFeatures()->getFeatures(),
            $plan->isActive(),
            $plan->getCreatedAt()->format('Y-m-d H:i:s'),
            $plan->getUpdatedAt()->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'monthly_price' => $this->monthlyPrice,
            'annual_price' => $this->monthlyPrice * 12,
            'user_limit' => $this->userLimit,
            'user_limit_display' => $this->userLimit === -1 ? 'Unlimited' : (string) $this->userLimit,
            'features' => $this->features,
            'features_count' => count($this->features),
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
} 