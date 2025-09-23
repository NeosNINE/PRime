<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceMarkup extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope',
        'provider_id',
        'service_category_id',
        'service_id',
        'percent',
        'fixed',
    ];

    protected $casts = [
        'percent' => 'float',
        'fixed' => 'float',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
