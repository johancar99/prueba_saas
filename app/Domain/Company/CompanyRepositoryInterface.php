<?php

namespace App\Domain\Company;

use App\Domain\Company\Company;
use App\ValueObjects\Company\CompanyId;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    public function findById(CompanyId $id): ?Company;
    
    public function save(Company $company): void;
    
    public function delete(Company $company): void;
    
    public function restore(Company $company): void;
    
    public function findAll(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function findActive(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function findDeleted(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function search(string $query, int $perPage = 15, int $page = 1): LengthAwarePaginator;
} 