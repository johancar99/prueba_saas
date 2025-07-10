<?php

namespace App\Application\UseCases\Plan;

use App\Domain\Plan\PlanRepositoryInterface;
use App\Application\DTOs\Plan\PlanResponseDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPlansUseCase
{
    public function __construct(
        private PlanRepositoryInterface $planRepository
    ) {}

    public function execute(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $plansPaginator = $this->planRepository->findAll($perPage, $page);
        
        // Transformar las entidades a DTOs
        $plansPaginator->getCollection()->transform(function ($plan) {
            return PlanResponseDTO::fromEntity($plan);
        });

        return $plansPaginator;
    }
} 