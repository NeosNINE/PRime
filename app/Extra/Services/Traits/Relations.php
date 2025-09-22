<?php

namespace App\Extra\Services\Traits;

use App\Extra\Services\Service;
use Exception;

trait Relations
{


    /**
     * Получить информацию о связи для relation query
     * @throws Exception
     */
    protected function getDataForRelationQuery( array $relation ): array
    {

        /**
         * Service @var $service Service
         */
        $service = new $relation['service'];

        //Проставляем model_key т.к. у одного сервиса может быть несколько разных моделей
        $service->setModelKey($relation['to']);

        $relation_model = new ('App\\Models\\'.$relation['to']);
        $relation_table_db = $relation_model->getTable();
        $relation_fields = $service->getModelFieldsExcept();
        $relation_fields = $this->cleanFieldsForRelationQuery($relation, $relation_fields);

        return compact('service', 'relation_model', 'relation_table_db', 'relation_fields');

    }


    /**
     * Убрать не нужные поля из поиска по связям
     */
    protected function cleanFieldsForRelationQuery( array $relation, array $relation_fields ): array
    {

        $fields_to_clean = [
            'id', 'created_at', 'updated_at', 'deleted_at'
        ];

        foreach( $fields_to_clean as $clean_field ){

            if( isset($relation_fields[$clean_field]) ) unset($relation_fields[$clean_field]);

        }


        return $relation_fields;

    }



    /**
     * Получить список названий полей, которые можно передать в request запрос, чтобы выбрать нужные сущности по связям
     * @throws Exception
     */
    public function getRequestInputNamesForRelations(): array
    {

        if( !isset($this->request_input_names_for_relation) )
            $this->request_input_names_for_relation = $this->requestInputNamesForRelation();

        if( $this->request_input_names_for_relation )
            return $this->request_input_names_for_relation;

        $fields_names = [];

        foreach( $this->getModelRelations() as $relation )
            $fields_names[$relation['to']] = $this->getRequestInputNamesForRelation($relation);

        return $this->request_input_names_for_relation = $fields_names;

    }


    /**
     * Получить список названий полей для определенной сущности, которые можно передать в request запрос, чтобы выбрать нужные сущности по связям
     * @throws Exception
     */
    public function getRequestInputNamesForRelation( array $relation ): array
    {

        $model_name = explode('\\',$relation['to']);
        $model_name = array_pop($model_name);
        $model_name = str($model_name)->singular();
        $model_name_plural = str($model_name)->plural();

        $extra_model_name = explode('_', str($model_name)->snake());
        $extra_model_name = array_pop($extra_model_name);
        $extra_model_name = str($extra_model_name)->singular();
        $extra_model_name_plural = str($extra_model_name)->plural();

        return array_unique([
            str($model_name.'_id')->snake()->toString(),
            str($model_name.'_ids')->snake()->toString(),
            str($extra_model_name.'_id')->snake()->toString(),
            str($extra_model_name.'_ids')->snake()->toString(),

            str($model_name_plural.'_id')->snake()->toString(),
            str($extra_model_name_plural.'_id')->snake()->toString(),

            str($model_name)->snake()->toString(),
            str($extra_model_name_plural)->snake()->toString(),
            str($model_name_plural)->snake()->toString(),
            str($extra_model_name)->snake()->toString()
        ]);

    }


    /**
     * Установить связи
     * @throws Exception
     */
    protected function setupRelations( $model, array $data )
    {

        $relations = $this->getModelRelations();

        if( !count($relations) )
            return $model;

        $should_refresh = false;
        $should_save = false;

        foreach( $relations as $relation ){

            if( !isset($relation['service_func']) )
                continue;

            extract($this->getDataForRelationQuery($relation));
            $request_input_fields = $this->getRequestInputNamesForRelations()[$relation['to']];

            $request_input_fields_data = null;
            foreach( $request_input_fields as $request_input_field_name ){

                if( isset($data[$request_input_field_name]) ){

                    $request_input_fields_data = $data[$request_input_field_name];
                    break;

                }

            }

            //Если не переданы никакие параметры для привязки других сущностей - пропускаем
            if( $request_input_fields_data === null )
                continue;

            //Привязываем несколько сущностей
            if( in_array($relation['type'], ['belongsToMany', 'hasMany']) ){

                if( !is_array($request_input_fields_data) )
                    throw new Exception('Request parameter "'.$request_input_field_name.'" should be an array of ids relation model.');

                $relation_model_ids = [];

                foreach( $request_input_fields_data as $relation_model_id ){

                    $relation_model = $this->getRelationModel($relation, $relation_model_id);

                    if( $relation_model && $this->canSetRelations($model, $relation_model, $relation) )
                        $relation_model_ids[] = $relation_model->id;

                }

                if( count($relation_model_ids) ) {

                    $model->{$relation['model_func']}()->sync($relation_model_ids);

                    $should_refresh = true;

                }



            //Принадлежит какой-то сущности
            }elseif ( in_array($relation['type'], ['belongsTo', 'hasOne']) ){

                $relation_model = $this->getRelationModel($relation, $request_input_fields_data);

                if( $relation_model && $this->canSetRelations($model, $relation_model, $relation) ){

                    $model->{$request_input_field_name} = $relation_model->id;

                }else{

                    $model->{$request_input_field_name} = null;

                }

                $should_save = true;

            }

        }


        if( $should_save )
            $model->save();

        if( $should_refresh )
            $model->refresh();

        return $model;

    }



    /**
     * Получить связанную модель
     * @throws Exception
     */
    protected function getRelationModel( $relation, $relation_model_id ){

        if( is_array($relation_model_id) || is_object($relation_model_id) )
            return null;

        $model_class = 'App\Models\\'.$relation['to'];

        $relation_model = $model_class::find($relation_model_id);

        if( in_array($relation['to'], $this->relationNotFoundModelFail()) && !$relation_model )
            throw new Exception('Relation model "'.$relation['to'].':'.$relation_model_id.'" not found.');

        return $relation_model;

    }



    /**
     * Получить список связей
     * @throws Exception
     */
    public function getModelRelations(): array
    {
        return $this->getModelRelationsFromJSON();
    }



    /**
     * Для каких моделей нужно останавливать работу скрипта, если не найдена связываемая модель
     * (по умолчанию такая связь будет просто пропускаться)
     * Передаем список моделей, например ['User', 'System\Email']
     */
    protected function relationNotFoundModelFail(): array
    {
        return [];
    }




    /**
     * Список название полей для связываемых сущностей
     * Если возвращает null - то будет автоматически определяться
     */
    protected function requestInputNamesForRelation(): ?array
    {
        return null;
    }




    /**
     * Проверка можем ли мы установить связь для этих моделей (при необходимости переопределяется метод в сервисе)
     */
    protected function canSetRelations( $model, $relation_model, $relation ): bool
    {

        return true;

    }


}
