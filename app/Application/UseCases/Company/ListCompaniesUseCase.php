<?php

namespace App\Application\UseCases\Company;

use App\Domain\Company\Company;
use App\Domain\Company\CompanyRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListCompaniesUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository
    ) {}

    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return $this->companyRepository->findAll($perPage);
    }
} 