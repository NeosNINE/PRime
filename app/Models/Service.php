<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'provider_id',
        'external_id',
        'name',
        'description',
        'min_quantity',
        'max_quantity',
        'cost_price',
        'price',
        'is_active',
        'is_manual',
        'total_orders',
        'meta',
    ];

    protected $casts = [
        'min_quantity' => 'int',
        'max_quantity' => 'int',
        'cost_price' => 'float',
        'price' => 'float',
        'is_active' => 'bool',
        'is_manual' => 'bool',
        'total_orders' => 'int',
        'meta' => 'array',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
