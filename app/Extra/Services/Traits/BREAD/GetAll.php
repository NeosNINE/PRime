<?php

namespace App\Extra\Services\Traits\BREAD;

use Exception;

trait GetAll
{

    /**
     * Выборка всех строк
     * @throws Exception
     */
    public function getAll( $model_class = null ): mixed
    {
        return $this->get([ 'limit' => 0, 'paginate' => false ], $model_class);
    }

}
