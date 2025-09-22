<?php

namespace App\Models\System;

use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocalizationSection extends Model
{

    use SoftDeletes, ModelTrait;

    protected $casts = [
        'name' => 'json'
    ];


    /**
     * Получить дочерние разделы
     */
    public function sections(): HasMany
    {
        return $this->hasMany(LocalizationSection::class,'section_id')->orderBy('name','ASC');
    }


    /**
     * Получить родительский раздел
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(LocalizationSection::class,'section_id');
    }


    /**
     * Получить название в виде хлебных крошек
     */
    public function getBreadcrumbNameAttribute(): ?string
    {

        if( $this->section )
            return $this->section->breadcrumb_name." -> ".langText($this->name);

        return langText($this->name);

    }

}
