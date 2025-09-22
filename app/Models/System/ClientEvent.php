<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class ClientEvent extends Model
{

    protected $fillable = ['event_name', 'data', 'access_key', 'unique', 'created_user_id', 'for_user_id', 'created_at'];

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime'
    ];

    public $timestamps = false;

}
