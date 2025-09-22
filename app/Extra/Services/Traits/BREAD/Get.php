<?php

namespace App\Extra\Services\Traits\BREAD;

use Eloquent;

trait Get
{

    /**
     * Выборка
     * @throws \Exception
     */
    public function get( $args = [], $model_class = null ): mixed
    {

        if( !is_null($model_class) )
            $this->setModelKey($model_class);

        /**
         * Model Class @var $model_class Eloquent
         */
        $model_class = $this->getModelClass();

        $args = $this->argsPrepare($args,$this->defaultGetParams());
        $args = $this->getDataPrepare($args);

        return $this->getModelAll( $model_class::query(), $args );

    }


    /**
     * Стандартные настройки для метода get()
     */
    protected function defaultGetParams(): array
    {
        return [
            'limit' => 20,
            'sort_by' => 'id',
            'sort_order' => 'DESC',
            'search' => false,
            'paginate' => true
        ];
    }



    /**
     * Подготовить данные для выборки
     */
    protected function getDataPrepare( array $data ): array
    {

        return $data;

    }

}
