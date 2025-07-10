<?php

namespace App\Application\UseCases\Plan;

use App\Domain\Plan\PlanRepositoryInterface;
use App\Application\DTOs\Plan\PlanResponseDTO;
use App\ValueObjects\Plan\PlanId;

class GetPlanUseCase
{
    public function __construct(
        private PlanRepositoryInterface $planRepository
    ) {}

    public function execute(PlanId $planId): PlanResponseDTO
    {
        $plan = $this->planRepository->findById($planId);
        
        if ($plan === null) {
            throw new \InvalidArgumentException('Plan not found');
        }

        return PlanResponseDTO::fromEntity($plan);
    }
} 