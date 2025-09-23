<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'provider_id',
        'external_id',
        'link',
        'quantity',
        'price',
        'cost_price',
        'status',
        'is_drip_feed',
        'drip_runs',
        'drip_interval_minutes',
        'drip_runs_processed',
        'is_manual',
        'completed_at',
        'failed_at',
        'refunded_at',
        'refunded_amount',
        'meta',
    ];

    protected $casts = [
        'quantity' => 'int',
        'price' => 'float',
        'cost_price' => 'float',
        'is_drip_feed' => 'bool',
        'drip_runs' => 'int',
        'drip_interval_minutes' => 'int',
        'drip_runs_processed' => 'int',
        'is_manual' => 'bool',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refunded_amount' => 'float',
        'meta' => 'array',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(OrderRun::class);
    }
}
