<?php

namespace App\Console\Commands\Revered;

use App\Extra\Helpers\HelperClass;
use Illuminate\Console\Command;
use Illuminate\Support\Str;


/**
 * Данный класс нужен исключительно для разработки и поддержки проекта.
 * Функционал для самого проекта здесь не прописывается.
 * Это авторская разработка, права принадлежат revered.pro
 */
class ReveredHelpersRefresh extends Command
{

    use ReveredConsoleTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revered:helperRefresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить функции хелпера.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        $helperClass = HelperClass::getInstance();

        $reflection = new \ReflectionClass($helperClass);
        $methods_class = $reflection->getMethods();

        $methods = [];

        foreach( $methods_class as $method_class ){

            if( !$method_class->isPublic() )
                continue;

            $method_name = $method_class->getName();

            if( in_array($method_name,['__construct', 'getInstance', '__clone', '__wakeup']) )
                continue;

            $method = [
                'name' => $method_name,
                'params' => $method_class->getParameters(),
                'return_type' => $method_class->getReturnType()
            ];

            $methods[] = $method;

        }

        $codeHelpersPHP = $this->getCodeHelpersPHP();

        foreach( $methods as $method ){

            $params = '';
            $params_without_defaults = '';

            if( count($method['params']) ){

                $params = [];
                $params_without_defaults = [];
                foreach( $method['params'] as $param ){

                    $param_str = '$'.$param->name;

                    if( $param->hasType() ){

                        if( $param->getType() instanceof \ReflectionUnionType ){

                            $str = [];
                            foreach( $param->getType()->getTypes() as $t ){
                                $str[] = $t->getName();
                            }
                            $str = implode('|', $str);

                            $param_str = $str.' '.$param_str;

                        }else{

                            $param_str2 = $param->getType()->getName();

                            if( $param->allowsNull() && $param_str2 != 'mixed' )
                                $param_str2 = '?'.$param_str2;

                            $param_str = $param_str2.' '.$param_str;

                        }

                    }

                    if( $param->isOptional() )
                        $param_str .= ' = '.json_encode($param->getDefaultValue());

                    $params[] = $param_str;
                    $params_without_defaults[] = '$'.$param->name;
                }

                $params = ' '.implode(', ', $params).' ';
                $params_without_defaults = ' '.implode(', ', $params_without_defaults).' ';

            }

            if( $method['return_type'] ){

                $return_type = ' : '.$method['return_type'];

            }else{

                $return_type = '';

            }

            if( $method['return_type'] == 'void' || $method['return_type'] == 'never' ){

                $return_fnc = '';

            }else{

                $return_fnc = 'return ';

            }

            $codeHelpersPHP .= '


    if( helperFunctionCheck(\''.$method['name'].'\') ){

        function '.$method['name'].'('.$params.')'.$return_type.'
        {
            '.$return_fnc.'helperClass()->'.$method['name'].'('.$params_without_defaults.');
        }

    }
';

        }


        //Добавляем функции сервисов
        $services_PHP = $this->scanServices();

        if( $services_PHP != '' ){
            $codeHelpersPHP .= '






    /**
    * Функции Services
    */
';
            $codeHelpersPHP .= $services_PHP;
        }



        //Сохраняем код
        devTools()->saveFile('/app/Extra/Helpers/Helpers.php', $codeHelpersPHP);

        $this->print('Helpers.php successfully updated.');

        return self::SUCCESS;

    }


