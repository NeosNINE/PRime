<?php

namespace App\Extra\Services\Traits;

use App\Extra\Services\Traits\BREAD\Add;
use App\Extra\Services\Traits\BREAD\Delete;
use App\Extra\Services\Traits\BREAD\Edit;
use App\Extra\Services\Traits\BREAD\Get;
use App\Extra\Services\Traits\BREAD\GetAll;
use App\Extra\Services\Traits\BREAD\GetOneById;

trait BREAD
{
    use Add, Edit, GetOneById, GetAll, Get, Delete;
}
