<?php

namespace App\Console\Commands\Revered;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReveredGenerateModuleCommand extends Command
{
    protected $signature = 'revered:generate_module';

    protected $description = 'Команда генерирует файлы для новой сущности: сервис, шаблоны, роуты, контроллер в админке и т.п.';

    public string $singular_en;
    public string $plural_en;
    public string $singular_ru_i;
    public string $singular_ru_r;
    public string $singular_ru_d;
    public string $singular_ru_v;
    public string $singular_ru_t;
    public string $singular_ru_p;
    public string $plural_ru_i;
    public string $plural_ru_r;
    public string $plural_ru_d;
    public string $plural_ru_v;
    public string $plural_ru_t;
    public string $plural_ru_p;

    public array $fails = [];

    /**
     * @var \Eloquent
     */
    public $model;
    public string $model_php_class_path;
    public array $model_columns;

    public bool $need_archive_model = false;

    public function getCache( string $key, $default = null ){

        return Cache::get('ReveredGenerateModuleCommand'.$key, $default);

    }

    public function setCache( string $key, mixed $value ): bool
    {

        return Cache::set('ReveredGenerateModuleCommand'.$key, $value, now()->addDay());

    }

    public function handle(): void
    {

        try {

            $this->singular_en = $this->ask('Введите название сущности в ед. числе на английском. Пример [User]', $this->getCache('singular_en'));

            $this->singular_en = str($this->singular_en)->singular()->camel()->ucfirst();
            $this->plural_en = str($this->singular_en)->plural();

            $this->setCache('singular_en', $this->singular_en);
            $this->setCache('plural_en', $this->plural_en);

            //TODO добавить проверку на существование модели
            $model = false;

            foreach( devTools()->getModels() as $m ){

                if( str($m)->endsWith('/'.$this->singular_en.'.php') )
                    $model = $m;

            }

            $this->model_php_class_path = str($model)->replace('.php', '')->replace('/', '\\');

            if( !$model ){
                $this->error('You should create model first: App/Models/'.$this->singular_en.'.php');
                return;
            }

            $this->model = new (str($model)->replace('.php', '')->replace('/', '\\')->toString());
            $this->model_columns = Schema::getColumnListing($this->model->getTable());

            if( !count($this->model_columns) ){

                $this->error('You should run migration first. Because there are no columns in DB table: '.$this->model->getTable());

                if( !$this->confirm('Do you want to run artisan:migrate?', true) )
                    return;

                $this->call('migrate');

                $this->model_columns = Schema::getColumnListing($this->model->getTable());

            }


            $this->singular_ru_i = $this->ask('Ед. число. есть Кто? Что? [Пользователь]', $this->getCache('singular_ru_i', $this->singular_en));
            $this->singular_ru_r = $this->ask('Ед. число. нет Кого? Чего? [Пользователя]', $this->getCache('singular_ru_r', $this->singular_en));
            $this->singular_ru_d = $this->ask('Ед. число. даю Кому? Чему? [Пользователю]', $this->getCache('singular_ru_d', $this->singular_en));
            $this->singular_ru_v = $this->ask('Ед. число. вижу Кого? Чего? [Пользователя]', $this->getCache('singular_ru_v', $this->singular_en));
            //$this->singular_ru_t = $this->ask('Ед. число. горжусь Кем? Чем? [Пользователем]', $this->singular_en);
            //$this->singular_ru_p = $this->ask('Ед. число. думаю О ком? О чем? [Пользователе]', $this->singular_en);

            $this->plural_ru_i = $this->ask('Мн. число. есть Кто? Что? [Пользователи]', $this->getCache('plural_ru_i', $this->plural_en));
            $this->plural_ru_r = $this->ask('Мн. число. нет Кого? Чего? [Пользователей]', $this->getCache('plural_ru_r', $this->plural_en));
            //$this->plural_ru_d = $this->ask('Мн. число. даю Кому? Чему? [Пользователям]', $this->plural_en);
            //$this->plural_ru_v = $this->plural_ru_r;
            //$this->plural_ru_t = $this->ask('Мн. число. горжусь Кем? Чем? [Пользователями]', $this->plural_en);
            //$this->plural_ru_p = $this->ask('Мн. число. думаю О ком? О чем? [Пользователях]', $this->plural_en);

            $this->singular_ru_i = str($this->singular_ru_i)->lower()->ucfirst();
            $this->singular_ru_r = str($this->singular_ru_r)->lower()->ucfirst();
            $this->singular_ru_d = str($this->singular_ru_d)->lower()->ucfirst();
            $this->singular_ru_v = str($this->singular_ru_v)->lower()->ucfirst();
            //$this->singular_ru_t = str($this->singular_ru_t)->lower()->ucfirst();
            //$this->singular_ru_p = str($this->singular_ru_p)->lower()->ucfirst();

            $this->plural_ru_i = str($this->plural_ru_i)->lower()->ucfirst();
            $this->plural_ru_r = str($this->plural_ru_r)->lower()->ucfirst();
            //$this->plural_ru_d = str($this->plural_ru_d)->lower()->ucfirst();
            //$this->plural_ru_v = str($this->plural_ru_v)->lower()->ucfirst();
            //$this->plural_ru_t = str($this->plural_ru_t)->lower()->ucfirst();
            //$this->plural_ru_p = str($this->plural_ru_p)->lower()->ucfirst();

            $this->setCache('singular_ru_i', $this->singular_ru_i);
            $this->setCache('singular_ru_r', $this->singular_ru_r);
            $this->setCache('singular_ru_d', $this->singular_ru_d);
            $this->setCache('singular_ru_v', $this->singular_ru_v);

            $this->setCache('plural_ru_i', $this->plural_ru_i);
            $this->setCache('plural_ru_r', $this->plural_ru_r);


            $need_archive_model = $this->ask('Нужна ли возможность архивировать сущность в админке? [yes, no]', $this->getCache('need_archive_model', 'no'));
            $need_archive_model = str($need_archive_model)->lower()->trim();

            if( $need_archive_model == 'yes' || $need_archive_model == 'y' ){

                $this->need_archive_model = true;
                $this->setCache('need_archive_model', 'yes');

            }else{

                $this->need_archive_model = false;
                $this->setCache('need_archive_model', 'no');

            }

            $this->addRolesRule();
            $this->addAdminJsFiles();
            $this->addAdminViews();
            $this->addLinkToAdminNav();
            $this->changeAdminRoutes();
            $this->createService();
            $this->createAdminController();

            if( count($this->fails) ){

                foreach( $this->fails as $fail )
                    $this->warn($fail);

            }else{

                $this->info('Successful! Go do magic code.');

            }


            $this->call('revered:helperRefresh');
            $this->call('revered:schema');


        } catch ( \Throwable $e) {

            $this->error($e->getMessage(). ' in '.$e->getFile().':'.$e->getLine());

        }

    }

