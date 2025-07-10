<?php

namespace App\Application\UseCases\Plan;

use App\Domain\Plan\PlanRepositoryInterface;
use App\ValueObjects\Plan\PlanId;

class DeletePlanUseCase
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

        if ($plan->isDeleted()) {
            throw new \InvalidArgumentException('Plan is already deleted');
        }

        $plan->delete();
        $this->planRepository->save($plan);
    }
} 