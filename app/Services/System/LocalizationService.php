<?php

namespace App\Services\System;

use App\Extra\Services\Service;
use App\Models\System\Localization;
use App\Models\System\LocalizationSection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LocalizationService extends Service
{

    const PAGINATION_LIMIT = 50;


    /**
     * Получить информацию об одном разделе
     */
    public function getSection( $section_id ): ?LocalizationSection
    {

        return LocalizationSection::find( (int)$section_id );

    }


    /**
     * Выбрать все разделы (только первого уровня)
     */
    public function getAllSections(): mixed
    {

        return LocalizationSection::with('sections')->whereNull('section_id')->orderBy('name','ASC')->get();

    }


    /**
     * Получить информацию об одной локале
     */
    public function getLocal( $id ): ?Localization
    {

        return Localization::find( (int)$id );

    }



    /**
     * Получить все локали для выбранного раздела (с пагинацией)
     */
    public function getAllLocals( $section_id ): LengthAwarePaginator
    {

        $sections_id = $this->getAllSectionId($section_id);

        return Localization::whereIn('section_id', $sections_id)->orderBy('key')->paginate($this::PAGINATION_LIMIT);

    }


    /**
     * Получить локали по поиску (с пагинацией)
     */
    public function getSearchLocals( $search_text ): LengthAwarePaginator
    {

        return Localization::where('key','LIKE','%'.$search_text.'%')->orWhere('name','LIKE','%'.$search_text.'%')->orWhere('text','LIKE','%'.$search_text.'%')->orderBy('key')->paginate($this::PAGINATION_LIMIT);

    }


    /**
     * Получить локали по ключу
     */
    public function getLocalsByKey( $key ): LengthAwarePaginator
    {

        return Localization::where('key',$key)->paginate($this::PAGINATION_LIMIT);

    }


    /**
     * Получить список всех дочерних ID секций (вместе с текущим)
     */
    public function getAllSectionId( $section_id ): array
    {

        $section_id = (int)$section_id;

        $ids = [ $section_id ];

        $child_sections = LocalizationSection::with('sections')->where('section_id',$section_id)->pluck('id');

        foreach( $child_sections as $child_section_id )
            $ids = array_merge($ids, $this->getAllSectionId($child_section_id) );


        return array_unique($ids);
    }



    /**
     * Выбрать секцию по helper_id
     */
    public function getSectionByHelperID( $helper_id ): ?LocalizationSection
    {

        return LocalizationSection::where('helper_id', (int)$helper_id )->first();

    }



    /**
     * Выбрать локаль по helper_id
     */
    public function getLocalByHelperID( $helper_id ): ?Localization
    {

        return Localization::where('helper_id', (int)$helper_id )->first();

    }



    /**
     * Добавить или обновить секцию
     */
    public function saveSection( $data, $dont_update = false ): LocalizationSection
    {

        $section = false;

        $data['key'] = trim($data['key'] ?? false);
        if( !$data['key'] )
            error('Необходимо указать ключ.');

        $check_key = LocalizationSection::where('key', $data['key'] )->where('section_id', $data['section_id'] ?? NULL )->first();

        if( $dont_update ){

            if( $check_key )
                error('Секция с ключом "'.$data['key'].'" уже существует.');

        }else {

            $section = $check_key;

        }


        if( !$section && isset($data['id']) ){

            $section = LocalizationSection::where('id',$data['id'])->first();

        }

        if( !$section && isset($data['helper_id']) ){

            $section = LocalizationSection::where('helper_id',$data['helper_id'])->first();



        }

        if( !$section )
            $section = new LocalizationSection();


        $name = $data['name'] ?? false;
        if( !$name )
            error('Необходимо указать название раздела.');


        if( !is_array($name) )
            $name = [ 'ru' => $name ];

        $section->name = $name;


        $section->key = $data['key'];


        if( isset($data['helper_id']) )
            $section->helper_id = $data['helper_id'];


        if( isset($data['helper_section_id']) ){

            $section->helper_section_id = $data['helper_section_id'];

            if( !isset($data['section_id']) ){

                $parent_section = $this->getSectionByHelperID($data['helper_section_id']);
                if( $parent_section )
                    $data['section_id'] = $parent_section->id;

            }

        }


        if( isset($data['section_id']) ){

            $parent_section = $this->getSection( $data['section_id'] );
            if( !$parent_section )
                error('Родительская секция №'.$data['section_id'].' не найдена.');

            $section->section_id = $data['section_id'];

        }



        //Прежде чем сохранить - ещё раз проверяем по ключу и section_id если ли уже такая категория
        $check_section = LocalizationSection::where('key', $section->key)->where('section_id', $section->section_id)->first();
        if( $check_section ){

            //Меняем helper_id, если нужно
            if( isset($data['helper_id']) || isset($data['helper_section_id']) ){

                if( isset($data['helper_id']) )
                    $check_section->helper_id = $data['helper_id'];

                if( isset($data['helper_section_id']) )
                    $check_section->helper_section_id = $data['helper_section_id'];

                $check_section->save();

            }

            return $check_section;

        }


        $section->save();

        return $section;

    }



    /**
     * Удалить раздел
     */
    public function deleteSection( $section_id ): bool
    {

        $section = $this->getSection($section_id);

        if( !$section )
            return false;

        return $section->delete();

    }



    /**
     * Удалить раздел by helper_id
     */
    public function deleteSectionByHelperID( $section_helper_id ): bool
    {

        $section = $this->getSectionByHelperID( $section_helper_id );

        if( !$section )
            return false;

        return $section->delete();

    }



    /**
     * Добавить или обновить локаль
     */
    public function saveLocal( $data, $dont_update = false ): bool
    {

        $local = false;

        if( !isset($data['lang_file']) ){
            $data['lang_file'] = 'content.php';
        }

        if( isset($data['id']) ){

            $local = Localization::where('id',$data['id'])->first();

        }

        if( !$local && isset($data['key']) ){

            $local = Localization::where('key',$data['key'])->where('lang_file', $data['lang_file'])->first();

        }

        if( !$local && isset($data['helper_id']) ){

            $local = Localization::where('helper_id',$data['helper_id'])->first();

        }

        if( $dont_update && $local )
            error('Локализация уже создана: '.$local->key);


        if( !$local ) {

            $local = new Localization();
            $new_local = true;

        }else{

            $new_local = false;

        }


        $local->type = $data['type'] ?? 'text';


        $text = $data['text'] ?? NULL;
        $local->text = $text;


        $name = $data['name'] ?? NULL;
        if( !is_array($name) && !is_null($name) )
            $name = [ 'ru' => $name ];

        if( isset($name['ru']) && is_null($name['ru']) )
            $name = NULL;

        $local->name = $name;



        $data['key'] = trim($data['key'] ?? false);
        if( !$data['key'] )
            error('Необходимо указать ключ.');


        if( isset($data['helper_id']) )
            $local->helper_id = $data['helper_id'];


        if( isset($data['helper_section_id']) ){

            $local->helper_section_id = $data['helper_section_id'];

            if( !isset($data['section_id']) ){

                $parent_section = $this->getSectionByHelperID($data['helper_section_id']);
                if( $parent_section )
                    $data['section_id'] = $parent_section->id;

            }

        }


        if( isset($data['section_id']) ){

            $parent_section = $this->getSection( $data['section_id'] );
            if( !$parent_section )
                error('Родительская секция №'.$data['section_id'].' не найдена.');

            $local->section_id = $data['section_id'];


        //Если секция не указана - то указываем секцию автоматически
        }else{

            $guest_section = $this->getOrAddSection('guest','Гостевой');
            $other_section = $this->getOrAddSection('extra','Дополнительно', $guest_section->id);
            $parent_section = $this->getOrAddSection('other', 'Другое', $other_section->id);

            $local->section_id = $parent_section->id;

        }


        $local->lang_file = $data['lang_file'];

        if( isset($data['not_consider_section_key']) ){

            $local->key = $data['key'];

        }else{
            $local->key = $this->getLocalKeyWithSectionKeys( $local->section_id, $data['key'] );
        }

        if( $new_local ) {

            $check_key = Localization::where('key', $local->key)->where('lang_file', $data['lang_file'])->first();
            if ($check_key)
                error('Ключ локали "' . $local->key . '" уже занят.');

        }

        return $local->save();


    }



    /**
     * Получить секцию по названию (если нет, то создает)
     */
    public function getOrAddSection( $key, $name, $parent_section = NULL ): LocalizationSection
    {

        $section = LocalizationSection::where('key', $key)->where('section_id',$parent_section)->first();

        if( $section )
            return $section;


        return $this->saveSection([
            'name' => $name,
            'key' => $key,
            'section_id' => $parent_section
        ]);

    }



    /**
     * Получить секцию по ключу
     */
    public function getSectionByKey( $key ): ?LocalizationSection
    {

        return LocalizationSection::where('key', $key)->first();

    }



    /**
     * Получить секцию по DOT ключу
     */
    public function getSectionByDotKey( $key, $ignore_last_dot = true ): LocalizationSection|bool
    {

        $section_path = explode('.', $key);

        if( $ignore_last_dot ){
            unset($section_path[count($section_path)-1]);
        }

        $section = false;

        foreach( $section_path as $current_key ){

            $get_section = LocalizationSection::where('key', $current_key);

            if( $section ){
                $get_section->where('section_id',$section->id);
            }

            $section = $get_section->first();

        }

        return $section;
    }



    /**
     * Получить ключ локали с учетом родительских ключей
     */
    public function getLocalKeyWithSectionKeys( $section_id, $key ): string
    {

        $section = $this->getSection( $section_id );
        if( !$section )
            return $key;

        $key = $section->key.'.'.$key;

        if( $section->section_id )
            return $this->getLocalKeyWithSectionKeys( $section->section_id, $key );

        return $key;
    }



    /**
     * Удалить локаль
     */
    public function deleteLocal( $id ): bool
    {

        $local = $this->getLocal($id);

        if( !$local )
            return false;

        return $local->delete();

    }



    /**
     * Удалить локаль by helper id
     */
    public function deleteLocalByHelperID( $local_helper_id ): bool
    {

        $local = $this->getlocalByHelperID( $local_helper_id );

        if( !$local )
            return false;

        return $local->delete();

    }



    /**
     * Обновить все файлы локалей в соответствии с текущими данными в базе данных
     */
    public function refreshLocalsFiles(): string
    {

        $locals = Localization::all();

        $array = [];

        foreach( $locals as $local ){

            if( is_array($local->text) )
                $array[$local->id] = $local->text;

        }

        return $this->localsSave( $array );

    }



    /**
     * Сохранить локали
     */
    public function localsSave( $array_input_locals ): string
    {

        $locals_id = array_keys($array_input_locals);
        $locals = Localization::whereIn('id',$locals_id)->get()->keyBy('id');

        $files_locals_settings = [];

        //Формируем массивы с локалями для разных файлов и сохраняем данные в базу данных
        foreach( $locals as $local ){

            $text = $array_input_locals[$local->id] ?? [];

            $local->text = $text;
            $local->save();

            foreach( $text as $lang_key => $lang_text ){

                if( !isset($files_locals_settings[$local->lang_file][$lang_key]) )
                    $files_locals_settings[$local->lang_file][$lang_key] = $this->getLocalFile($lang_key, $local->lang_file);

                if( !$lang_text )
                    $lang_text = null;

                $files_locals_settings[$local->lang_file][$lang_key] = data_set( $files_locals_settings[$local->lang_file][$lang_key], $local->key, $lang_text );

            }

        }

        //Сохраняем данные в файлы локализации
        foreach( $files_locals_settings as $file_name => $array ){

            foreach( $array as $lang_key => $array_content_locals ){

                $this->saveLocalFile($lang_key, $file_name, $array_content_locals);

            }

        }


        return 'success';

    }


    /**
     * Получить массив локализации из файла по ключу языка и названию файла
     */
    public function getLocalFile( $lang_key, $file_name ): array
    {

        $file_path = base_path('lang/'.$lang_key.'/'.$file_name);

        if( file_exists($file_path ) )
            return require $file_path;

        return [];

    }



    /**
     * Сохранить файл локали по ключу и названия файла
     */
    public function saveLocalFile( $lang_key, $file_name, $content_array ): bool|int
    {

        $dir_path = base_path('lang/'.$lang_key.'/');
        $file_path = $dir_path.$file_name;

        $code = "<?php";
        $code .= "\n\r";
        $code .= "return ".$this->getArrayCodeToPHP( $content_array ).';';

        if( !file_exists($dir_path) )
            mkdir($dir_path);

        return devTools()->saveFile($file_path, $code );

    }



    /**
     * Преобразовать массив для вставки в PHP файл
     */
    private function getArrayCodeToPHP( $array, $tabs = 1 ): string
    {

        $code = "[";
        $code .= "\r\n";

        $brackets_tabs = $tabs - 1;
        $values_tabs = $tabs;
        $next_level_tabs = $tabs + 1;

        $i = 0;
        foreach( $array as $key => $val ){

            $i++;

            $comma = ',';
            if( count($array) <= $i )
                $comma = '';

            $key = "'".$key."'";

            if( is_array($val) ){

                $code .= $this->addTab($values_tabs) . $key." => ".$this->getArrayCodeToPHP($val, $next_level_tabs) . $comma;
                $code .= "\r\n";

            }else{

                if( !is_null($val) ){

                    $val = "'".str_replace("'","\'", $val)."'";

                }else{
                    $val = 'null';
                }

                $code .= $this->addTab($values_tabs) . $key." => " . $val . $comma;
                $code .= "\r\n";

            }


        }

        $code .= $this->addTab($brackets_tabs) . "]";

        return $code;

    }


    /**
     * Добавить табуляцию
     */
    private function addTab($tabs=1): string
    {

        $code = '';

        if( $tabs > 0 ) {
            while (true) {

                $tabs--;
                $code .= "    ";

                if ($tabs <= 0) {
                    break;
                }
            }
        }

        return $code;

    }


    /**
     * Выгрузить локали из файлов в базу данных (сохраняются только те локали, которых нет в базе данных)
     */
    public function loadFromFilesToDB(): array
    {

        $statuses = [
            'success' => [],
            'error' => []
        ];
        $locals = [];

        $lang_dirs = scandir( base_path('lang') );
        foreach( $lang_dirs as $lang_key ){

            if( $lang_key == '.' || $lang_key == '..' || !is_dir(base_path('lang/'.$lang_key)) )
                continue;

            $lang_files = scandir( base_path('lang/'.$lang_key) );
            foreach( $lang_files as $file_name ){

                if( $file_name == '.' || $file_name == '..' )
                    continue;

                $file_content = require base_path('lang/'.$lang_key.'/'.$file_name);

                $file_content_dot = Arr::dot($file_content);

                foreach( $file_content_dot as $local_key => $local_text ){

                    if( is_array($local_text) ){
                        $statuses['error'][] = 'Пропущена локаль: '.$local_key.' ('.$file_name.') так как значение локали является массивом и не может редактироваться а админ-панели.';
                        continue;
                    }

                    $locals[$file_name][$local_key][$lang_key] = $local_text;

                }


            }

        }

        //Перебираем сформированный массив и добавляем в базу локалей, которых не было в базе
        $guest_section = $this->getOrAddSection('guest','Гостевой');
        $other_section = $this->getOrAddSection('extra','Дополнительно', $guest_section->id);
        foreach( $locals as $file_name => $file_locals ){

            foreach ( $file_locals as $local_key => $local_text ){

                //Если локаль уже есть в базе - обновляем значение в базе
                $current_local = Localization::where('key', $local_key)->where('lang_file', $file_name)->first();
                if( $current_local ) {

                    $ksort_current = $current_local->text;
                    ksort($ksort_current);

                    $ksort_new = $local_text;
                    ksort($ksort_new);

                    if( json_encode($ksort_current) != json_encode($ksort_new) ){

                        $current_local->text = $local_text;
                        $current_local->save();

                        $statuses['success'][] = 'Успешно обновлен текст локали файла '.$file_name.' "'.$local_key.'".';

                    }

                    continue;

                }

                $section = false;

                if( $file_name == 'auth.php' ){

                    $section_name = 'Авторизация';
                    $section_key = 'auth';

                }elseif( $file_name == 'pagination.php' ){

                    $section_name = 'Пагинация';
                    $section_key = 'pagination';

                }elseif( $file_name == 'passwords.php' ){

                    $section_name = 'Пароли';
                    $section_key = 'passwords';

                }elseif( $file_name == 'validation.php' ){

                    $section_name = 'Валидация';
                    $section_key = 'validation';

                }else{

                    $section = $this->getSectionByDotKey($local_key);

                    if( !$section ){
                        $section_name = 'Другое';
                        $section_key = 'other';
                    }

                }

                if( !$section )
                    $section = $this->getOrAddSection($section_key, $section_name, $other_section->id);

                try {

                    $this->saveLocal([
                        'section_id' => $section->id,
                        'consider_section_key' => false,
                        'text' => $local_text,
                        'lang_file' => $file_name,
                        'key' => $local_key,
                        'not_consider_section_key' => true
                    ], true);

                    $statuses['success'][] = 'Локаль файла '.$file_name.' "'.$local_key.'" успешно добавлена.';


                } catch ( \Exception $e ){

                    $statuses['error'][] = 'Не добавлена локаль файла '.$file_name.' "'.$local_key.'". Ошибка: '.$e->getMessage();

                }

            }

        }


        return $statuses;

    }



    /**
     * Проверить локализацию, найти потенциальные ошибки
     */
    public function check(): array
    {

        $response = [];


        //Папки, которые нужно просканировать
        $dir_to_scan = [
            'app',
            'resources/js',
            'resources/views'
        ];


        foreach( $dir_to_scan as $dir ){

            $files = $this->scanDir($dir);

            foreach( $files as $file_path ){

                $file_base_path = base_path($file_path);

                if( is_dir($file_base_path) )
                    dd($file_base_path);

                $file_code = devTools()->getFile($file_base_path);

                //Ищем вставки локализации
                preg_match_all(
                    '/lang\((.+?)\)/',
                    $file_code,
                    $matches
                );

                if( !count($matches[1]) )
                    continue;

                foreach ( $matches[1] as $match ){

                    $local_key = str_replace(['"',"'"], '', $match);

                    if( Str::startsWith(trim($local_key), '$key') )
                        continue;

                    $local_key = explode(',', $local_key)[0];
                    $local_key_c = 'content.' . $local_key;
                    $text = __($local_key_c);

                    if( $text == $local_key_c ) {

                        $local_key_c = $local_key;
                        $text = __($local_key_c);

                    }

                    if( $text == $local_key_c ){

                        if( __($local_key) == $local_key ){

                            $response[] = [
                                'msg' => 'Не найдена локаль  "'.$local_key.'"   '.$file_path,
                                'type' => 'warn'
                            ];

                        }else{

                            $response[] = [
                                'msg' => 'Возможно используется не верная функция @lang()  "'.$local_key.'"   '.$file_path,
                                'type' => 'info'
                            ];

                        }

                    }

                }

            }

        }

        return $response;

    }



    /**
     * Получить список файлов из нужной папки
     */
    public function scanDir( $dir, $files = [] ): array
    {

        $path = base_path($dir);

        $scan = scandir( $path );

        foreach( $scan as $file_name ){

            if( $file_name == '.' || $file_name == '..' )
                continue;

            $file_path = $dir.'/'.$file_name;

            if( is_dir(base_path($file_path)) ){

                $files = $this->scanDir( $file_path, $files );

            }else{

                $files[] = $file_path;

            }

        }

        return $files;

    }

}
