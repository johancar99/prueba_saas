<?php

namespace App\Application\UseCases\Company;

use App\Application\DTOs\Company\UpdateCompanyDTO;
use App\Domain\Company\Company;
use App\Domain\Company\CompanyRepositoryInterface;

class UpdateCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository
    ) {}

    public function execute(UpdateCompanyDTO $dto): Company
    {
        $company = $this->companyRepository->findById($dto->id);
        if (!$company) {
            throw new \InvalidArgumentException('Company not found');
        }

        if ($dto->name) {
            $company->name = $dto->name->getValue();
        }

        if ($dto->email) {
            $company->email = $dto->email->getValue();
        }

        if ($dto->phone) {
            $company->phone = $dto->phone->getValue();
        }

        if ($dto->address) {
            $company->address = $dto->address->getValue();
        }

        if ($dto->status) {
            $company->is_active = $dto->status->getValue();
        }

        $this->companyRepository->save($company);
        return $company;
    }
} 