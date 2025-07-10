<?php

namespace App\Infrastructure\Plan;

use App\Domain\Plan\Plan;
use App\Domain\Plan\PlanRepositoryInterface;
use App\ValueObjects\Plan\PlanId;
use App\ValueObjects\Plan\PlanName;
use App\ValueObjects\Plan\MonthlyPrice;
use App\ValueObjects\Plan\UserLimit;
use App\ValueObjects\Plan\Features;
use App\Models\Plan as PlanModel;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanRepository implements PlanRepositoryInterface
{
    public function findById(PlanId $id): ?Plan
    {
        $planModel = PlanModel::withTrashed()->find($id->getValue());
        
        if ($planModel === null) {
            return null;
        }

        return $this->mapToEntity($planModel);
    }

    public function save(Plan $plan): Plan
    {
        $planModel = PlanModel::withTrashed()->find($plan->getId()->getValue());
        
        if ($planModel === null) {
            $planModel = new PlanModel();
        }

        $this->mapToModel($plan, $planModel);
        $planModel->save();
        
        // Si es un nuevo registro, crear una nueva entidad con el ID generado por la BD
        if ($plan->getId()->getValue() === 0) {
            return new Plan(
                new PlanId($planModel->id),
                $plan->getName(),
                $plan->getMonthlyPrice(),
                $plan->getUserLimit(),
                $plan->getFeatures(),
                $plan->isActive(),
                $plan->getCreatedAt(),
                $plan->getUpdatedAt(),
                $plan->getDeletedAt()
            );
        }
        
        return $plan;
    }

    public function delete(Plan $plan): void
    {
        $planModel = PlanModel::find($plan->getId()->getValue());
        
        if ($planModel !== null) {
            $planModel->delete();
        }
    }

    public function restore(Plan $plan): void
    {
        $planModel = PlanModel::withTrashed()->find($plan->getId()->getValue());
        
        if ($planModel !== null) {
            $planModel->restore();
        }
    }

    public function findAll(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $plans = PlanModel::whereNull('deleted_at')
            ->orderBy('monthly_price', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        $plans->getCollection()->transform(function ($planModel) {
            return $this->mapToEntity($planModel);
        });

        return $plans;
    }

    public function findActive(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $plans = PlanModel::whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('monthly_price', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        $plans->getCollection()->transform(function ($planModel) {
            return $this->mapToEntity($planModel);
        });

        return $plans;
    }

    public function findDeleted(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $plans = PlanModel::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $plans->getCollection()->transform(function ($planModel) {
            return $this->mapToEntity($planModel);
        });

        return $plans;
    }

    public function search(string $query, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $plans = PlanModel::whereNull('deleted_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhereJsonContains('features', $query);
            })
            ->orderBy('monthly_price', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        $plans->getCollection()->transform(function ($planModel) {
            return $this->mapToEntity($planModel);
        });

        return $plans;
    }

    public function findByPriceRange(float $minPrice, float $maxPrice, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $plans = PlanModel::whereNull('deleted_at')
            ->whereBetween('monthly_price', [$minPrice, $maxPrice])
            ->orderBy('monthly_price', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        $plans->getCollection()->transform(function ($planModel) {
            return $this->mapToEntity($planModel);
        });

        return $plans;
    }

    private function mapToEntity(PlanModel $planModel): Plan
    {
        // Laravel automáticamente decodifica los campos JSON, así que features ya es un array
        $features = is_string($planModel->features) ? json_decode($planModel->features, true) : $planModel->features;
        
        return new Plan(
            new PlanId($planModel->id),
            new PlanName($planModel->name),
            new MonthlyPrice($planModel->monthly_price),
            new UserLimit($planModel->user_limit),
            new Features($features),
            $planModel->is_active,
            new \DateTimeImmutable($planModel->created_at),
            new \DateTimeImmutable($planModel->updated_at),
            $planModel->deleted_at ? new \DateTimeImmutable($planModel->deleted_at) : null
        );
    }

    private function mapToModel(Plan $plan, PlanModel $planModel): void
    {
        // Solo asignar ID si el modelo ya existe y el ID no es 0 (nuevo registro)
        if ($planModel->exists && $plan->getId()->getValue() > 0) {
            $planModel->id = $plan->getId()->getValue();
        }
        
        $planModel->name = $plan->getName()->getValue();
        $planModel->monthly_price = $plan->getMonthlyPrice()->getValue();
        $planModel->user_limit = $plan->getUserLimit()->getValue();
        $planModel->features = json_encode($plan->getFeatures()->getFeatures());
        $planModel->is_active = $plan->isActive();
        $planModel->created_at = $plan->getCreatedAt()->format('Y-m-d H:i:s');
        $planModel->updated_at = $plan->getUpdatedAt()->format('Y-m-d H:i:s');
        $planModel->deleted_at = $plan->getDeletedAt()?->format('Y-m-d H:i:s');
    }
} 