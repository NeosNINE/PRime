<?php

namespace App\Extra\DevTools\Services\Traits;

trait Models
{

    /**
     * Получить список всех моделей
     */
    public function getModels( $models = [], $path = 'Models' ){

        foreach( scandir( app_path($path)) as $file_path ){

            if( $file_path == '.' || $file_path == '..' )
                continue;

            $full_file_path = app_path($path.'/'.$file_path);

            if( is_dir($full_file_path) ){

                $models = $this->getModels($models, $path.'/'.$file_path);

            }else{

                if( str($file_path)->endsWith('.json') || str($file_path)->endsWith('Trait.php') || !str($file_path)->endsWith('.php') )
                    continue;

                $models[] = 'App/'.$path.'/'.$file_path;

            }

        }

        return $models;

    }


    /**
     * Get Models Info
     */
    public function getModelsInfo( string $path = 'Models' ): array
    {

        $models = [];

        foreach( $this->getModels() as $model ){

            $model_name = explode('/', $model);
            $model_name_file = array_pop($model_name);
            $model_name = str($model_name_file)->replace('.php', '')->toString();

            $service_name = str($model_name)->plural().'Service';
            $service_path = str($model)->replace('App/Models/', 'App/Services/')->replace($model_name_file, $service_name)->toString();
            $service_path = str($service_path)->replace('/', '\\')->toString();

            try {

                $service = new $service_path;

            } catch ( \Throwable $throwable ){

                $service_name = str($model_name)->singular().'Service';
                $service_path = str($service_path)->replace(str($model_name)->plural().'Service', $service_name)->toString();

                try {

                    $service = new $service_path;

                } catch ( \Throwable $throwable ){

                    $service = null;

                }

            }

            $data = [
                'name' => $model_name,
                'service' => $service
            ];


            if( $service ){

                $reflection = new \ReflectionClass($service);

                if( $reflection->getParentClass() && str($reflection->getParentClass()->getName())->endsWith('Service') ){

                    if( method_exists($service, 'getEventName') )
                        $data['events_name'] = [
                            $service->getEventName('add'),
                            $service->getEventName('edit'),
                            $service->getEventName('delete')
                        ];


                    if( method_exists($service, 'getModelFieldsFromJSON') )
                        $data['fields'] = $service->getModelFields();


                    if( method_exists($service, 'getModelRelations') )
                        $data['relations'] = $service->getModelRelations();


                }else{

                    $data['not_has_extends'] = true;

                }


            }


            $models[] = $data;

        }

        return $models;

    }

}
