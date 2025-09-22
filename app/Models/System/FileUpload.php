<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{

    public $timestamps;

    protected $fillable = [
        'user_id',
        'path',
        'original_name',
        'used'
    ];

}
