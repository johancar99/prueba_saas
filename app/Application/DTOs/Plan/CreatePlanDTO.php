<?php

namespace App\Application\DTOs\Plan;

use App\ValueObjects\Plan\PlanName;
use App\ValueObjects\Plan\MonthlyPrice;
use App\ValueObjects\Plan\UserLimit;
use App\ValueObjects\Plan\Features;

class CreatePlanDTO
{
    public function __construct(
        public readonly PlanName $name,
        public readonly MonthlyPrice $monthlyPrice,
        public readonly UserLimit $userLimit,
        public readonly Features $features
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            new PlanName($data['name']),
            new MonthlyPrice((float) $data['monthly_price']),
            new UserLimit((int) $data['user_limit']),
            new Features($data['features'])
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name->getValue(),
            'monthly_price' => $this->monthlyPrice->getValue(),
            'user_limit' => $this->userLimit->getValue(),
            'features' => $this->features->getFeatures(),
        ];
    }
} 