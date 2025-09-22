<?php

namespace App\Models\System;

use App\Models\Traits\ModelTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model
{

    use SoftDeletes, ModelTrait;


    protected $casts = [
        'data' => 'json',
        'sent_date' => 'datetime'
    ];



    /**
        Обратная связь один ко многим
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
