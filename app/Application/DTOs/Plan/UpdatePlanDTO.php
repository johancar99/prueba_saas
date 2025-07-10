<?php

namespace App\Application\DTOs\Plan;

use App\ValueObjects\Plan\PlanName;
use App\ValueObjects\Plan\MonthlyPrice;
use App\ValueObjects\Plan\UserLimit;
use App\ValueObjects\Plan\Features;

class UpdatePlanDTO
{
    public function __construct(
        public readonly ?PlanName $name = null,
        public readonly ?MonthlyPrice $monthlyPrice = null,
        public readonly ?UserLimit $userLimit = null,
        public readonly ?Features $features = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['name']) ? new PlanName($data['name']) : null,
            isset($data['monthly_price']) ? new MonthlyPrice((float) $data['monthly_price']) : null,
            isset($data['user_limit']) ? new UserLimit((int) $data['user_limit']) : null,
            isset($data['features']) ? new Features($data['features']) : null
        );
    }

    public function toArray(): array
    {
        $array = [];
        
        if ($this->name !== null) {
            $array['name'] = $this->name->getValue();
        }
        
        if ($this->monthlyPrice !== null) {
            $array['monthly_price'] = $this->monthlyPrice->getValue();
        }
        
        if ($this->userLimit !== null) {
            $array['user_limit'] = $this->userLimit->getValue();
        }
        
        if ($this->features !== null) {
            $array['features'] = $this->features->getFeatures();
        }
        
        return $array;
    }

    public function hasChanges(): bool
    {
        return $this->name !== null || 
               $this->monthlyPrice !== null || 
               $this->userLimit !== null || 
               $this->features !== null;
    }
} 