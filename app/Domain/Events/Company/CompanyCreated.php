<?php

namespace App\Domain\Events\Company;

use App\Domain\Company\Company;
use App\ValueObjects\Plan\PlanId;

class CompanyCreated
{
    public function __construct(
        public readonly Company $company,
        public readonly PlanId $planId
    ) {}
} 