<?php

namespace App\Domain\Company;

use App\ValueObjects\Company\CompanyId;
use App\ValueObjects\Company\CompanyName;
use App\ValueObjects\Company\CompanyEmail;
use App\ValueObjects\Company\CompanyPhone;
use App\ValueObjects\Company\CompanyAddress;
use App\ValueObjects\Company\CompanyStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function getId(): CompanyId
    {
        return new CompanyId($this->id);
    }

    public function getName(): CompanyName
    {
        return new CompanyName($this->name);
    }

    public function getEmail(): CompanyEmail
    {
        return new CompanyEmail($this->email);
    }

    public function getPhone(): CompanyPhone
    {
        return new CompanyPhone($this->phone);
    }

    public function getAddress(): CompanyAddress
    {
        return new CompanyAddress($this->address);
    }

    public function getStatus(): CompanyStatus
    {
        return new CompanyStatus($this->is_active);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('is_active', true)
            ->where('ends_at', '>', now())
            ->first();
    }
} 