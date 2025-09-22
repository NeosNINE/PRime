<?php

namespace App\Models\System;

use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Localization extends Model
{

    use SoftDeletes, ModelTrait;

    protected $casts = [
        'name' => 'json',
        'text' => 'json'
    ];


    /**
     * Получить родительский раздел
     */
    public function section(): HasOne
    {
        return $this->hasOne(LocalizationSection::class,'id', 'section_id');
    }

}
