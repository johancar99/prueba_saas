<?php

namespace App\Application\UseCases\Auth;

use App\Domain\Auth\AuthServiceInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Application\DTOs\Auth\LoginDTO;
use App\Application\DTOs\Auth\AuthResponseDTO;
use App\ValueObjects\User\Email;
use App\ValueObjects\User\Password;

class LoginUseCase
{
    public function __construct(
        private AuthServiceInterface $authService,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(LoginDTO $dto): AuthResponseDTO
    {
        // Buscar usuario por email
        $user = $this->userRepository->findByEmail($dto->email);
        
        if ($user === null) {
            throw new \InvalidArgumentException('Invalid credentials');
        }

        // Verificar contraseña
        if (!$user->getPassword()->verify($dto->password->getValue())) {
            throw new \InvalidArgumentException('Invalid credentials');
        }

        // Verificar si el usuario está eliminado
        if ($user->isDeleted()) {
            throw new \InvalidArgumentException('User account is deactivated');
        }

        // Generar nuevo token (esto ya elimina tokens anteriores en el AuthService)
        $token = $this->authService->login($dto->email, $dto->password);

        return new AuthResponseDTO(
            $token,
            $user->getId(),
            $user->getName()->getValue(),
            $user->getEmail()->getValue()
        );
    }
} 