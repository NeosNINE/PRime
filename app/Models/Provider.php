<?php

namespace App\Models;

use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    use ModelTrait;

    protected $fillable = [
        'name',
        'driver',
        'api_url',
        'api_key',
        'is_active',
        'balance',
        'currency',
        'last_synced_at',
        'services_last_synced_at',
        'low_balance_threshold',
        'last_balance_notification_at',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'balance' => 'decimal:4',
        'last_synced_at' => 'datetime',
        'services_last_synced_at' => 'datetime',
        'low_balance_threshold' => 'decimal:4',
        'last_balance_notification_at' => 'datetime',
        'meta' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
