<?php

namespace App\Application\UseCases\User;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Application\DTOs\User\CreateUserDTO;
use App\Application\DTOs\User\UserResponseDTO;
use App\ValueObjects\User\Name;
use App\ValueObjects\User\Email;
use App\ValueObjects\User\Password;
use App\ValueObjects\Company\CompanyId as CompanyCompanyId;

class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private \App\Domain\Company\CompanyRepositoryInterface $companyRepository
    ) {}

    public function execute(CreateUserDTO $dto): UserResponseDTO
    {
        // Verificar si el email ya existe
        $existingUser = $this->userRepository->findByEmail($dto->email);
        if ($existingUser !== null) {
            throw new \InvalidArgumentException('User with this email already exists');
        }

        // Validar límite de usuarios por plan si companyId está presente
        if ($dto->companyId) {
            // Convertir CompanyId del módulo User al módulo Company
            $companyCompanyId = new CompanyCompanyId($dto->companyId->getValue());
            $company = $this->companyRepository->findById($companyCompanyId);
            if ($company) {
                $activeSubscription = $company->activeSubscription();
                if ($activeSubscription && $activeSubscription->plan) {
                    $userLimit = $activeSubscription->plan->user_limit;
                    $currentUsers = $this->userRepository->countActiveByCompanyId($dto->companyId);
                    if ($userLimit !== null && $userLimit > 0 && $currentUsers >= $userLimit) {
                        throw new \InvalidArgumentException('User limit for this company plan has been reached.');
                    }
                }
            }
        }

        // Crear la entidad de usuario
        $user = User::create(
            $dto->name,
            $dto->email,
            $dto->password->hash(), // Hashear la contraseña
            $dto->companyId
        );

        // Guardar en el repositorio
        $this->userRepository->save($user);

        // Retornar DTO de respuesta
        return UserResponseDTO::fromEntity($user);
    }
} 