<?php

namespace App\Application\UseCases\Company;

use App\Domain\Company\Company;
use App\ValueObjects\Company\CompanyId;
use App\Domain\Company\CompanyRepositoryInterface;

class GetCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository
    ) {}

    public function execute(CompanyId $companyId): Company
    {
        $company = $this->companyRepository->findById($companyId);
        if (!$company) {
            throw new \InvalidArgumentException('Company not found');
        }

        return $company;
    }
} 