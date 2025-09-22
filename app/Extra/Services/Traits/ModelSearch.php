<?php

namespace App\Extra\Services\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait ModelSearch
{

    /**
     * Исключаемые поля из поиска (параметр search)
     */
    protected function exceptFieldsFromSearch(): array
    {
        return [];
    }


    /**
     * Получить список сущностей (универсальная функция)
     * @throws Exception
     */
    protected function getModelAll( Builder $query, array $args ){

        //Устанавливаем ключ модели
        $this->setModelKey($query->getModel()::class);

        //Подготавливаем параметры
        $args = $this->argsPrepareForGetModelAllQuery($args);

        //Ищем по удаленным записям
        $query = $this->setOnlyTrashedForGetModelAllQuery($query, $args);

        //Добавляем поиск search (ищет по всем полям через оператор LIKE %...%
        $query = $this->setSearchForGetModelAllQuery($query, $args);

        //Поиск по конкретному полю
        $query = $this->setSearchByFieldsForGetModelAllQuery($query, $args);

        //Добавить к query запросу - поиск по связям
        $query = $this->setRelationsForQuery($query, $args);

        //Сортировка
        $query = $this->setSortForGetModelAllQuery($query, $args);

        //Выбираем данные
        $result = $this->getDataForGetModelAllQuery($query, $args);

        //Отчищаем установленный model key
        $this->clearModelKey();

        return $result;

    }


    /**
     * Подготовить данные для выборки getModelAll
     */
    protected function argsPrepareForGetModelAllQuery( array $args ): array
    {

        //Если будет поиск по определенному id (вначале указана решетка и далее цифры)
        if( Str::startsWith($args['search'], '#') && is_numeric(substr($args['search'], 1)) ) {

            $args['fields']['id'] = substr($args['search'], 1);
            unset($args['search']);

        }

        return $args;

    }


    /**
     * Поиск по удаленным записям
     */
    protected function setOnlyTrashedForGetModelAllQuery( Builder $query, array $args ): Builder
    {

        if( isset($args['deleted']) && $args['deleted'] )
            $query->onlyTrashed();

        return $query;

    }


    /**
     * Добавить поиск по полям lIKE %...% для query getModelALl
     * @throws Exception
     */
    protected function setSearchForGetModelAllQuery( Builder $query, array $args ): Builder
    {

        //Поиск по полям
        if( isset($args['search']) && $args['search'] ){

            $query->where(function (Builder $query) use ($args){

                foreach( $this->getModelFieldsExcept() as $field_key => $field_data ){

                    if( $field_data['type'] == 'json' ){

                        $query->orWhere(DB::raw('lower(`'.$field_key.'`)'), "LIKE", "%".mb_strtolower($args['search'])."%");

                    }else{

                        $query->orWhere( $field_key, 'LIKE', "%".$args['search']."%");

                    }

                }

            });

        }

        return $query;

    }


    /**
     * Добавить поиск по конкретным полям для query getModelALl
     * @throws Exception
     */
    protected function setSearchByFieldsForGetModelAllQuery( Builder $query, array $args ): Builder
    {

        foreach( $this->getModelFields() as $field_key => $field_data ){

            if( isset($args['fields'][$field_key]) ){

                $query->where( $field_key, $args['fields'][$field_key]);

            }

        }

        return $query;

    }


    /**
     * Добавить сортировку для query getModelALl
     */
    protected function setSortForGetModelAllQuery( Builder $query, array $args ): Builder
    {

        return $query->orderBy($args['sort_by'], $args['sort_order']);

    }



    /**
     * Выборка данных query getModelALl
     */
    protected function getDataForGetModelAllQuery( Builder $query, array $args ){

        if( $args['paginate'] ){

            $data = $query->paginate( perPage: $args['limit'], pageName: $args['paginate_page_name'] ?? 'page' );
            $data->setCollection( $data->getCollection()->keyBy('id') );

        }else{

            $data = $query->limit($args['limit'])->get()->keyBy('id');

        }

        return $data;

    }



    /**
     * Добавить к query запросу - поиск по связям
     * @throws Exception
     */
    protected function setRelationsForQuery( Builder $query, array $args ): Builder
    {

        if( isset($args['without_relations']) && $args['without_relations'] )
            return $query;

        //Список связей среди которых обязательно нужно проводить поиск, то есть показывать только записи с этими связями (прописывается в формате System\Role)
        $only_with_relations = $args['only_with_relations'] ?? [];

        if( is_string($only_with_relations) )
            $only_with_relations = [$only_with_relations];

        //Какие связи нужно исключить (прописывается в формате System\Role)
        $except_relations = $args['except_relations'] ?? [];

        if( is_string($except_relations) )
            $except_relations = [$except_relations];

        foreach( $this->getModelRelations() as $relation ){

            if( isset($relation['service_func']) && $relation['service_func'] ){

                if( in_array($relation['to'], $except_relations) )
                    continue;

                extract($this->getDataForRelationQuery($relation));

                //Можно указать в аргументах search_relations и не указывать search, тогда поиск будет только по полям модели
                $search = $args['search_relations'] ?? $args['search'] ?? false;

                //Поиск по связям
                if( $search ) {

                    //Поиск ИЛИ
                    $search_type = 'orWhereHas';

                    //Если нужен поиск исключительно по этой связи
                    if (in_array($relation['to'], $only_with_relations))
                        $search_type = 'whereHas';


                    $query = $this->setRelationsSearchForQuery($query, $search, $search_type, $relation, $relation_fields, $relation_table_db);

                }


                //Добавляем выборку по связанным сущностям
                $query = $this->setRelationsRequestSelectForQuery($query, $args, $relation, $relation_fields, $relation_table_db);


            }

        }

        return $query;

    }


    /**
     * Проставляем для query поиск по связанным сущностям по полям через оператор like %...%
     * @param $search_type - может быть whereHas или orWhereHas
     */
    protected function setRelationsSearchForQuery( Builder $query, string $search, string $search_type, array $relation, array $relation_fields, string $relation_table_db ): Builder
    {

        $query->$search_type($relation['model_func'], function (Builder $query) use ($relation_fields, $relation_table_db, $search) {

            $query->where(function (Builder $q) use ($relation_fields, $relation_table_db, $search){

                foreach( $relation_fields as $field_key => $field_type ){

                    if( $field_type['type'] == 'json' ){

                        $q->orWhere(DB::raw('lower(`'.$field_key.'`)'), "like", "%".mb_strtolower($search)."%");

                    }else{

                        $q->orWhere($relation_table_db . '.' . $field_key, 'like', '%' . $search . '%');

                    }


                }

            });


        });

        return $query;

    }


    /**
     * Добавляет выборку по конкретным вязям. Это дает возможность передавать, например, category_id в параметрах и делать выборку сущностей из определенных категорий
     * @throws Exception
     */
    protected function setRelationsRequestSelectForQuery( Builder $query, array $args, array $relation, array $relation_fields, string $relation_table_db ): Builder
    {

        $request_fields_names_rel = $this->getRequestInputNamesForRelations()[$relation['to']] ?? [];

        foreach ( $request_fields_names_rel as $request_field ){

            if( isset($args[$request_field]) && $args[$request_field] ){

                $query->whereHas($relation['model_func'], function (Builder $query) use ($relation_fields, $args, $relation_table_db, $request_field) {

                    $query->where($relation_table_db . '.id', $args[$request_field]);

                });

            }

        }

        return $query;

    }

}
