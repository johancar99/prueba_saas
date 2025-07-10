<?php

namespace App\Application\UseCases\Plan;

use App\Domain\Plan\Plan;
use App\Domain\Plan\PlanRepositoryInterface;
use App\Application\DTOs\Plan\CreatePlanDTO;
use App\Application\DTOs\Plan\PlanResponseDTO;

class CreatePlanUseCase
{
    public function __construct(
        private PlanRepositoryInterface $planRepository
    ) {}

    public function execute(CreatePlanDTO $dto): PlanResponseDTO
    {
        // Crear la entidad de plan
        $plan = Plan::create(
            $dto->name,
            $dto->monthlyPrice,
            $dto->userLimit,
            $dto->features
        );

        // Guardar en el repositorio y obtener la entidad actualizada con el ID correcto
        $savedPlan = $this->planRepository->save($plan);

        // Retornar DTO de respuesta
        return PlanResponseDTO::fromEntity($savedPlan);
    }
} 