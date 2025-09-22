<?php

namespace App\Extra\Services\Traits\BREAD;

use Illuminate\Support\Facades\DB;
use Throwable;

trait Edit
{

    /**
     * Редактирование
     * @throws \Throwable
     */
    public function edit( $model, $data ){

        $this->setModelKey($model);

        $data = $this->dataPrepare($data);

        try {

            $data = $this->saveDataPrepare($data, $model);
            $data = $this->editDataPrepare($data, $model);

        } catch ( Throwable $throwable ){

            //Такое исключение не выбрасываем т.к. ниже валидация должна выбросить ее как ошибку валидации
            if( !str($throwable)->lower()->contains('undefined array key') )
                throw $throwable;

        }

        $data = $this->setUserIdField($data);

        $this->saveValidate($data, $model);
        $this->editValidate($data, $model);


        return DB::transaction(function () use ($model, $data){

            if( $before_save = $this->beforeSave($data, $model) )
                $data = $before_save;

            if( $before_edit = $this->beforeEdit($data, $model) )
                $data = $before_edit;

            $model = $this->saveModel($model, $data);

            events()->setClientEvent($this->getEventName('edit'), $model);

            $this->afterSave($data, $model);
            $this->afterEdit($data, $model);

            $this->clearModelKey();

            return $model;

        });

    }


    /**
     * Подготовить данные для добавления
     */
    protected function editDataPrepare( array $data, $model = null ): array
    {

        return $data;

    }


    /**
     * Запускается перед редактированием (мы можем права доступа проверить здесь, например)
     */
    protected function beforeEdit( array $data, $model ){

    }


    /**
     * Запускаеться после редактирования
     */
    protected function afterEdit( array $data, $model ){

    }


    /**
     * Валидация при редактировании
     */
    public function editValidate( array $data, $model = null ): void
    {

        /*

        Validator::make( $data, [

        ])->validate();

        */

    }


}
