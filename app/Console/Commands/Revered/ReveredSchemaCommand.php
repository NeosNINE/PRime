<?php

namespace App\Console\Commands\Revered;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReveredSchemaCommand extends Command
{

    use ReveredConsoleTrait;

    protected $signature = 'revered:schema';

    protected $description = 'Обновляет Models Schema на основе драйвера DB (подключается к базе данных)';

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function handle()
    {

        if( config('env.db.database') === null )
            throw new \Exception('Значение env DB_DATABASE = null. Возможно, нужно просто почистить cache: artisan config:clear');

        $something_updated = false;
        $logs = [];

        try {

            $models = devTools()->getModels();

            foreach( $models as $model_path ){

                if( !str($model_path)->endsWith('.php') )
                    continue;

                $model = app(str_replace(['/', '.php'], ['\\', ''], $model_path));

                $schema_json_path = str_replace(['/Models/', '.php'], ['/Models/Schema/', '.json'], $model_path);

                if( count($logs) )
                    $logs[] = '';

                $logs[] = $model_path.' : '.$schema_json_path;

                $current_json_data = [
                    'fields' => [],
                    'relations' => []
                ];

                $operation = 'created';
                if( devTools()->fileExists($schema_json_path) ){

                    $operation = 'updated';
                    $current_json_data = json_decode(devTools()->getFile($schema_json_path), true);

                }

                $new_json_data = $current_json_data;

                $fields = $new_json_data['fields'] ?? [];
                $relations = $new_json_data['relations'] ?? [];

                $columns_to_delete = array_keys($current_json_data['fields'] ?? []);
                $columns_to_delete = array_flip($columns_to_delete);


                $columns = Schema::getColumnListing($model->getTable());
                foreach ( $columns as $column ){

                    $logs[] = ' - field: '.$column;

                    unset($columns_to_delete[$column]);

                    $column_info = DB::table('INFORMATION_SCHEMA.COLUMNS')
                        ->where('TABLE_SCHEMA', config('env.db.database'))
                        ->where('TABLE_NAME', $model->getTable())
                        ->where('COLUMN_NAME', $column)
                        ->first();

                    if( !$column_info || !isset($column_info->DATA_TYPE) ){

                        $this->error('Не удалось получить информацию из таблицы: '.$model->getTable());
                        exit;

                    }

                    $fields[$column]['type'] = $column_info->DATA_TYPE;
                    $fields[$column]['default'] = $column_info->COLUMN_DEFAULT;
                    $fields[$column]['is_nullable'] = $column_info->IS_NULLABLE == 'YES';

                    if( !is_null($column_info->CHARACTER_MAXIMUM_LENGTH) )
                        $fields[$column]['max_length'] = $column_info->CHARACTER_MAXIMUM_LENGTH;

                    if( !is_null($column_info->NUMERIC_PRECISION) )
                        $fields[$column]['numeric_precision'] = $column_info->NUMERIC_PRECISION;

                    if( !is_null($column_info->NUMERIC_SCALE) )
                        $fields[$column]['numeric_scale'] = $column_info->NUMERIC_SCALE;

                }

                //Удаляем колонки, которых уже нет в БД
                if( count($columns_to_delete) > 0 ){

                    foreach( array_flip($columns_to_delete) as $column_to_delete ) {
                        unset($fields[$column_to_delete]);
                    }

                }

                //Получаем связи с другими моделями
                $reflection = new \ReflectionClass($model);

                $full_model_code = devTools()->getCodeFromFile($model_path);

                preg_match_all("/use\s(.*?);".devTools()->getEOL()."/", $full_model_code, $model_uses);
                $model_uses = $model_uses[1];

                foreach ( $reflection->getMethods() as $method ){


                    if( !$method->hasReturnType() )
                        continue;

                    $return_type = $method->getReturnType();

                    try {

                        $return_type_name = $return_type->getName();

                    } catch ( \Throwable $throwable ){


                        $return_type_name = '';

                    }

                    if( str($return_type_name)->startsWith('Illuminate\Database\Eloquent\Relations\\') ){

                        $logs[] = ' - relation method: ' . $method->getName();

                        $code = devTools()->getCodeFromFile($model_path, $method->getStartLine(), $method->getEndLine());

                        if( !$code )
                            error('Can not get code form file: '. $model_path);

                        preg_match_all("/function (.*?)\(/s", $code, $matches);

                        if( !isset($matches[1][0]) )
                            error('Can not get model func from matches: '.print_r($matches, true));

                        $model_func = trim($matches[1][0]);

                        preg_match_all("/return(.*?);/s", $code, $matches);

                        $relation_return = trim($matches[1][0]);
                        $relation_return = explode('->', $relation_return);
                        $relation_return = $relation_return[1] ?? '';

                        if( str($relation_return)->endsWith(';') )
                            $relation_return = mb_substr($relation_return, 0, -1);

                        $relation_type = explode('(', $relation_return)[0];

                        preg_match_all("/\((.*?)\)/s", $relation_return, $matches);

                        $relation_return_args = explode(',', $matches[1][0]);

                        $relation_model = explode('::', $relation_return_args[0])[0];
                        $relation_model = str($relation_model)->trim()->replace(["'", '"', '(', ')', ';'], '')->toString();

                        foreach( $model_uses as $use ){

                            if( str($use)->startsWith('App\Models') && str($use)->endsWith($relation_model) )
                                $relation_model = $use;

                        }

                        if( !str($relation_model)->startsWith('App\Models') )
                            $relation_model = str($relation_model)->start($reflection->getNamespaceName().'\\');

                        $relation_model = str($relation_model)->start('App\\Models\\')->substr(11);

                        $relation_service = 'App\\Services\\'.str($relation_model)->plural().'Service';

                        $relation_service_two_arr = explode('\\', $relation_model);
                        $relation_service_two = array_pop($relation_service_two_arr);
                        $relation_service_two = str($relation_service_two)->substr(0, mb_strlen(str($relation_service_two)->snake()->explode('_')->first()));

                        $relation_service_two_arr[] = $relation_service_two;
                        $relation_service_two = implode('\\', $relation_service_two_arr);
                        $relation_service_three = 'App\\Services\\'.str($relation_service_two)->singular().'Service';
                        $relation_service_two = 'App\\Services\\'.str($relation_service_two)->plural().'Service';

                        $relation_service_path = str($relation_service)->replace('\\', '/');
                        $relation_service_path_two = str($relation_service_two)->replace('\\', '/');
                        $relation_service_path_three = str($relation_service_three)->replace('\\', '/');

                        $relation_service_path_found = devTools()->fileExists($relation_service_path.'.php');
                        $relation_service_path_two_found = devTools()->fileExists($relation_service_path_two.'.php');
                        $relation_service_path_three_found = devTools()->fileExists($relation_service_path_three.'.php');

                        if(
                            $relation_service_path_found
                            || $relation_service_path_two_found
                            || $relation_service_path_three_found
                        ){

                            if( $relation_service_path_three_found ){

                                $relation_service = $relation_service_three;

                            }elseif( $relation_service_path_two_found ){

                                $relation_service = $relation_service_two;

                            }

                            $relation_service_func_explode = explode('\\', str($relation_service)->substr(0, -7));
                            $relation_service_func = array_pop($relation_service_func_explode);

                            $relation_service_func = str($relation_service_func)->camel()->toString();

                            if( !function_exists($relation_service_func) ){

                                $relation_service_func = str(array_pop($relation_service_func_explode).'_'.$relation_service_func)->camel();
                                $this->print($relation_service_func);

                                if( !function_exists($relation_service_func) ){
                                    $relation_service_func = null;
                                }

                            }


                        }else{

                            $relation_service = null;
                            $relation_service_func = null;

                        }

                        $already_contains = false;

                        foreach( $relations as $rel ){

                            if( $rel['type'] == $relation_type && $rel['to'] == $relation_model ){
                                $already_contains = true;
                                break;
                            }

                        }

                        if( !$already_contains ) {

                            $data = [
                                'type' => $relation_type,
                                'to' => $relation_model,
                                'model_func' => $model_func,
                                'service' => $relation_service,
                                'service_func' => $relation_service_func
                            ];

                            $relations[] = $data;

                        }

                    }


                }


                $new_json_data['fields'] = $fields;
                $new_json_data['relations'] = $relations;

                $current_json = json_encode($current_json_data, JSON_PRETTY_PRINT);
                $new_json = json_encode($new_json_data, JSON_PRETTY_PRINT);

                if( $current_json != $new_json ){

                    $schema_json_path_dir = explode('/',$schema_json_path);
                    unset($schema_json_path_dir[count($schema_json_path_dir)-1]);
                    $schema_json_path_dir = base_path(implode('/',$schema_json_path_dir));

                    if( !is_dir($schema_json_path_dir) )
                        mkdir($schema_json_path_dir, 0777, true);


                    devTools()->saveFile($schema_json_path, $new_json);

                    $this->print($operation.': '.str_replace('App/Models/Schema/', '', $schema_json_path));

                    $something_updated = true;

                }


            }


            if( $something_updated ) {

                $this->print(''); //перенос строки
                $this->print('Schema successfully updated.');

            }else{

                $this->print('Nothing to update.');

            }

            return Command::SUCCESS;


        } catch ( \Throwable $throwable ){

            report($throwable);

            foreach ( $logs as $log_line ){

                $this->print($log_line);

            }

            $this->print($throwable->getMessage().' in '.$throwable->getFile().':'.$throwable->getLine(), 'error');

            return Command::FAILURE;

        }

    }

}
