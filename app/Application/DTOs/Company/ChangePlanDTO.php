<?php

namespace App\Application\DTOs\Company;

use App\ValueObjects\Company\CompanyId;
use App\ValueObjects\Plan\PlanId;

class ChangePlanDTO
{
    public function __construct(
        public readonly CompanyId $companyId,
        public readonly PlanId $planId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            companyId: new CompanyId($data['company_id']),
            planId: new PlanId($data['plan_id'])
        );
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->companyId->getValue(),
            'plan_id' => $this->planId->getValue()
        ];
    }
} 