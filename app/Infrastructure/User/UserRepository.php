<?php

namespace App\Infrastructure\User;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\ValueObjects\User\UserId;
use App\ValueObjects\User\Email;
use App\ValueObjects\User\Name;
use App\ValueObjects\User\Password;
use App\ValueObjects\User\CompanyId;
use App\Models\User as UserModel;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function findById(UserId $id): ?User
    {
        $userModel = UserModel::withTrashed()->find($id->getValue());
        
        if ($userModel === null) {
            return null;
        }

        return $this->mapToEntity($userModel);
    }

    public function findByEmail(Email $email): ?User
    {
        $userModel = UserModel::withTrashed()->where('email', $email->getValue())->first();
        
        if ($userModel === null) {
            return null;
        }

        return $this->mapToEntity($userModel);
    }

    public function findByCompanyId(CompanyId $companyId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $users = UserModel::whereNull('deleted_at')
            ->where('company_id', $companyId->getValue())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $users->getCollection()->transform(function ($userModel) {
            return $this->mapToEntity($userModel);
        });

        return $users;
    }

    public function save(User $user): void
    {
        $userModel = UserModel::withTrashed()->find($user->getId()->getValue());
        
        if ($userModel === null) {
            $userModel = new UserModel();
        }

        $this->mapToModel($user, $userModel);
        $userModel->save();
    }

    public function delete(User $user): void
    {
        $userModel = UserModel::find($user->getId()->getValue());
        
        if ($userModel !== null) {
            $userModel->delete();
        }
    }

    public function restore(User $user): void
    {
        $userModel = UserModel::withTrashed()->find($user->getId()->getValue());
        
        if ($userModel !== null) {
            $userModel->restore();
        }
    }

    public function findAll(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $users = UserModel::whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $users->getCollection()->transform(function ($userModel) {
            return $this->mapToEntity($userModel);
        });

        return $users;
    }

    public function findDeleted(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $users = UserModel::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $users->getCollection()->transform(function ($userModel) {
            return $this->mapToEntity($userModel);
        });

        return $users;
    }

    public function search(string $query, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $users = UserModel::whereNull('deleted_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $users->getCollection()->transform(function ($userModel) {
            return $this->mapToEntity($userModel);
        });

        return $users;
    }

    public function countActiveByCompanyId(CompanyId $companyId): int
    {
        return UserModel::where('company_id', $companyId->getValue())
            ->whereNull('deleted_at')
            ->count();
    }

    private function mapToEntity(UserModel $userModel): User
    {
        return new User(
            new UserId($userModel->id),
            new Name($userModel->name),
            new Email($userModel->email),
            new Password($userModel->password, true),
            $userModel->company_id ? new CompanyId($userModel->company_id) : null,
            $userModel->email_verified_at ? new \DateTimeImmutable($userModel->email_verified_at) : null,
            new \DateTimeImmutable($userModel->created_at),
            new \DateTimeImmutable($userModel->updated_at),
            $userModel->deleted_at ? new \DateTimeImmutable($userModel->deleted_at) : null
        );
    }

    private function mapToModel(User $user, UserModel $userModel): void
    {
        $userModel->id = $user->getId()->getValue();
        $userModel->name = $user->getName()->getValue();
        $userModel->email = $user->getEmail()->getValue();
        $userModel->company_id = $user->getCompanyId()?->getValue();
        $userModel->email_verified_at = $user->getEmailVerifiedAt()?->format('Y-m-d H:i:s');
        $userModel->created_at = $user->getCreatedAt()->format('Y-m-d H:i:s');
        $userModel->updated_at = $user->getUpdatedAt()->format('Y-m-d H:i:s');
        $userModel->deleted_at = $user->getDeletedAt()?->format('Y-m-d H:i:s');
        
        // Manejar el password sin hashear de nuevo
        $userModel->setRawAttributes(array_merge($userModel->getAttributes(), [
            'password' => $user->getPassword()->getValue()
        ]));
    }
} 