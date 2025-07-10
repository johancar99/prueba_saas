<?php

namespace App\Application\UseCases\Company;

use App\Application\DTOs\Company\ChangePlanDTO;
use App\Domain\Company\Subscription;
use App\Domain\Company\CompanyRepositoryInterface;
use App\Domain\Plan\PlanRepositoryInterface;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChangePlanUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private PlanRepositoryInterface $planRepository
    ) {}

    public function execute(ChangePlanDTO $dto): Subscription
    {
        // Verificar que la empresa existe
        $company = $this->companyRepository->findById($dto->companyId);
        if (!$company) {
            throw new \InvalidArgumentException('Company not found');
        }

        // Verificar que el plan existe
        $plan = $this->planRepository->findById($dto->planId);
        if (!$plan) {
            throw new \InvalidArgumentException('Plan not found');
        }

        // Desactivar suscripción actual si existe
        $activeSubscription = $company->activeSubscription();
        if ($activeSubscription) {
            $activeSubscription->is_active = false;
            $activeSubscription->ends_at = Carbon::now();
            $this->companyRepository->saveSubscription($activeSubscription);
        }

        // Crear nueva suscripción
        $subscription = new Subscription();
        $subscription->company_id = $dto->companyId->getValue();
        $subscription->plan_id = $dto->planId->getValue();
        $subscription->is_active = true;
        $subscription->starts_at = Carbon::now();
        $subscription->ends_at = Carbon::now()->addMonth();

        return $this->companyRepository->saveSubscription($subscription);
    }
} 