<?php

namespace App\Extra\Services\Traits\BREAD;

use Eloquent;

trait GetOneById
{

    /**
    Выборка по ID
     */
    public function getOneById( $id, bool $fail = true, $model_class = null ){

        if( !is_null($model_class) )
            $this->setModelKey($model_class);

        /**
         * Model Class @var $model_class Eloquent
         */
        $model_class = $this->getModelClass();

        $method = $fail ? 'findOrFail' : 'find';

        $result = $model_class::{$method}($id);

        $this->clearModelKey();

        return $result;

    }


    /**
     * Выпорка по ID из архива (для softDeletes)
     */
    public function getOneFromTrash( $id, bool $fail = true, $model_class = null ){

        if( !is_null($model_class) )
            $this->setModelKey($model_class);

        /**
         * Model Class @var $model_class Eloquent
         */
        $model_class = $this->getModelClass();

        $method = $fail ? 'findOrFail' : 'find';

        $result = $model_class::onlyTrashed()->{$method}($id);

        $this->clearModelKey();

        return $result;

    }

}
