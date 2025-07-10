<?php

namespace App\Application\UseCases\Company;

use App\ValueObjects\Company\CompanyId;
use App\Domain\Company\CompanyRepositoryInterface;

class DeleteCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository
    ) {}

    public function execute(CompanyId $companyId): bool
    {
        $company = $this->companyRepository->findById($companyId);
        if (!$company) {
            throw new \InvalidArgumentException('Company not found');
        }

        $this->companyRepository->delete($company);
        return true;
    }
} 