<?php

namespace App\Application\UseCases\Company;

use App\Application\DTOs\Company\CreateCompanyDTO;
use App\Domain\Company\Company;
use App\Domain\Company\CompanyRepositoryInterface;
use App\Domain\Plan\PlanRepositoryInterface;
use App\Domain\Events\Company\CompanyCreated;
use App\Domain\User\UserRepositoryInterface;
use App\Application\DTOs\User\CreateUserDTO;
use App\ValueObjects\User\Name;
use App\ValueObjects\User\Email;
use App\ValueObjects\User\Password;
use App\ValueObjects\User\CompanyId;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use App\Models\User as UserModel;

class CreateCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private PlanRepositoryInterface $planRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(CreateCompanyDTO $dto): array
    {
        // Verificar que el plan existe
        $plan = $this->planRepository->findById($dto->planId);
        if (!$plan) {
            throw new \InvalidArgumentException('Plan not found');
        }

        // Crear la empresa
        $company = new Company();
        $company->name = $dto->name->getValue();
        $company->email = $dto->email->getValue();
        $company->phone = $dto->phone->getValue();
        $company->address = $dto->address->getValue();
        $company->is_active = $dto->status->getValue();

        $this->companyRepository->save($company);

        // Crear el usuario admin para la empresa
        $adminName = $dto->adminName ?? $dto->name->getValue();
        $userDto = new CreateUserDTO(
            new Name($adminName),
            new Email($dto->adminEmail),
            new Password($dto->adminPassword),
            new CompanyId($company->id)
        );
        $userResponse = $this->userRepository->save(
            \App\Domain\User\User::create(
                new Name($adminName),
                new Email($dto->adminEmail),
                (new Password($dto->adminPassword))->hash(),
                new CompanyId($company->id)
            )
        );

        // Asignar el rol admin usando el modelo Eloquent
        $userModel = UserModel::where('email', $dto->adminEmail)->first();
        if ($userModel) {
            $userModel->assignRole('admin');
        }

        // Disparar evento de dominio para crear la suscripción inicial
        Event::dispatch(new CompanyCreated($company, $dto->planId));

        return [
            'company' => $company,
            'admin_user' => $userModel
        ];
    }
} 