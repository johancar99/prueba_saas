<?php

namespace App\Domain\Company;

use App\ValueObjects\Company\SubscriptionId;
use App\ValueObjects\Company\CompanyId;
use App\ValueObjects\Plan\PlanId;
use App\ValueObjects\Company\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'company_id',
        'plan_id',
        'is_active',
        'starts_at',
        'ends_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getId(): SubscriptionId
    {
        return new SubscriptionId($this->id);
    }

    public function getCompanyId(): CompanyId
    {
        return new CompanyId($this->company_id);
    }

    public function getPlanId(): PlanId
    {
        return new PlanId($this->plan_id);
    }

    public function getStatus(): SubscriptionStatus
    {
        return new SubscriptionStatus($this->is_active);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Plan::class);
    }
} 