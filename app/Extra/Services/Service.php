<?php


namespace App\Extra\Services;

use Exception;
use Illuminate\Support\Str;
use function app_path;

class Service
{


    /**
     * Устанавливаем ключ модели
     */
    protected ?string $model_key = null; //Устанавливается автоматически, если указан null
    protected bool $model_key_now_set_automatically = false;
    protected ?string $model_key_auto = null;


    /**
     * Events
     */
    protected array $events_name = []; //Устанавливается автоматически, если указанно null


    /**
     * Schema
     */
    protected array $schema = [];
    protected array $fields = [];
    protected array $relations = [];



    /**
     * Получить список fields из Schema
     * @throws Exception
     */
    protected function getModelFieldsFromJSON( string|object $key = '' ) : array
    {

        $key = $this->getModelKey($key);

        if( array_key_exists($key, $this->fields) )
            return $this->fields[$key];

        return $this->fields[$key] = $this->getSchema($key)['fields'];

    }



    /**
     * Получить список relations из Schema
     * @throws Exception
     */
    protected function getModelRelationsFromJSON( string|object $key = '' ) : array
    {

        $key = $this->getModelKey($key);

        if( array_key_exists($key, $this->relations) )
            return $this->relations[$key];

        return $this->relations[$key] = $this->getSchema($key)['relations'];

    }



    /**
     * Получить Schema model
     * @throws Exception
     */
    protected function getSchema( string|object $key = '' ): array
    {

        $key = $this->getModelKey($key);

        if( array_key_exists($key, $this->schema) )
            return $this->schema[$key];

        $path = app_path('Models/Schema/'. $key .'.json');

        if( !file_exists( $path) )
            throw new Exception('File '.$path.' not found. Maybe you should specify protected string $model_key in your service.');

        return $this->schema[$key] = json_decode(
            devTools()->getFile(
                app_path('Models/Schema/'. $key .'.json')
            ), 1);

    }


    /**
     * Получить ключ модели
     */
    protected function getModelKey( string|object $key = '' ): string
    {

        if( $key === '' ){

            $this->modelKeyCheck();
            return $this->model_key;

        }


        if( is_object($key) )
            $key = get_class($key);


        return str($key)->replace('\\', '/')->replace('App/Models/', '')->toString();

    }


    /**
     * Проверка $model_key
     */
    protected function modelKeyCheck(): void
    {

        //Если ключ модели явно не указан - пытаемся получить его
        if( is_null($this->model_key) ){

            if( is_null($this->model_key_auto) ) {

                $class = get_class($this);

                $class_name = explode('\\', $class);
                $class_name = end($class_name);

                $model_name = str($class_name)->replaceLast('Service', '', $class_name)->singular();

                $model_key = str($class)
                    ->replace('\\', '/')
                    ->replace('App/Services/', '')
                    ->replace($class_name, $model_name);

                if( !class_exists('App\Models\\'.str($model_key)->replace('/', '\\')) )
                    $model_key = $model_name;

                $this->model_key = $model_key;
                $this->model_key_auto = $this->model_key;


            }else{

                $this->model_key = $this->model_key_auto;

            }

            $this->model_key_now_set_automatically = true;

        }

    }


    /**
     * Получить список полей
     * @throws Exception
     */
    public function getModelFields( string|object $key = '' ): array
    {
        return $this->getModelFieldsFromJSON($key);
    }


    /**
     * Получить список полей (ключи)
     * @throws Exception
     */
    public function getModelFieldsKeys( string|object $key = '' ): array
    {
        return array_keys( $this->getModelFields($key) );
    }



    /**
     * Получить список полей кроме, тех, которые переданы
     * @throws Exception
     */
    public function getModelFieldsExcept( array|string|null $except = null, string|object $key = '' ): array
    {

        if( is_null($except) && method_exists($this, 'exceptFieldsFromSearch') )
            $except = $this->exceptFieldsFromSearch();

        $fields = $this->getModelFieldsFromJSON($key);

        if( !$except )
            return $fields;

        if( is_string($except) )
            $except = [ $except ];

        foreach( $except as $field_key )
            unset($fields[$field_key]);

        return $fields;

    }


    /**
     * Получить список только тех полей, которые переданы
     * @throws Exception
     */
    public function getModelFieldsOnly( array|string $only, string|object $key = '' ): array
    {

        $fields = $this->getModelFieldsFromJSON($key);

        if( is_string($only) )
            $only = [ $only ];

        $return_fields = [];

        foreach( $only as $field_key )
            $return_fields[$field_key] = $fields[$field_key];

        return $return_fields;

    }



    /**
     * Возвращает путь к модели
     */
    public function getModelClass(): string
    {

        $this->modelKeyCheck();

        return 'App\\Models\\'. str($this->model_key)->replace('/', '\\')->toString();

    }


    /**
     * Установить ключ модели
     */
    public function setModelKey( string|object|null $key ): string
    {

        $this->model_key_now_set_automatically = false;

        return $this->model_key = $this->getModelKey($key ?? '');

    }


    /**
     * Отчистить ключ модели, который был вручную установлен
     */
    public function clearModelKey(): void
    {

        if( !$this->model_key_now_set_automatically )
            $this->model_key = null;

    }



    /**
     * Возвращает event name
     */
    public function getEventName( string $event, string|object $key = '' ): string
    {

        $key = $this->getModelKey($key);

        if( !array_key_exists($key, $this->events_name) ){

            $event_name = str($key)->explode('/')->toArray();
            $event_name = array_pop($event_name);
            $event_name = str($event_name)->singular()->snake()->lower();

            $this->events_name[$key] = $event_name;

        }

        return $this->events_name[$key].'.'.$event;

    }


}
