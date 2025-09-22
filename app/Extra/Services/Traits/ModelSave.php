<?php

namespace App\Extra\Services\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

trait ModelSave
{

    /**
     *  Подготовить массив
     */
    protected function dataPrepare( $data ): array
    {

        //Если передан не массив - пытаемся его преобразовать
        if( is_object($data) )
            $data = $data->all();


        return (array)$data;

    }


    /**
     * Подготовить массив с аргументами
     * @throws Exception
     */
    protected function argsPrepare( $args = [], $default = [] ): array
    {

        $args = $this->dataPrepare($args);

        foreach ( $default as $key => $value ){

            if( !isset($args[$key]) )
                $args[$key] = $value;

        }

        if( isset($args['limit']) && $args['limit'] == 0 )
            $args['limit'] = 999999999;

        $model_fields = array_keys($this->getModelFields());
        $except_fields = array_keys($this->defaultGetParams());

        foreach( $args as $key => $val ){

            if( Str::startsWith($key,'field.') ){

                $args['fields'][substr($key,6)] = $val;

            }elseif( in_array($key, $model_fields) && !in_array($key, $except_fields) ){

                $args['fields'][$key] = $val;

            }

        }

        return $args;
    }


    /**
     * Установить значение у объекта
     * @throws Exception
     */
    protected function setupFields( $model, array $data, array $fields = null ){

        if( $fields == null )
            $fields = $this->getModelFieldsKeys($model);

        foreach( $data as $key => $value ){

            if( is_string($value) && mb_strtolower($value) == 'null' )
                $value = null;

            //это нужно, чтобы, если в multiple select не было выбрано не одного значение, то поле сохранялось в базу как null
            if( is_array($value) && array_key_exists(0, $value) && count($value) == 1 && $value[0] == null )
                $value = null;

            if( in_array( $key, $fields ) ) {

                $model->{$key} = $value;

                $model->{$key} = $model->{$key}; //Не удалять. Строчка нужна, чтобы принудительно выполнить cast аттрибута и избежать ошибок SQL при сохранении в БД

            }

        }

        return $model;
    }



    /**
     * Проставить все значения и сохранить объект
     * @throws Exception
     * @throws Throwable
     */
    protected function saveModel( $model, $data, bool $db_transaction = false ){

        $callback = function () use ($model, $data){

            $data = $this->dataPrepare($data);

            $this->setupFields($model, $data);

            $model->save();

            fileUploads()->setFilesUsed($model, $data);

            $this->setupRelations($model, $data);

            return $model;

        };

        if( $db_transaction )
            return DB::transaction($callback);

        return $callback();

    }


    /**
     * Подготовить данные для сохранения (при редактировании / добавлении)
     */
    protected function saveDataPrepare( array $data, $model = null ): array
    {

        return $data;

    }


    /**
     * Запускается перед сохранением (при редактировании / добавлении)
     */
    protected function beforeSave( array $data, $model = null ){

    }


    /**
     * Запускается после сохранения (при редактировании / добавлении)
     */
    protected function afterSave( array $data, $model ){

    }


    /**
     * Валидация при сохранении
     */
    public function saveValidate( array $data, $model = null ): void
    {
        /*

        Validator::make( $data, [

        ])->validate();

        */

    }


    /**
     * Установить user_id
     * @throws Exception
     */
    public function setUserIdField( array $data ): array
    {

        if(
            in_array('user_id', $this->getModelFieldsKeys())
            && !isset($data['user_id'])
            && \Auth::check()
        )
            $data['user_id'] = \Auth::id();


        return $data;

    }


}
