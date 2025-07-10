<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'monthly_price',
        'user_limit',
        'features',
        'is_active',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'user_limit' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
