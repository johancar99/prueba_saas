<?php

namespace App\Application\UseCases\User;

use App\Domain\User\UserRepositoryInterface;
use App\Application\DTOs\User\UserResponseDTO;
use App\ValueObjects\User\UserId;
use App\ValueObjects\User\CompanyId;
use Illuminate\Support\Facades\Auth;

class GetUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(UserId $userId): UserResponseDTO
    {
        $user = $this->userRepository->findById($userId);
        
        if ($user === null) {
            throw new \InvalidArgumentException('User not found');
        }

        // Verificar que el usuario autenticado puede acceder a este usuario
        $authenticatedUser = Auth::user();
        
        if ($authenticatedUser->hasRole('admin')) {
            // Si es admin, solo puede ver usuarios de su empresa
            if ($user->getCompanyId() === null || !$user->getCompanyId()->equals(new CompanyId($authenticatedUser->company_id))) {
                throw new \InvalidArgumentException('You can only access users from your own company');
            }
        }
        // Si es super-admin, puede acceder a cualquier usuario

        return UserResponseDTO::fromEntity($user);
    }
} 