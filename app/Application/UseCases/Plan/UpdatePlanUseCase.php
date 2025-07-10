<?php

namespace App\Application\UseCases\Plan;

use App\Domain\Plan\Plan;
use App\Domain\Plan\PlanRepositoryInterface;
use App\Application\DTOs\Plan\UpdatePlanDTO;
use App\Application\DTOs\Plan\PlanResponseDTO;
use App\ValueObjects\Plan\PlanId;

class UpdatePlanUseCase
{
    public function __construct(
        private PlanRepositoryInterface $planRepository
    ) {}

    public function execute(PlanId $planId, UpdatePlanDTO $dto): PlanResponseDTO
    {
        $plan = $this->planRepository->findById($planId);
        
        if ($plan === null) {
            throw new \InvalidArgumentException('Plan not found');
        }

        // Aplicar cambios
        if ($dto->name !== null) {
            $plan->updateName($dto->name);
        }

        if ($dto->monthlyPrice !== null) {
            $plan->updateMonthlyPrice($dto->monthlyPrice);
        }

        if ($dto->userLimit !== null) {
            $plan->updateUserLimit($dto->userLimit);
        }

        if ($dto->features !== null) {
            $plan->updateFeatures($dto->features);
        }

        // Guardar cambios
        $this->planRepository->save($plan);

        return PlanResponseDTO::fromEntity($plan);
    }
} 