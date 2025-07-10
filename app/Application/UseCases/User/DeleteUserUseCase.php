<?php

namespace App\Application\UseCases\User;

use App\Domain\User\UserRepositoryInterface;
use App\ValueObjects\User\UserId;
use App\ValueObjects\User\CompanyId;
use Illuminate\Support\Facades\Auth;

class DeleteUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(UserId $userId): void
    {
        $user = $this->userRepository->findById($userId);
        
        if ($user === null) {
            throw new \InvalidArgumentException('User not found');
        }

        if ($user->isDeleted()) {
            throw new \InvalidArgumentException('User is already deleted');
        }

        // Verificar que el usuario autenticado puede eliminar a este usuario
        $authenticatedUser = Auth::user();
        
        if ($authenticatedUser->hasRole('admin')) {
            // Si es admin, solo puede eliminar usuarios de su empresa
            if ($user->getCompanyId() === null || !$user->getCompanyId()->equals(new CompanyId($authenticatedUser->company_id))) {
                throw new \InvalidArgumentException('You can only delete users from your own company');
            }
        }
        // Si es super-admin, puede eliminar cualquier usuario

        $user->delete();
        $this->userRepository->save($user);
    }
} 