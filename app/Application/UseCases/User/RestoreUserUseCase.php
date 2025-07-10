<?php

namespace App\Application\UseCases\User;

use App\Domain\User\UserRepositoryInterface;
use App\ValueObjects\User\UserId;
use App\ValueObjects\User\CompanyId;
use Illuminate\Support\Facades\Auth;

class RestoreUserUseCase
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

        if (!$user->isDeleted()) {
            throw new \InvalidArgumentException('User is not deleted');
        }

        // Verificar que el usuario autenticado puede restaurar a este usuario
        $authenticatedUser = Auth::user();
        
        if ($authenticatedUser->hasRole('admin')) {
            // Si es admin, solo puede restaurar usuarios de su empresa
            if ($user->getCompanyId() === null || !$user->getCompanyId()->equals(new CompanyId($authenticatedUser->company_id))) {
                throw new \InvalidArgumentException('You can only restore users from your own company');
            }
        }
        // Si es super-admin, puede restaurar cualquier usuario

        $user->restore();
        $this->userRepository->save($user);
    }
} 