    private function addRolesRule(){

        $path = app_path('Services/System/RolesService.php');

        $code = devTools()->getFile($path);
        $lines = explode("\n", $code);

        $plural_en_lower = str($this->plural_en)->snake();

        if( str($code)->contains("\$accesses['".$plural_en_lower."'] =") ){
            $this->fails[] = 'roles rule already exists: app/Services/System/RolesService.php';
            return false;
        }

        $new_code = '';
        $next_insert_1 = false;
        $next_insert_2 = false;
        $inserted = false;


        foreach ( $lines as $line ){

            $new_code .= $line."\n";

            if( $next_insert_1 && $next_insert_2 ){

                if( str($line)->trim() == ']);' ){

                    $new_code .= "
        \$accesses['".$plural_en_lower."'] = \$this->getBREADAccesses('".$this->plural_ru_i."', '".$plural_en_lower."', [
            'browse' => 'Просмотр списка ".str($this->plural_ru_r)->lower()."',
            'add' => 'Добавление новых ".str($this->plural_ru_r)->lower()."',
            'edit' => 'Редактирование ".str($this->plural_ru_r)->lower()."',
            'delete' => 'Удаление ".str($this->plural_ru_r)->lower()."'
        ]);
";

                    $next_insert_1 = false;
                    $next_insert_2 = false;
                    $inserted = true;

                }

            }

            if( str($line)->contains("public function getAccesses(") && !$inserted )
                $next_insert_1 = true;

            if( str($line)->contains("\$accesses['users'] =") && !$inserted )
                $next_insert_2 = true;

        }

        if( !$inserted ){

            $this->fails[] = 'can not insert roles rule link: app/Services/System/RolesService.php';
            return false;

        }


        if( devTools()->saveFile($path, $new_code) ) {

            $this->info('updated: app/Services/System/RolesService.php');

        }else{

            $this->fails[] = 'can not update: app/Services/System/RolesService.php';

        }


    }

