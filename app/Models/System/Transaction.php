<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'external_id',
        'amount_usd',
        'method',
        'status',
        'meta',
    ];

    protected $casts = [
        'amount_usd' => 'float',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['username'])) {
            $username = trim($filters['username']);
            $query->whereHas('user', function($q) use ($username) {
                $q->where('login', 'like', "%$username%")
                  ->orWhere('email', 'like', "%$username%");
            });
        }
        if (!empty($filters['method'])) {
            $query->where('method', $filters['method']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        return $query;
    }
}


