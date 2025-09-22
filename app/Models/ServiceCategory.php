<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'name',
        'is_manual_only',
        'is_active',
    ];

    protected $casts = [
        'is_manual_only' => 'bool',
        'is_active' => 'bool',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'service_category_id');
    }
}
