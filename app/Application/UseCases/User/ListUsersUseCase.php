<?php

namespace App\Application\UseCases\User;

use App\Domain\User\UserRepositoryInterface;
use App\Application\DTOs\User\UserResponseDTO;
use App\ValueObjects\User\CompanyId;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ListUsersUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $user = Auth::user();
        
        // Si el usuario es admin, solo mostrar usuarios de su empresa
        if ($user->hasRole('admin')) {
            $companyId = new CompanyId($user->company_id);
            $usersPaginator = $this->userRepository->findByCompanyId($companyId, $perPage, $page);
        } else {
            // Si es super-admin, mostrar todos los usuarios
            $usersPaginator = $this->userRepository->findAll($perPage, $page);
        }
        
        // Transformar las entidades a DTOs
        $usersPaginator->getCollection()->transform(function ($user) {
            return UserResponseDTO::fromEntity($user);
        });

        return $usersPaginator;
    }
} 