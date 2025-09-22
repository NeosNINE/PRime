<?php

namespace App\\Models\\System;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'driver',
        'api_url',
        'api_key',
        'is_active',
    ];
}