    private function addAdminJsFiles(){

        $singular_en_lower = str($this->singular_en)->snake();
        $plural_en_lower = str($this->plural_en)->snake();

        $path = base_path('resources/js/admin/pages/'.$plural_en_lower.'.js');
        $path_public = base_path('public/assets/admin/js/pages/'.$plural_en_lower.'.js');

        if( devTools()->fileExists($path) ){
            $this->fails[] = 'file already exist: resources/js/admin/pages/'.$plural_en_lower.'.js';
            return;
        }

        $js_code = "

    /**
     * Устанавливаем стандартные события для ".str($this->plural_ru_r)->lower()."
     */
    setDefaultObjectEvents('".$singular_en_lower."', '".$plural_en_lower."', '.".$plural_en_lower."-table', 'id');";



        $public_js_code = "/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************************!*\
  !*** ./resources/js/admin/pages/".$plural_en_lower.".js ***!
  \*********************************************/
".$js_code."
/******/ })()
;";

        $mix_manifest = devTools()->getFile(base_path('public/mix-manifest.json'));
        if( $mix_manifest ){

            $mix_manifest = json_decode($mix_manifest, true);
            $mix_manifest["/assets/admin/js/pages/".$plural_en_lower.".js"] = "/assets/admin/js/pages/".$plural_en_lower.".js??id=".md5(mktime(true).rand());
            $mix_manifest = json_encode($mix_manifest, JSON_PRETTY_PRINT);

            if( devTools()->saveFile(base_path('public/mix-manifest.json'), $mix_manifest) ) {

                $this->info('updated: public/mix-manifest.json');

            }else{

                $this->fails[] = 'can not update: public/mix-manifest.json';

            }

        }


        if( devTools()->saveFile($path, $js_code) ) {

            $this->info('created: resources/js/admin/pages/'.$plural_en_lower.'.js');

        }else{

            $this->fails[] = 'can not create: resources/js/admin/pages/'.$plural_en_lower.'.js';

        }

        if( devTools()->saveFile($path_public, $public_js_code) ) {

            $this->info('created: public/assets/admin/js/pages/'.$plural_en_lower.'.js');

        }else{

            $this->fails[] = 'can not create: public/assets/admin/js/pages/'.$plural_en_lower.'.js';

        }

    }


    private function addLinkToAdminNav(){

        $path = app_path('Services/System/AdminService.php');

        $code = devTools()->getFile($path);
        $lines = explode("\n", $code);

        $singular_en_lower = str($this->singular_en)->snake();
        $plural_en_lower = str($this->plural_en)->snake();

        if( str($code)->contains("route('admin.".$plural_en_lower.".") ){
            $this->fails[] = 'admin link already exists: app/Services/System/AdminService.php';
            return false;
        }

        $new_code = '';
        $next_insert = false;
        $inserted = false;

        foreach ( $lines as $line ){

            $new_code .= $line."\n";

            if( $next_insert ){

                if( str($line)->trim() == '],' ){

                    $new_code .= "            [
                'text' => '".$this->plural_ru_i."',
                'href' => route('admin.".$plural_en_lower.".browse'),
                'active_route_prefix' => 'admin.".$singular_en_lower."',
                'icon' => 'fa fa-".$plural_en_lower."',
                'access' => '".$plural_en_lower.".*'
            ],\n";

                    $next_insert = false;
                    $inserted = true;

                }

            }

            if( str($line)->contains("'href' => route('admin.users.browse'),") && !$inserted )
                $next_insert = true;

        }

        if( !$inserted ){

            $this->fails[] = 'can not insert admin nav link: app/Services/System/AdminService.php';
            return false;

        }


        if( devTools()->saveFile($path, $new_code) ) {

            $this->info('updated: app/Services/System/AdminService.php');

        }else{

            $this->fails[] = 'can not update: app/Services/System/AdminService.php';

        }


    }

    private function changeAdminRoutes(){

        $path = base_path('routes/includes/admin.php');

        $code = devTools()->getFile($path);
        $lines = explode("\n", $code);

        $singular_en_lower = str($this->singular_en)->snake();
        $plural_en_lower = str($this->plural_en)->snake();

        $singular_en_route_path = str($singular_en_lower)->replace('_', '-');
        $plural_en_route_path = str($plural_en_lower)->replace('_', '-');

        if( str($code)->contains("Route::name('".$plural_en_lower.".')") ){
            $this->fails[] = 'admin routes already exists: routes/includes/admin.php';
            return false;
        }

        $new_code = '';
        $next_insert = false;
        $inserted = false;

        $restore_route = '';
        if( $this->need_archive_model ){

            $restore_route = "
        Route::post('".$singular_en_route_path."/restore/{".$singular_en_lower."_id}', 'restore')->name('restore');";

        }

        foreach ( $lines as $line ){

            $new_code .= $line."\n";

            if( $next_insert ){

                if( str($line)->trim() == '});' ){

                    $new_code .= "\n\n";

                    $new_code .= "
    //".$this->plural_ru_i."
    Route::name('".$plural_en_lower.".')->controller(Admin".$this->plural_en."Controller::class)->group(function(){
        Route::get('".$plural_en_route_path."', 'browse')->name('browse');
        Route::get('".$singular_en_route_path."/add', 'add')->name('add');
        Route::post('".$singular_en_route_path."/add', 'addSave')->name('add.save');
        Route::get('".$singular_en_route_path."/edit/{".$singular_en_lower."}', 'edit')->name('edit');
        Route::put('".$singular_en_route_path."/edit/{".$singular_en_lower."}', 'editSave')->name('edit.save');
        Route::delete('".$singular_en_route_path."/delete/{".$singular_en_lower."}', 'delete')->name('delete');
        Route::get('".$singular_en_route_path."/{".$singular_en_lower."}', 'read')->name('read');".$restore_route."
    });
    ";

                    $new_code .= "\n";

                    $next_insert = false;
                    $inserted = true;

                }

            }

            if( str($line)->contains("Route::prefix('settings')") && !$inserted )
                $next_insert = true;

            if( str($line)->contains("use App\Http\Controllers\Admin\AdminUsersController;") && !str($code)->contains("use App\Http\Controllers\Admin\Admin".$this->plural_en."Controller;") ) {
                $new_code .= "use App\Http\Controllers\Admin\Admin".$this->plural_en."Controller;\n";
            }

        }

        if( !$inserted ){

            $this->fails[] = 'can not insert routes: routes/includes/admin.php';
            return false;

        }


        if( devTools()->saveFile($path, $new_code) ) {

            $this->info('updated: routes/includes/admin.php');

        }else{

            $this->fails[] = 'can not update: routes/includes/admin.php';

        }


    }

    private function createAdminController(){

        $name = 'Admin'.$this->plural_en.'Controller';

        $path = app_path('Http/Controllers/Admin/'.$name.'.php');

        if( devTools()->fileExists($path) ){
            $this->fails[] = 'file already exist: app/Http/Controllers/Admin/'.$name.'.php';
            return;
        }

        $singular_en_lower = str($this->singular_en)->snake();
        $plural_en_lower = str($this->plural_en)->snake();

        $service_func = str($plural_en_lower)->camel()->lcfirst();

        $restore_func = '';
        if( $this->need_archive_model ){

            $restore_func = "


    /**
     * Восстановить ".str($this->singular_ru_v)->lower()."
     * @throws \Throwable
     * @throws \Throwable
     */
    public function restore( $".$singular_en_lower."_id ): ".$this->singular_en."
    {
        roles()->checkAccessWithAbort('".$plural_en_lower.".delete');

        return ".$service_func."()->restore( $".$singular_en_lower."_id );

    }


";

        }

        $code = '<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use '.$this->model_php_class_path.';
use Illuminate\Http\Request;
use Illuminate\View\View;

class '.$name.' extends Controller
{


    /**
     * Просмотр списка '.str($this->plural_ru_r)->lower().'
     * @throws \Exception
     */
    public function browse( Request $request ): View
    {

        roles()->checkAccessWithAbort(\''.$plural_en_lower.'.browse\');

        return view(\'admin.app.'.$plural_en_lower.'.browse\', [
            \''.$plural_en_lower.'\' => '.$service_func.'()->get( $request )
        ]);

    }



    /**
     * Добавить '.str($this->singular_ru_v)->lower().' (Форма)
     */
    public function add(): View
    {

        roles()->checkAccessWithAbort(\''.$plural_en_lower.'.add\');

        return view(\'admin.app.'.$plural_en_lower.'.add\');

    }


    /**
     * Добавить '.str($this->singular_ru_v)->lower().' (Сохранить)
     * @throws \Throwable
     */
    public function addSave( Request $request ): '.$this->singular_en.'
    {

        roles()->checkAccessWithAbort(\''.$plural_en_lower.'.add\');

        return '.$service_func.'()->add( $request );

    }



    /**
        Просмотр '.str($this->singular_ru_r)->lower().'
    */
    public function read( '.$this->singular_en.' $'.$singular_en_lower.' ): View
    {

        roles()->checkAccessWithAbort(\''.$plural_en_lower.'.read\');

        return view(\'admin.app.'.$plural_en_lower.'.read\', [
            \''.$singular_en_lower.'\' => $'.$singular_en_lower.'
        ]);

    }



    /**
        Изменить '.str($this->singular_ru_v)->lower().' (HTML шаблон)
    */
    public function edit( '.$this->singular_en.' $'.$singular_en_lower.' ): View
    {

        roles()->checkAccessWithAbort(\''.$plural_en_lower.'.edit\');

        return view(\'admin.app.'.$plural_en_lower.'.edit\', [
            \''.$singular_en_lower.'\' => $'.$singular_en_lower.'
        ]);

    }


    /**
     * Изменить '.str($this->singular_ru_v)->lower().' (отправка формы)
     * @throws \Throwable
     */
    public function editSave( '.$this->singular_en.' $'.$singular_en_lower.', Request $request ): '.$this->singular_en.'
    {

        roles()->checkAccessWithAbort(\''.$plural_en_lower.'.edit\');

        return '.$service_func.'()->edit( $'.$singular_en_lower.', $request );

    }


    /**
     * Удалить '.str($this->singular_ru_v)->lower().'
     * @throws \Throwable
     */
    public function delete( '.$this->singular_en.' $'.$singular_en_lower.' ): ?bool
    {

        roles()->checkAccessWithAbort(\''.$plural_en_lower.'.delete\');

        return '.$service_func.'()->delete( $'.$singular_en_lower.' );

    }

    '.$restore_func.'
}';

        if( devTools()->saveFile($path, $code) ) {

            $this->info('created: app/Http/Controllers/Admin/'.$name.'.php');

        }else{

            $this->fails[] = 'can not create: app/Http/Controllers/Admin/'.$name.'.php';

        }


    }


    private function createService(){

        $name = str($this->plural_en)->camel()->ucfirst().'Service';

        $path = app_path('Services/'.$name.'.php');

        if( devTools()->fileExists($path) ){
            $this->fails[] = 'file already exist: app/Services/'.$name.'.php';
            return;
        }

        $code = '<?php

namespace App\Services;

use App\Extra\Services\Traits\ServiceTrait;
use App\Extra\Services\Service;

class '.$name.' extends Service
{

    use ServiceTrait;

}';


        if( devTools()->saveFile($path, $code) ) {

            $this->info('created: app/Services/'.$name.'.php');

        }else{

            $this->fails[] = 'can not create: app/Services/'.$name.'.php';

        }



    }

    private function addAdminViews(){

        $singular_en_lower = str($this->singular_en)->snake();
        $plural_en_lower = str($this->plural_en)->snake();

        $files_to_add = [];

        $browse_clean_path = 'resources/views/admin/app/'.$plural_en_lower.'/browse.blade.php';
        $read_clean_path = 'resources/views/admin/app/'.$plural_en_lower.'/read.blade.php';
        $add_clean_path = 'resources/views/admin/app/'.$plural_en_lower.'/add.blade.php';
        $edit_clean_path = 'resources/views/admin/app/'.$plural_en_lower.'/edit.blade.php';

        $component_form_path = 'resources/views/admin/app/'.$plural_en_lower.'/components/form.blade.php';
        $component_table_row_path = 'resources/views/admin/app/'.$plural_en_lower.'/components/table-row.blade.php';

        $th = "";
        $td = "";
        $form_inputs = "";
        foreach( $this->model_columns as $column ){

            //JSON пока пропускаем
            $column_info = DB::table('INFORMATION_SCHEMA.COLUMNS')
                ->where('TABLE_SCHEMA', config('env.db.database'))
                ->where('TABLE_NAME', $this->model->getTable())
                ->where('COLUMN_NAME', $column)
                ->first();

            if( $column_info && isset($column_info->DATA_TYPE) && $column_info->DATA_TYPE == 'json' )
                continue;

            if( !in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at']) ){

                if( $th )
                    $th .= "\n";

                if( $td )
                    $td .= "\n";

                $th .= "                            <th>".$column."</th>";
                $td .= "    <td>{{ $".$singular_en_lower."->".$column." }}</td>";


                $form_inputs .= "
        <div class=\"input\">
            <label>
                <span class=\"label\">".$column." <i>*</i></span>
                <input type=\"text\" name=\"".$column."\" value=\"{{ $".$singular_en_lower."->".$column." ?? request()->input('".$column."') }}\" autocomplete=\"password\">
            </label>
        </div>";

            }

        }

        $browse_dropdown_btn = '';

        if( $this->need_archive_model ){

            $browse_dropdown_btn = ",
                                'dropdown' => [
                                    [
                                        'href' => route('admin.".$plural_en_lower.".browse', ['deleted' => true]),
                                        'text' => 'Архив'
                                    ]
                                ]";

        }

        $browse_code = "@extends('admin.app.layout')
@section('title', '".$this->plural_ru_i."' )

@section('content')

    <div class=\"container\">

        <div class=\"page-header\">
            <h1>".$this->plural_ru_i."</h1>
            <div class=\"actions\">
                @if( roles()->checkAccess('".$plural_en_lower.".add') )
                    {!!
                        admin()->buttons([
                            [
                                'offcanvas-href' => route('admin.".$plural_en_lower.".add'),
                                'style' => 'primary',
                                'icon' => 'fa fa-plus',
                                'text' => 'Добавить',
                                'access' => '".$plural_en_lower.".add'".$browse_dropdown_btn."
                            ]
                        ])
                    !!}
                @endif
            </div>
        </div>

        <div class=\"box no-padding\">
            @include('admin.components.search_form')

            <div class=\"box-table\">
                <table class=\"table table-striped ".$plural_en_lower."-table @if( !count($".$plural_en_lower.") ) table-no-rows-found @endif\">
                    <thead>
                        <tr class=\"tr-bg-primary text-nowrap\">
                            <th class=\"id\">ID</th>
".$th."
                            <th class=\"actions\"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(  count($".$plural_en_lower.")  )

                        @foreach( $".$plural_en_lower." as $".$singular_en_lower." )
                            @include('admin.app.".$plural_en_lower.".components.table-row')
                        @endforeach

                    @else

                        <tr>
                            <td>
                                Ничего не найдено.
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>

            {{ admin()->paginate($".$plural_en_lower.") }}

        </div>

    </div>

    <load-js src=\"{{ mix('assets/admin/js/pages/".$plural_en_lower.".js') }}\"></load-js>

@endsection";



        $read_code = "@extends('admin.app.layout')
@section('title', '".$this->singular_ru_i." #'.$".$singular_en_lower."->id )

@section('content')

    <div class=\"container\">

        @include('admin.components.link-back')
        <div class=\"page-header\">
            <h1>".$this->singular_ru_i." #{{ $".$singular_en_lower."->id }}</h1>
            <div class=\"actions\">

            </div>
        </div>

        <form
            action=\"#\"
            class=\"inputs-only-read\"
        >

            @include('admin.app.".$plural_en_lower.".components.form', ['is_read' => true] )

        </form>

        <div class=\"page-footer\">
            <div class=\"actions\">
                @if( roles()->checkAccess('".$plural_en_lower.".edit') )
                    <a href=\"{{ route('admin.".$plural_en_lower.".edit', $".$singular_en_lower."->id) }}\" class=\"btn btn-primary\"><i class=\"fa fa-pen\"></i> <span>Редактировать</span></a>
                @endif
                @if( roles()->checkAccess('".$plural_en_lower.".delete') )
                    <button
                        class=\"btn btn-danger\"
                        data-delete-object
                        data-action=\"{{ route('admin.".$plural_en_lower.".delete', $".$singular_en_lower.") }}\"
                        data-confirm-text=\"Вы уверены, что желаете удалить ".str($this->singular_ru_v)->lower()."?\"
                        data-success-text=\"".$this->singular_ru_i." успешно ".$this->genderWord($this->singular_ru_i, 'удален', 'удалена', 'удалено').".\"
                        data-id=\"{{ $".$singular_en_lower."->id }}\"
                        data-event=\"".$singular_en_lower.".delete\"
                            >
                        <i class=\"fa fa-trash-alt\"></i> <span>Удалить</span>
                    </button>
                @endif
            </div>
        </div>

    </div>

    <load-js src=\"{{ mix('assets/admin/js/pages/".$plural_en_lower.".js') }}\"></load-js>

@endsection";


        $add_code = "@extends('admin.app.layout')
@section('title', 'Добавить ".str($this->singular_ru_v)->lower()."' )

@section('content')

    <div class=\"container\">

        @include('admin.components.link-back')

        <form
            action=\"#\"
            method=\"POST\"
            class=\"ajax-submit\"
            data-action=\"{{ route('admin.".$plural_en_lower.".add.save') }}\"
            data-method=\"POST\"
            data-redirect=\"back\"
            data-success-message=\"".$this->singular_ru_i." успешно ".$this->genderWord($this->singular_ru_i, 'добавлен', 'добавлена', 'добавлено').".\"
            data-event=\"".$singular_en_lower.".add\"
        >
            <div class=\"page-header\">
                <h1>Добавить ".str($this->singular_ru_v)->lower()."</h1>
                <div class=\"actions\">
                </div>
            </div>

            @include('admin.app.".$plural_en_lower.".components.form', ['is_add' => true] )

            <div class=\"page-footer\">
                <div class=\"actions\">
                    <button class=\"btn btn-primary\"><i class=\"fa fa-check\"></i> <span>Сохранить</span></button>
                </div>
            </div>
        </form>

    </div>

    <load-js src=\"{{ mix('assets/admin/js/pages/".$plural_en_lower.".js') }}\"></load-js>

@endsection";


        $edit_code = "@extends('admin.app.layout')
@section('title', 'Редактировать ".str($this->singular_ru_v)->lower()." #'.$".$singular_en_lower."->id )

@section('content')

    <div class=\"container\">

        @include('admin.components.link-back')

        <form
            action=\"#\"
            method=\"POST\"
            class=\"ajax-submit\"
            data-action=\"{{ route('admin.".$plural_en_lower.".edit.save', $".$singular_en_lower.") }}\"
            data-method=\"PUT\"
            data-redirect=\"back_with_update\"
            data-success-message=\"".$this->singular_ru_i." успешно ".$this->genderWord($this->singular_ru_i, 'изменен', 'изменена', 'изменено').".\"
            data-event=\"".$singular_en_lower.".edit\"
        >
            <div class=\"page-header\">
                <h1><a href=\"{{ route('admin.".$plural_en_lower.".read', $".$singular_en_lower.") }}\">".$this->singular_ru_i." #{{ $".$singular_en_lower."->id }}</a></h1>
                <div class=\"actions\">
                </div>
            </div>

            @include('admin.app.".$plural_en_lower.".components.form', ['is_edit' => true] )

            <div class=\"page-footer\">
                <div class=\"actions\">
                    <button class=\"btn btn-primary\"><i class=\"fa fa-check\"></i> <span>Сохранить</span></button>
                </div>
            </div>
        </form>

    </div>

    <load-js src=\"{{ mix('assets/admin/js/pages/".$plural_en_lower.".js') }}\"></load-js>

@endsection";


        $component_form_code = "<div class=\"box\">
    <div class=\"box-body\">".$form_inputs."
    </div>
</div>";


        $attr_soft_deletes = '';
        $btn_restore = '';
        $tr_disabled = '';
        if( $this->need_archive_model ){

            $tr_disabled = "
    @class(['disabled' => \$".$singular_en_lower."->trashed() ])";

            $attr_soft_deletes = "
                data-soft-deletes";

            $btn_restore = "
            <button
                class=\"btn btn-icon-default\"
                data-restore-object
                @if( !$".$singular_en_lower."->trashed() ) data-skip-confirm @endif
                data-action=\"{{ route('admin.".$plural_en_lower.".restore', $".$singular_en_lower.") }}\"
                data-confirm-text=\"Вы уверены, что желаете восстановить ".str($this->singular_ru_v)->lower()."?\"
                data-success-text=\"".$this->singular_ru_i." успешно ".$this->genderWord($this->singular_ru_i, 'восстановлен', 'восстановлена', 'восстановлено').".\"
            >
                <i class=\"fa fa-trash-arrow-up\"></i> <span>Восстановить</span>
            </button>";

        }

        $component_table_row_code = "<tr
    @if( roles()->checkAccess('".$plural_en_lower.".read') ) data-offcanvas-href=\"{{ route('admin.".$plural_en_lower.".read', $".$singular_en_lower.") }}\" data-id=\"{{ $".$singular_en_lower."->id }}\" @endif".$tr_disabled."
>
    <td class=\"id\">{{ $".$singular_en_lower."->id }}</td>
".$td."
    <td class=\"actions\">
        @if( roles()->checkAccess('".$plural_en_lower.".edit') )
            <a data-offcanvas-href=\"{{ route('admin.".$plural_en_lower.".edit', $".$singular_en_lower.") }}\" class=\"btn btn-icon-success\"><i class=\"fa fa-pen-to-square\"></i></a>
        @endif
        @if( roles()->checkAccess('".$plural_en_lower.".delete') )
            <button
                class=\"btn btn-icon-danger\"
                data-delete-object".$attr_soft_deletes."
                data-action=\"{{ route('admin.".$plural_en_lower.".delete', $".$singular_en_lower.") }}\"
                data-confirm-text=\"Вы уверены, что желаете удалить ".str($this->singular_ru_v)->lower()."?\"
                data-success-text=\"".$this->singular_ru_i." успешно ".$this->genderWord($this->singular_ru_i, 'удален', 'удалена', 'удалено').".\"
                data-id=\"{{ $".$singular_en_lower."->id }}\"
                data-event=\"".$singular_en_lower.".delete\"
            >
                <i class=\"fa fa-trash-alt\"></i>
            </button>".$btn_restore."
        @endif
    </td>
</tr>";


        $files_to_add[$component_form_path] = $component_form_code;
        $files_to_add[$component_table_row_path] = $component_table_row_code;
        $files_to_add[$browse_clean_path] = $browse_code;
        $files_to_add[$read_clean_path] = $read_code;
        $files_to_add[$add_clean_path] = $add_code;
        $files_to_add[$edit_clean_path] = $edit_code;

        foreach( $files_to_add as $clean_path => $code ){

            $path = base_path($clean_path);

            if( devTools()->fileExists($path) ){
                $this->fails[] = 'view already exists: '.$clean_path;
                continue;
            }

            if( devTools()->saveFile($path, $code) ) {

                $this->info('created: '.$clean_path);

            }else{

                $this->fails[] = 'can not create: '.$clean_path;

            }

        }

    }


    //Простенькая функция для определения рода и возвращения правильного слова в зависимости от рода
    public function genderWord( string $word, string $men, string $woman, string $neuter_gender ): string
    {

        if( str($word)->endsWith('а') )
            return $woman;

        if( str($word)->endsWith('о') || str($word)->endsWith('е') )
            return $neuter_gender;

        return $men;

    }

}
