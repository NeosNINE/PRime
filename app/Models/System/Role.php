<?php

namespace App\Models\System;

use App\Models\Traits\ModelTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Role extends Model
{

    use SoftDeletes, ModelTrait;


    protected $casts = [
        'access' => 'json'
    ];


    /**
     * Пользователи принадлежащие роли
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

}
