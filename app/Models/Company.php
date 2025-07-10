<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use SoftDeletes, HasFactory;

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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(\App\Models\Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('is_active', true)
            ->where('ends_at', '>', now())
            ->first();
    }
} 