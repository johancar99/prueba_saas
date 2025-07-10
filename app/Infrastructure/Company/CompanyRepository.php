<?php

namespace App\Infrastructure\Company;

use App\Domain\Company\CompanyRepositoryInterface;
use App\Domain\Company\Company;
use App\Domain\Company\Subscription;
use App\ValueObjects\Company\CompanyId;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function findById(CompanyId $id): ?Company
    {
        return Company::find($id->getValue());
    }

    public function save(Company $company): void
    {
        $company->save();
    }

    public function delete(Company $company): void
    {
        $company->delete();
    }

    public function restore(Company $company): void
    {
        $company->restore();
    }

    public function findAll(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Company::with('subscriptions.plan')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function findActive(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Company::with('subscriptions.plan')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function findDeleted(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Company::with('subscriptions.plan')
            ->onlyTrashed()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function search(string $query, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Company::with('subscriptions.plan')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function saveSubscription(Subscription $subscription): Subscription
    {
        $subscription->save();
        return $subscription;
    }
} 