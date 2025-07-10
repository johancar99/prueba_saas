<?php

namespace App\Domain\User;

use App\ValueObjects\User\UserId;
use App\ValueObjects\User\Email;
use App\ValueObjects\User\CompanyId;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;
    
    public function findByEmail(Email $email): ?User;
    
    public function findByCompanyId(CompanyId $companyId, int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function save(User $user): void;
    
    public function delete(User $user): void;
    
    public function restore(User $user): void;
    
    public function findAll(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function findDeleted(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function search(string $query, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    // Nuevo método para contar usuarios activos por empresa
    public function countActiveByCompanyId(CompanyId $companyId): int;
} 