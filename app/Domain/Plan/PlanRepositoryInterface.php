<?php

namespace App\Domain\Plan;

use App\ValueObjects\Plan\PlanId;
use Illuminate\Pagination\LengthAwarePaginator;

interface PlanRepositoryInterface
{
    public function findById(PlanId $id): ?Plan;
    
    public function save(Plan $plan): Plan;
    
    public function delete(Plan $plan): void;
    
    public function restore(Plan $plan): void;
    
    public function findAll(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function findActive(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function findDeleted(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function search(string $query, int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function findByPriceRange(float $minPrice, float $maxPrice, int $perPage = 15, int $page = 1): LengthAwarePaginator;
} 