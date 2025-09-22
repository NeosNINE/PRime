<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'run_number',
        'quantity',
        'status',
        'scheduled_for',
        'dispatched_at',
        'completed_at',
    ];

    protected $casts = [
        'run_number' => 'int',
        'quantity' => 'int',
        'scheduled_for' => 'datetime',
        'dispatched_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
