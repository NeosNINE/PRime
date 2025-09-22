<?php

namespace App\Extra\Services\Traits\BREAD;

use Eloquent;
use Illuminate\Support\Facades\DB;
use Throwable;

trait Add
{

    /**
     * Добавление
     * @throws Throwable
     */
    public function add( $data, $model_class = null ){

        if( !is_null($model_class) )
            $this->setModelKey($model_class);

        /**
         * Model Class @var $model_class Eloquent
         */
        $model_class = $this->getModelClass();

        $data = $this->dataPrepare($data);

        try {

            $data = $this->saveDataPrepare($data);
            $data = $this->addDataPrepare($data);

        } catch ( Throwable $throwable ){

            //Такое исключение не выбрасываем т.к. ниже валидация должна выбросить ее как ошибку валидации
            if( !str($throwable)->lower()->contains('undefined array key') )
                throw $throwable;

        }


        $data = $this->setUserIdField($data);

        $this->saveValidate($data);
        $this->addValidate($data);


        return DB::transaction(function () use ($model_class, $data){

            if( $before_save = $this->beforeSave($data) )
                $data = $before_save;

            if( $before_edit = $this->beforeAdd($data) )
                $data = $before_edit;

            $model = $this->saveModel( new $model_class(), $data );

            events()->setClientEvent($this->getEventName('add'), $model);

            $this->afterSave($data, $model);
            $this->afterAdd($data, $model);

            $this->clearModelKey();

            return $model;

        });

    }


    /**
     * Подготовить данные для добавления
     */
    protected function addDataPrepare( array $data ): array
    {

        return $data;

    }


    /**
     * Запускается перед добавлением (мы можем права доступа проверить здесь, например)
     */
    protected function beforeAdd( array $data ){

    }


    /**
     * Запускается после добавления
     */
    protected function afterAdd( array $data, $model ){

    }


    /**
     * Валидация при добавлении
     */
    public function addValidate( array $data ): void
    {
        /*

        Validator::make( $data, [

        ])->validate();

        */

    }

}