    /*
     * Генерация кода для сервисов. Сканирует папку Services
     */
    protected array $services_functions = [];
    public function scanServices( $services_PHP = '', $dir = false): string
    {

        if( !$dir )
            $dir = 'Services';


        //Получаем список названий функций, которые прописаны в текущем Helpers.php
        preg_match_all('/function (\w+)/', devTools()->getFile(app_path().'/Extra/Helpers/Helpers.php'), $current_functions);
        $current_functions = $current_functions[1];


        //Добавляем сервисы (сперва основную папку сканируем)
        $services = scandir(  app_path($dir) );
        foreach( $services as $service ){

            if( $service == '.' || $service == '..' || $service == 'Service.php' )
                continue;

            $deep_dir = $dir.'/'.$service;
            if( is_dir(app_path($deep_dir)) ){
                continue;
            }

            $service_name = str_replace('.php', '',  $service);
            $function_name = lcfirst($service_name);

            //Пытаемся из названия функции убрать Service в конце, для краткости
            if( Str::endsWith($function_name,'Service') ){

                $function_name_without_service = mb_substr($function_name,0,-7);

                //Если такой функции нет и такая функция есть, но она прописана в Helpers.php
                if( !function_exists($function_name_without_service) || in_array($function_name_without_service, $current_functions) )
                    $function_name = $function_name_without_service;


            //Если название не заканчивается на Service - то пропускаем этот файл, его не нужно добавлять в Helpers.php
            }else{

                continue;

            }

            $service_instance_var = '$'.Str::snake($service_name).'_instance';
            $service_path = '\app\\'.str_replace('/','\\', $dir).'\\'.$service_name;

            //имя класса должно начинаеться на App в отличии от пути, который начинается на app
            $class_name = preg_replace('/\\\app/', '\App', $service_path);

            $service_reflection = new \ReflectionClass($class_name);
            $service_construct_params_str = '';
            $service_construct_params_str_without_defaults = '';
            $service_construct_params_array = [];
            $service_construct_params_array_without_defaults = [];

            foreach( $service_reflection->getMethods() as $service_method ) {

                if( $service_method->getName() == '__construct' ){

                    foreach( $service_method->getParameters() as $service_param ) {

                        $param_str = '$'.$service_param->name;

                        if( $service_param->hasType() )
                            $param_str = $service_param->getType()->getName().' '.$param_str;

                        if( $service_param->isOptional() )
                            $param_str .= ' = '.json_encode($service_param->getDefaultValue());

                        $service_construct_params_array[] = $param_str;
                        $service_construct_params_array_without_defaults[] = '$'.$service_param->name;

                    }

                    if( count($service_construct_params_array) ){
                        $service_construct_params_str = implode(', ', $service_construct_params_array).', ';
                        $service_construct_params_str_without_defaults = implode(', ', $service_construct_params_array_without_defaults);
                    }

                }

            }

            if( in_array( $function_name, $this->services_functions) ){
                $name_dir = explode('/',$dir);
                $name_dir = array_pop($name_dir);
                $function_name = Str::camel($name_dir.'_'.$function_name);
            }

            $this->services_functions[] = $function_name;

            $services_PHP .= '
    if( helperFunctionCheck(\''.$function_name.'\') ){

        /**
        * @param bool $refresh
        * @return '.$class_name.'
        */
        function '.$function_name.'( '.$service_construct_params_str.'bool $refresh = false ) : '.$class_name.'
        {
            static '.$service_instance_var.' = false;

            if( $refresh )
                '.$service_instance_var.' = false;

            if( !'.$service_instance_var.' )
                '.$service_instance_var.' = new '.$class_name.'('.$service_construct_params_str_without_defaults.');

            return '.$service_instance_var.';
        }

    }
';

        }

        //Теперь сканируем вложенные папки
        foreach( $services as $service ) {

            if ($service == '.' || $service == '..' || $service == 'Service.php')
                continue;

            $deep_dir = $dir . '/' . $service;
            if (is_dir(app_path($deep_dir))) {
                $services_PHP = $this->scanServices($services_PHP, $deep_dir);
            }

        }


        //Сканируем DevTools Services
        if( $dir == 'Services' )
            $services_PHP = $this->scanServices($services_PHP, 'Extra/DevTools/Services');


        return $services_PHP;

    }


    /*
     * Получить шаблон кода для Helpers.php
     */
    private function getCodeHelpersPHP(): string
    {

        return '<?php


    /**
     *  ВНИМАНИЕ! В этом файле функции не стоит добавлять вручную. Этот файл автоматически генерируются
     *  с помощью команды php artisan revered:helperRefresh и изменения в данном файле могут быть перетерты.
     *  Добавляйте нужные функции в файл  App\Extra\Helpers\HelperClass.php
     *  и далее запускайте команду php artisan revered:helperRefresh
     *  Функции-хелперы для сервисов формируются автоматически, их не нужно добавлять в класс HelperClass
     */






    use App\Extra\Helpers\HelperClass;


    /**
     *  Поверка существования функции
     */
    function helperFunctionCheck( $func ): bool
    {

        if( function_exists($func) )
            dd("The helper function \'$func\' already exists.");

        return true;
    }


    /**
    *   Инициализация хелпера
    */
    if( helperFunctionCheck(\'helperClass\') ){

        function helperClass() : HelperClass
        {
            return HelperClass::getInstance();
        }

    }
    ';

    }

}
