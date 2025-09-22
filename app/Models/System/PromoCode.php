<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class PromoCode extends Model
{
    use HasFactory;

    protected $table = 'promo_codes';

    protected $fillable = [
        'code',
        'type',
        'bonus_amount',
        'active',
        'expires_at'
    ];

    protected $casts = [
        'active' => 'boolean',
        'bonus_amount' => 'float',
        'expires_at' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'promo_code_user');
    }

    public function isValidForUser(?\App\Models\User $user): bool
    {
        if (!$this->active) return false;
        if ($this->expires_at && now()->greaterThan($this->expires_at)) return false;
        if ($this->type === 'individual') {
            if (!$user) return false;
            $relation = $this->users()->where('users.id', $user->id);
            if (!$relation->exists()) return false;
            // One-time usage for individual promo codes
            $used = \DB::table('promo_code_user')
                ->where('promo_code_id', $this->id)
                ->where('user_id', $user->id)
                ->whereNotNull('used_at')
                ->exists();
            if ($used) return false;
            return true;
        }
        return true;
    }
}


