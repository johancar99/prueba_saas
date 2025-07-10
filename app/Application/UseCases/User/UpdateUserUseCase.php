<?php

namespace App\Application\UseCases\User;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Application\DTOs\User\UpdateUserDTO;
use App\Application\DTOs\User\UserResponseDTO;
use App\ValueObjects\User\UserId;
use App\ValueObjects\User\CompanyId;
use Illuminate\Support\Facades\Auth;

class UpdateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(UserId $userId, UpdateUserDTO $dto): UserResponseDTO
    {
        $user = $this->userRepository->findById($userId);
        
        if ($user === null) {
            throw new \InvalidArgumentException('User not found');
        }

        // Verificar que el usuario autenticado puede actualizar a este usuario
        $authenticatedUser = Auth::user();
        
        if ($authenticatedUser->hasRole('admin')) {
            // Si es admin, solo puede actualizar usuarios de su empresa
            if ($user->getCompanyId() === null || !$user->getCompanyId()->equals(new CompanyId($authenticatedUser->company_id))) {
                throw new \InvalidArgumentException('You can only update users from your own company');
            }
        }
        // Si es super-admin, puede actualizar cualquier usuario

        // Verificar si el email ya existe (si se está actualizando)
        if ($dto->email !== null) {
            $existingUser = $this->userRepository->findByEmail($dto->email);
            if ($existingUser !== null && !$existingUser->getId()->equals($userId)) {
                throw new \InvalidArgumentException('User with this email already exists');
            }
        }

        // Aplicar cambios
        if ($dto->name !== null) {
            $user->updateName($dto->name);
        }

        if ($dto->email !== null) {
            $user->updateEmail($dto->email);
        }

        if ($dto->password !== null) {
            $user->updatePassword($dto->password->hash());
        }

        if ($dto->companyId !== null) {
            $user->updateCompanyId($dto->companyId);
        }

        // Guardar cambios
        $this->userRepository->save($user);

        return UserResponseDTO::fromEntity($user);
    }
} 