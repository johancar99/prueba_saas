<?php

namespace App\Application\UseCases\Plan;

use App\Domain\Plan\PlanRepositoryInterface;
use App\ValueObjects\Plan\PlanId;

class RestorePlanUseCase
{
    public function __construct(
        private PlanRepositoryInterface $planRepository
    ) {}

    public function execute(PlanId $planId): void
    {
        $plan = $this->planRepository->findById($planId);
        
        if ($plan === null) {
            throw new \InvalidArgumentException('Plan not found');
        }

        if (!$plan->isDeleted()) {
            throw new \InvalidArgumentException('Plan is not deleted');
        }

        $plan->restore();
        $this->planRepository->save($plan);
    }
} 