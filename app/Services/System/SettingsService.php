<?php

namespace App\Services\System;

use App\Extra\Services\Service;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class SettingsService extends Service
{

    /**
     * Тип настроек, которые считать массивом
     */
    const ARRAY_TYPES = [ 'array', 'array_with_index', 'array_with_keys' ];


    /**
     * Файл, который редактируем в папке /configs
     */
    const CONFIG_FILE_NAME = 'settings';

    /**
     * Файл, который редактируем в папке /configs (для секретных данных, которые исключены из GIT)
     */
    const CONFIG_SECRET_FILE_NAME = 'settings_secret';



    /**
     * Проверка прав доступа
     */
    public function __construct( bool $with_check_access = true ){

        if( $with_check_access )
            roles()->checkAccessWithAbort('settings.*');

    }


    /**
     * Получить инфу обо всем Page по ключу раздела
     */
    public function getPageInfo( string $section_key = '' ){

        if( !$section_key )
            $section_key = $this->getCurrentSectionKey();

        return cacheService()->oneLoad('settings_get_page'.$section_key, function() use ($section_key){

            $page = null;

            foreach( config('settings_managing') as $page_key => $page_data ){

                foreach( $page_data['sections'] ?? [] as $key => $section_data ){

                    if( isset($section_data['is_secret']) && $section_data['is_secret'] )
                        checkConfirmPassword();

                    if( $section_key == $key ){
                        $page = $page_data;
                        $page['key'] = $page_key;
                    }

                }

            }

            if( is_null( $page ) )
                abort(404);

            return $page;

        });

    }



    /**
     * Получить section_key из URI
     */
    public function getCurrentSectionKey(): string
    {

        $section_key = request()->route()->originalParameter('section');

        if( is_null($section_key) )
            $section_key = last(explode('/', request()->route()->uri()));

        return $section_key;

    }



    /**
     * Получить список разделов для определенного Page
     * @param string $section_key - передается ключ секции (будет найдена первая секция по ключу и взят родительский Page
     */
    public function getSectionsForPage( string $section_key = '' ): array
    {

        $page_key_to_open = $this->getPageInfo($section_key)['key'];

        return cacheService()->oneLoad('settings_get_sections'.$page_key_to_open, function() use ($page_key_to_open){

            $sections = config('settings_managing.'.$page_key_to_open.'.sections');

            if( !count($sections) )
                return [];


            foreach( $sections as $key => $section ){

                if( isset($section['is_secret']) && $section['is_secret'] ){

                    if( !roles()->isSuperAdmin() ){

                        unset($sections[$key]);
                        continue;

                    }

                }

                if( $this->checkAccess($key) ){

                    $sections[$key] = $this->setDefaultValuesInSection($section, $key);


                }else{

                    unset($sections[$key]);

                }

            }


            return $sections;

        });

    }

    /**
     * Получить информацию об разделе
     * @param $section_key
     * @return array
     */
    public function getSection( $section_key ): array
    {

        return cacheService()->oneLoad('settings_get_section_'.$section_key, function() use ($section_key) {

            if( !$this->checkAccess($section_key) )
                abort(403, 'У Вас нет доступа');

            $page = $this->getPageInfo($section_key);

            if( !isset($page['sections'][$section_key]) )
                abort(404);

            $section = $page['sections'][$section_key];
            $section['section_key'] = $section_key;

            return $this->setDefaultValuesInSection($section, $section_key);

        });

    }


    /**
     * Проверить доступ к разделу
     */
    public function checkAccess( $section_key, $method = 'read' ): bool
    {

        $page = $this->getPageInfo($section_key);
        $section = config('settings_managing.'.$page['key'].'.sections.'.$section_key);

        if( isset($section['access_key']) ){

            $access_key = $section['access_key'];

        }else{

            $access_key = 'settings.'.$section_key.'.'.$method;

        }

        return roles()->checkAccess($access_key);

    }


    /**
     * Проверить доступ к разделу
     */
    public function checkAccessWithAbort( $section_key, $method = 'read' ): void
    {

        if( !$this->checkAccess($section_key, $method) )
            abort(403, 'У Вас нет доступа');

    }



    /**
     * Получить меню (ссылки) для настроек
     */
    public function getNavLinks(): array
    {

        $sections = $this->getSectionsForPage();

        $links = [];

        foreach( $sections as $section_key => $section_val ){

            if( isset($section_val['manage']) ){

                $links[] = [
                    'href' => route('admin.settings.section', $section_key),
                    'name' => $section_val['name']
                ];

            }elseif (isset($section_val['route']) ){

                $links[] = [
                    'href' => route($section_val['route']),
                    'name' => $section_val['name']
                ];

            }elseif (isset($section_val['href']) ){

                $links[] = [
                    'href' => $section_val['href'],
                    'name' => $section_val['name']
                ];

            }else{

                $this->error('You should specify manage, route or href key for section "'. $section_key .'".');

            }

        }

        return $links;
    }


    /**
     * Проставить стандартные значения для элементов, если они не указаны, для секции (рекурсивно)
     * @param $section
     * @return array
     */
    public function setDefaultValuesInSection( $section, $section_key ): array
    {

        $section['section_key'] = $section_key;

        if( !isset($section['manage']) )
            return $section;

        foreach( $section['manage'] as $manage_key => $item )
            $section['manage'][$manage_key] = $this->setDefaultValuesInItem( $item, $section );

        return $section;

    }


    /**
     * Рекурсивно проставить нужные значения в item (рекурсивно)
     * @param $item
     * @return array
     */
    public function setDefaultValuesInItem( $item, $section ): array
    {

        if( !isset($item['type']) )
            $this->error('The array of fields is set incorrectly. The \'type\' field is required. '.print_r($item, 1));

        $section_key = $section['section_key'];

        //Если это массив элементов
        if( in_array( $item['type'], self::ARRAY_TYPES) ){

            $item = $this->setDefaultValuesInArray( $item, [
                'localization' => false,
                'can_add_new_elem' => true,
                'can_delete_elem' => true,
                'count_items_in_line' => 2,
                'min_elements' => 1,
                'max_elements' => 50,
                'section_key' => $section_key
            ]);

            if( !isset($item['fields']) )
                $this->error('Fields must be specified for the array.');

            //Если передан массив значений
            if( isset($item['fields'][0]) ){

                foreach( $item['fields'] as $array_key => $array_item )
                    $item['fields'][$array_key] = $this->setDefaultValuesInItem( $array_item, $section );


            //Если редактируется только одно поле
            }else{

                $item['fields'] = $this->setDefaultValuesInItem( $item['fields'], $section );

            }


        }else{

            $item = $this->setDefaultValuesInArray( $item, [
                'localization' => false,
                'autocomplete' => false,
                'select2' => true,
                'step' => 0.01,
                'title_and_desc_on_top' => false,
                'section_key' => $section_key
            ]);

        }

        //Если этот раздел секретный - то и item секретный
        if( isset($section['is_secret']) && $section['is_secret'] )
            $item['is_secret'] = true;

        return $item;

    }


    /**
     * Рекурсивно проставить нужные значения в массив
     * @param $array
     * @param $default
     * @return array
     */
    public function setDefaultValuesInArray( $array, $default ): array
    {

        foreach( $default as $key => $val ){

            if( !array_key_exists($key, $array) )
                $array[$key] = $val;

        }

        return $array;

    }


    /**
     * Получить текущие значение, которое сейчас установлено в настройках
     * @param $current_path
     * @param $field
     */
    public function getCurrentValueForField( $current_path, $field ){

        $current_path = implode( '.', $current_path );

        if( isset($field['env']) && $field['env'] ){

            $value = env($current_path);

            $value_arr = json_decode($value, 1);
            if( is_array($value_arr) )
                return $value_arr;

            return $value;

        }else{

            if( isset($field['is_secret']) && $field['is_secret'] ){

                $config_file_name = $this::CONFIG_SECRET_FILE_NAME;

                return $this->decryptValue(config($config_file_name . '.' . $current_path));


            }else{

                $config_file_name = $this::CONFIG_FILE_NAME;

                return config($config_file_name . '.' . $current_path);

            }

        }

    }


    /**
     * Получить HTML для редактирования элемента
     * @param array $item
     * @param string $manage_key
     * @param array $current_full_manage_key_path
     * @param array $current_full_manage_key_default_path
     * @param string $item_lang
     * @param array $data
     * @return string
     */
    public function getItemHTML(
        array $item,
        string $manage_key = '',
        array $current_full_manage_key_path = [],
        array $current_full_manage_key_default_path = [],
        string $item_lang = '',
        array $data = []
    ): string
    {

        $html = '';

        if( !$manage_key ){

            if( !isset($item['manage_key']) )
                $this->error("Specify 'manage_key' => '...' for the element: ". print_r($item, 1));


            $manage_key = $item['manage_key'];

            //Проставляем родительские элементы дочерним
            $item = $this->setParentItemsForField($item);

        }

        //Если не указан тип массива, но при этом есть fields
        if( isset($item['fields']) && !in_array( $item['type'], self::ARRAY_TYPES) )
            $this->error('You have specified "fields" => [...] for a field that does not have an array type. '.print_r($item, 1));


        if( !count($current_full_manage_key_path) ){
            $current_full_manage_key_path = [$manage_key];
            $current_full_manage_key_default_path = [$manage_key];
        }

        $data = $this->setDefaultValuesInArray($data, [
            'default_item' => false,
            'can_delete_elem' => false,
            'specifying_key' => false,
            'input_length' => 100
        ]);

        if( isset($item['input_length']) )
            $data['input_length'] = $item['input_length'];


        if( $item['type'] == 'array_with_keys' )
            $data['specifying_key'] = true;


        //Если тип элемента - массив
        if( in_array( $item['type'], self::ARRAY_TYPES) ) {

            $fields = $item['fields'];

            //Если передан не массив полей - преобразуем в массив с одним полем
            if( !isset($fields[0]) )
                $fields = [$fields];

            //Если количество полей больше 1, то делаем проверку
            if( count($fields) > 1 ){

                foreach( $fields as $field ){

                    if( !isset($field['key']) )
                        $this->error('If you specify the number of fields greater than 1, then you must specify a "key" for each field. '.print_r($field, 1));

                }

            }


            //Проверяем чтобы manage_key не было у дочерних fields
            foreach( $fields as $field ){

                if( isset($field['manage_key']) )
                    $this->error('Child fields should not have manage_key. It is set only for the main parent field.'.print_r($field, 1));

            }


            $last_key = 1;


            //Уровень вложенности массива
            $array_level = !$item['parent_item'] ? 1 : ( isset($item['parent_item']['manage_key']) ? 2 : 3 );


            //Если массив localization = true
            if( $item['localization'] ){

                $array_elems_html = [];
                $default_item_html = [];

                $first_lang_key = '';
                foreach ( config('settings.languages') as $lang_key => $lang ) {
                    $first_lang_key = $lang_key;
                    break;
                }

                $current_full_manage_key_path_save = $current_full_manage_key_path;
                $current_full_manage_key_default_path_save = $current_full_manage_key_default_path;

                $current_value = $this->getCurrentValueForField($current_full_manage_key_path, $item);
                if( is_array($current_value) ){
                    $current_languages = array_keys($current_value);
                }else{
                    $current_languages = [$first_lang_key];
                }

                $count_elements = [];

                foreach ( config('settings.languages') as $lang_key => $lang ){

                    $current_full_manage_key_path = $current_full_manage_key_path_save;
                    $current_full_manage_key_default_path = $current_full_manage_key_default_path_save;

                    $current_full_manage_key_path[] = $lang_key;
                    $current_full_manage_key_default_path[] = $lang_key;

                    $current_value = $this->getCurrentValueForField($current_full_manage_key_path, $item);
                    if( is_array($current_value) && count($current_value) ){
                        $current_keys_value = array_keys($current_value);
                    }else{
                        $current_keys_value = [1];
                    }

                    //Если ключи для массива не указываются вручную
                    if( $item['type'] != 'array_with_keys' ){

                        if( count($current_keys_value) && max($current_keys_value) > $last_key ){
                            $last_key = max($current_keys_value);
                        }

                    }

                    $current_full_m_key = $current_full_manage_key_path;
                    $current_full_m_default_key = $current_full_manage_key_default_path;

                    $count_elements[$lang_key] = 0;
                    $array_elems_html[$lang_key] = '';

                    foreach( $current_keys_value as $current_key ){

                        $current_full_manage_key_path = $current_full_m_key;
                        $current_full_manage_key_default_path = $current_full_m_default_key;

                        $current_full_manage_key_path[] = $current_key;
                        $current_full_manage_key_default_path[] = '-default-index-';


                        if( in_array($lang_key, $current_languages) ) {

                            $count_elements[$lang_key]++;
                            $array_elems_html[$lang_key] .= $this->getInputsHTML($fields, $item, $lang_key, [
                                'manage_key' => $manage_key,
                                'full_manage_key_path' => $current_full_manage_key_path,
                                'full_manage_key_default_path' => $current_full_manage_key_default_path,
                                'specifying_key' => $data['specifying_key'],
                                'default_item' => $data['default_item'],
                                'data_key' => (int)$current_key,
                                'cfg_key' => (string)$current_key
                            ]);

                        }

                    }

                    //Стандартный элемент (т.е. элемент, который будет добавляться при нажатии на кнопку добавления элемента)
                    $default_item_html[$lang_key] = $this->getInputsHTML($fields, $item, $lang_key, [
                        'manage_key' => $manage_key,
                        'full_manage_key_path' => $current_full_manage_key_path,
                        'full_manage_key_default_path' => $current_full_manage_key_default_path,
                        'default_item' => true,
                        'can_delete_elem' => $item['can_delete_elem'],
                        'specifying_key' => $data['specifying_key'],
                        'data_key' => '-default-index-'
                    ]);


                }


            //Если бзе локализации
            }else{

                $current_value = $this->getCurrentValueForField($current_full_manage_key_path, $item);
                if( is_array($current_value) && count($current_value) ){
                    $current_keys_value = array_keys($current_value);
                }else{
                    $current_keys_value = [1];
                }

                if( count($current_keys_value) && max($current_keys_value) > $last_key ){
                    $last_key = max($current_keys_value);
                }

                $current_full_m_key = $current_full_manage_key_path;
                $current_full_m_default_key = $current_full_manage_key_default_path;

                $array_elems_html = '';
                $count_elements = 0;
                foreach( $current_keys_value as $current_key ){

                    $count_elements++;

                    $current_full_manage_key_path = $current_full_m_key;
                    $current_full_manage_key_default_path = $current_full_m_default_key;

                    $current_full_manage_key_path[] = $current_key;
                    $current_full_manage_key_default_path[] = '-default-index-';

                    $array_elems_html .= $this->getInputsHTML($fields, $item, $item_lang,[
                        'manage_key' => $manage_key,
                        'full_manage_key_path' => $current_full_manage_key_path,
                        'full_manage_key_default_path' => $current_full_manage_key_default_path,
                        'specifying_key' => $data['specifying_key'],
                        'default_item' => $data['default_item'],
                        'data_key' => (int)$current_key,
                        'cfg_key' => (string)$current_key
                    ]);

                }

                //Стандартный элемент (т.е. элемент, который будет добавляться при нажатии на кнопку добавления элемента)
                $default_item_html = $this->getInputsHTML($fields, $item, $item_lang, [
                    'manage_key' => $manage_key,
                    'full_manage_key_path' => $current_full_manage_key_path,
                    'full_manage_key_default_path' => $current_full_manage_key_default_path,
                    'default_item' => true,
                    'can_delete_elem' => $item['can_delete_elem'],
                    'specifying_key' => $data['specifying_key'],
                    'data_key' => '-default-index-'
                ]);

            }

            //Добавляем HTML массива элементов
            $html .= view('admin.app.settings.manage.arrays', [
                'item' => $item,
                'array_elems_html' => $array_elems_html,
                'default_item_html' => $default_item_html,
                'last_key' => $last_key,
                'array_level' => $array_level,
                'count_elements' => $count_elements
            ]);


            //Если это обычное поле (не массив)
        }else{

            $html .= $this->getInputsHTML($item, $item['parent_item'],$item_lang, [
                'manage_key' => $manage_key,
                'full_manage_key_path' => $current_full_manage_key_path,
                'full_manage_key_default_path' => $current_full_manage_key_default_path,
                'default_item' => $data['default_item'],
                'specifying_key' => $data['specifying_key'],
                'input_length' => $data['input_length']
            ]);

        }

        return $html;
    }


    /**
     * Получить HTML всех полей (вместе с <div class="settings-item"> ... </div>)
     * @param $fields
     * @param $parent_item
     * @param string $lang
     * @param array $data
     */
    public function getInputsHTML($fields, $parent_item, string $lang, array $data ){

        //Если передан не массив полей - преобразуем в массив с одним полем
        if( !isset($fields[0]) )
            $fields = [$fields];

        $html_fields = '';
        foreach( $fields as $field ) {

            $current_full_manage_key_path = $data['full_manage_key_path'];
            $current_full_manage_key_default_path = $data['full_manage_key_default_path'];

            $field_data = $data;

            //Если это массив элементов
            if( in_array($field['type'], self::ARRAY_TYPES) ){

                if( isset($parent_item['manage_key']) ){

                    $manage_key = $parent_item['manage_key'];

                }else{

                    if( !isset($parent_item['parent_item']['manage_key']) )
                        $this->error('The maximum nesting level of arrays (fields) is 3.');

                    $manage_key = $parent_item['parent_item']['manage_key'];

                }

                if( isset($field['key']) ){
                    $current_full_manage_key_path[] = $field['key'];
                    $current_full_manage_key_default_path[] = $field['key'];
                }

                $html_fields .= $this->getItemHTML( $field, $manage_key, $current_full_manage_key_path, $current_full_manage_key_default_path, $lang, [
                    'default_item' => $field_data['default_item']
                ]);


            //Если это input
            }else{

                if( isset($field['key']) ){
                    $current_full_manage_key_path[] = $field['key'];
                    $current_full_manage_key_default_path[] = $field['key'];
                }

                $field_data['full_manage_key_path'] = $current_full_manage_key_path;
                $field_data['full_manage_key_default_path'] = $current_full_manage_key_default_path;

                //Если это default элемент
                if( $field_data['default_item'] ){
                    $field_data['full_manage_key_path'] = $field_data['full_manage_key_default_path'];
                }

                $html_fields .= $this->getFieldHTML($field, $parent_item, $field_data);

            }

        }


        $data['parent_item'] = $parent_item;
        $data['slot'] = $html_fields;

        //Если нужно вывести key, то формируем его input name
        if( $data['specifying_key'] ){

            $specifying_key_path = ($data['default_item']) ? $data['full_manage_key_default_path'] : $data['full_manage_key_path'];
            $data['specifying_key_input_name'] = $this->getInputName($specifying_key_path, true);
        }

        if( count($fields) == 1 ){
            $data['item'] = $fields[0];
        }

        return view('admin.app.settings.manage.settings-item', $data);

    }


    /**
     * Получить HTML поле по типу (только HTML самого input`a)
     * @param array $field
     * @param $parent_item
     * @param array $data
     */
    public function getFieldHTML(array $field, $parent_item, array $data ){

        $data['parent_item'] = $parent_item;
        $data['item'] = $field;
        $data['field_value'] = $this->getCurrentValueForField($data['full_manage_key_path'], $field);
        $data['full_manage_key_path'] = $this->getInputName( $data['full_manage_key_path'] );

        if(
            is_array($data['field_value'])
            && !$field['localization']
            && $field['type'] != 'select_multiple'
            && $field['type'] != 'checkbox_multiple'
        )
            $data['field_value'] = json_encode($data['field_value']);

        $view_path = 'admin.app.settings.manage.types.'.$field['type'];

        if( !View::exists($view_path) )
            $this->error('This type "'.$field['type'].'" not supported. '.print_r($field, 1));

        return view($view_path, $data);

    }

    /**
     * Сформировать input name из массива path
     * @param array $full_manage_key_path
     * @param bool $keys
     * @return string
     */
    public function getInputName(array $full_manage_key_path, bool $keys = false ): string
    {

        $input_name = '';

        foreach( $full_manage_key_path as $key ){

            if( !$input_name ){

                $input_name = $key;

                if( $keys )
                    $input_name .= '__keys__';

            }else{

                $input_name .= '['.$key.']';

            }

        }

        if( $keys )
            $input_name .= '[__key__]';

        return $input_name;

    }


    /**
     * Проставить parent_item для поля (с учетом вложенности полей)
     * @param $field
     * @return array
     */
    public function setParentItemsForField( $field ): array
    {

        if( isset($field['manage_key']) )
            $field['parent_item'] = null;


        if( isset($field['fields']) ){

            $fields = $field['fields'];

            //Если передан не массив полей - преобразуем в массив с одним полем
            if( !isset($fields[0]) )
                $fields = [$fields];

            $field['fields'] = $fields;

            foreach( $field['fields'] as $child_field_key => $child_field ){

                $child_field = $this->setParentItemsForField( $child_field );
                $child_field['parent_item'] = $field;
                $field['fields'][$child_field_key] = $child_field;

            }

        }

        return $field;

    }



    /**
     * Сформировать массив полей с комментариями (для рединга)
     * @param null $array
     * @param string $current_array_path
     */
    public $array_render_comments = [];
    public function setCommentsForRender( $array = null, string $current_array_path = '' ){

        if( is_null( $array ) )
            $array = $this->fields_info;

        foreach( $array as $value ){

            if( isset($value['manage_key']) ){

                $new_array_path = $value['manage_key'];
                $this->array_render_comments[$new_array_path] = $value['desc'] ?? null;

                if( isset($value['fields']) )
                    $this->setCommentsForRender( $value['fields'], $new_array_path );

            }else{

                if( !isset($value['key']) )
                    continue;

                $new_array_path = $current_array_path.'.fields.'.$value['key'];
                $this->array_render_comments[$new_array_path] = $value['desc'] ?? null;

                if( isset($value['fields']) )
                    $this->setCommentsForRender( (array)$value['fields'], $new_array_path );


            }

        }

    }




    /**
     * Сохранить настройки
     * @param string $section_key
     * @param array $data
     * @return bool
     */
    public $data, $fields_info, $fields_info_initial_keys;
    public function save( string $section_key, array $data ): bool
    {

        $this->checkAccessWithAbort($section_key, 'edit');

        $this->data = $data;

        //Формируем список полей из всех секций, которые могли бы редактироваться
        $can_edit_keys = [];
        $fields_info = [];
        $sections = $this->getAllSections();
        foreach( $sections as $key => $section ){

            $section = $this->setDefaultValuesInSection($section, $key);
            $sections[$key] = $section;

            if( !isset($section['manage']) )
                continue;

            foreach( (array)$section['manage'] as $field ){

                if( isset($field['manage_key']) ){
                    $key = str_replace('.', '_', $field['manage_key']);
                    $can_edit_keys[] = $key;
                    $fields_info[$key] = $field;
                }

            }

        }

        //Проставляем parent_item для всех полей
        foreach( $fields_info as $key => $field ){
            $fields_info[$key] = $this->setParentItemsForField($field);
        }

        //Добавляем информацию о полей с изначальными manage_key (бзе str_replace)
        $fields_info_initial_keys = [];
        foreach( $fields_info as $field ){
            $fields_info_initial_keys[$field['manage_key']] = $field;
        }

        $this->fields_info = $fields_info;
        $this->fields_info_initial_keys = $fields_info_initial_keys;


        //Перебираем все данные и формируем массив
        $data_to_save = [];
        $data_to_save_env = [];
        foreach( $data as $key => $value ){

            if( !in_array($key, $can_edit_keys) )
                continue;

            $field = $fields_info[$key];

            $field_val = $this->getValueForField( $field, $value );

            if( isset($field['env']) && $field['env'] ){

                $data_to_save_env[$field['manage_key']] = $field_val;

            }else{

                $data_to_save[$field['manage_key']] = $field_val;

            }

        }


        //Перебираем все данные и отчищаем, если нужно
        if( isset($data['empty']) ){

            foreach( (array)$data['empty'] as $empty_key ){

                $field = $this->fields_info_initial_keys[$empty_key] ?? null;

                if( is_null($field) ){

                    $arr = explode('|lang|', $empty_key);
                    $field = $this->fields_info_initial_keys[$arr[0]] ?? null;
                    $lang = $arr[1] ?? null;

                }

                if( !is_null($field) && isset($field['manage_key']) ){

                    if( isset($field['env']) && $field['env'] ){

                        if( isset($lang) ){

                            $data_to_save_env[$field['manage_key']][$lang] = '';

                        }else{
                            $data_to_save_env[$field['manage_key']] = '';
                        }

                    }else{

                        if( in_array($field['type'], self::ARRAY_TYPES) ){

                            $val = [];

                        }else{

                            $val = null;

                        }

                        if( isset($lang) ){

                            $data_to_save[$field['manage_key']][$lang] = $val;

                        }else{

                            $data_to_save[$field['manage_key']] = $val;

                        }

                    }

                }

            }

        }


        //Сохраняем config (Secret)
        $this->saveInSettingsPHP( $data_to_save, true );

        //Сохраняем config (обычный, не секретный)
        $this->saveInSettingsPHP( $data_to_save);

        //Сохраняем .env
        $this->saveInENV( $data_to_save_env );

        //Отчищаем кеш config/env
        $this->cacheClear();

        return true;

    }



    /**
     * Отчистить Cache конфигурации
     */
    public function cacheClear(){
        Artisan::call('config:clear');
    }



    /**
     * Получить список "секретных" ключей в конфигурации
     */
    public function getSecretManageKeys(): array
    {

        $keys = [];

        foreach( $this->getAllSections() as $section ){

            if( isset($section['is_secret']) && $section['is_secret'] ) {

                foreach( $section['manage'] ?? [] as $item )
                    $keys[] = $item['manage_key'];

            }

        }

        return $keys;
    }



    /**
     * Сохранить данные в settings.php
     * @param $data
     * @param $secret - если true, сохраняем секретные настройки
     * @return bool
     */
    public function saveInSettingsPHP( $data, bool $secret = false ): bool
    {

        if( !count($data) )
            return false;


        $secret_keys = $this->getSecretManageKeys();


        if( $secret ){

            if( !count($secret_keys) )
                return false;

            $config_file_name = $this::CONFIG_SECRET_FILE_NAME;

        }else{

            $config_file_name = $this::CONFIG_FILE_NAME;

        }

        if( file_exists( base_path('config/'.$config_file_name.'.php') ) ){

            $current_data = require base_path('config/'.$config_file_name.'.php');
            $current_code = devTools()->getFile( 'config/'.$config_file_name.'.php');

        }else{

            $current_data = [];
            $current_code = '';

        }

        $new_data = $current_data;

        foreach( $data as $field_key => $field_value ){

            $is_secret_field = false;

            foreach( $secret_keys as $secret_key ){

                if( $field_key == $secret_key || str($field_key)->startsWith($secret_key.'.') )
                    $is_secret_field = true;

            }

            if( $secret && !$is_secret_field )
                continue;

            if( !$secret && $is_secret_field )
                continue;

            if( $secret && $is_secret_field )
                $field_value = $this->encryptValue($field_value);

            Arr::set($new_data, $field_key, $field_value);

        }

        //Формируем комментарии для рендинга массива
        $this->setCommentsForRender();

        $array_render = $this->arrayRender( $new_data );

        $code = '<?php';
        $code .= "\r\n";
        $code .= "\r\n";
        $code .= 'return '.$array_render.';';

        //Сохраняем и записываем в историю
        if( $current_code != $code ) {

            devTools()->saveFile('config/'.$config_file_name.'.php', $code);

            $history_file_name = now()->format('Y_m_d_H:i:s') . '_uid_' . Auth::id() . '_' . $config_file_name . '.php';
            Storage::put('history_config/' . $history_file_name, $code);

            //Отчищаем конфиг
            Artisan::call('config:clear');

            if( isProduction() )
                Artisan::call('config:cache');

        }

        return true;

    }


    /**
     * Encrypt Value
     */
    public function encryptValue( $value ){

        if( is_array($value) ){

            foreach( $value as $key => $val ){

                $value[$key] = $this->encryptValue($val);

            }

        }else{

            $value = encrypt($value);

        }

        return $value;

    }


    /**
     * Decrypt Value
     */
    public function decryptValue( $value ){

        if( is_array($value) ){

            foreach( $value as $key => $val ){

                $value[$key] = $this->decryptValue($val);

            }

        }else{

            try {

                $value = decrypt($value);

            } catch ( \Throwable $throwable ){

            }

        }

        return $value;

    }


    /**
     * Сохранить данные в .env
     * @param $data
     * @return bool
     */
    public function saveInENV( $data ): bool
    {

        if( !count($data) )
            return false;

        $current_env = devTools()->getFile('.env');
        $env_parse = explode("\n", $current_env);

        $env_lines = [];
        foreach( $env_parse as $key => $value ){

            if( !$value )
                continue;

            $env_lines[explode('=',$value)[0]] = $key;
        }

        //Если на данный момент нет нужной записи в ENV - добавляем
        foreach( $data as $key => $value ){

            if( strpos($current_env,$key.'=') === false ){
                $env_lines[$key] = max($env_lines) + 1;
            }

        }


        //Перебираем все данные для сохранения и сохраняем
        foreach( $data as $key => $value ){

            if( array_key_exists($key, $env_lines) ){

                if( is_array($value) )
                    $value = json_encode($value);

                //Убираем переносы строк
                $value = str_replace(["\n", "\r"], ' ', $value);

                $value = $this->prepareValueForRender($value);

                if( is_null($value) || $value === 'null' ){

                    $value = '';

                }elseif( $value === "''" ){

                    $value = '';

                }

                $env_parse[$env_lines[$key]] = $key."=".$value;

            }

        }

        $new_env = implode("\n", $env_parse);

        //Сохраняем и записываем в историю
        if( $current_env != $new_env ) {

            //Сохраняем первый вариант .ENV, если он не был сохранен
            if( !Storage::exists('history_env/_first.env') ){
                Storage::put('history_env/_first.env', $current_env);
            }

            devTools()->saveFile('.env', $new_env);

            $history_file_name = now()->format('Y_m_d_H:i:s') . '_uid_' . Auth::id() . '_.env';
            Storage::put('history_env/' . $history_file_name, $new_env);
        }

        return true;

    }


    /**
     * Вывести массив как массив PHP
     * @param array $array
     * @param int $tabs
     * @param string $current_array_path
     * @return string
     */
    public function arrayRender( array $array, int $tabs = 1, string $current_array_path = '' ): string
    {

        $code = "";

        $brackets_tabs = $tabs - 1;
        $values_tabs = $tabs;
        $next_level_tabs = $tabs + 1;

        $code .= "[";
        $code .= "\r\n";

        $i = 0;
        $should_print_key = true;

        foreach( $array as $key => $val ){

            $i++;

            if( !$current_array_path ){

                $new_array_path = $key;
                $comment = Arr::get($this->array_render_comments, $new_array_path);

            }else{

                $new_array_path = $current_array_path.'.fields';
                $comment = Arr::get($this->array_render_comments, $new_array_path);

                if( !$comment ){

                    $new_array_path = $current_array_path.'.'.$key;
                    $comment = Arr::get($this->array_render_comments, $new_array_path);

                    if( !$comment ){
                        $new_array_path = $current_array_path.'.'.( !is_string($key) ? 'fields' : $key);
                    }

                }

            }

            //Если это будет массив с ключами, которые начинаются с нуля, то не выводим ключи
            if( $key === 0 )
                $should_print_key = false;

            $comma = ',';
            if( count($array) <= $i )
                $comma = '';

            $key = $this->prepareValueForRender($key);


            $code = $this->addTab($code, $values_tabs);

            if( is_array($val) ){

                if( $comment ){
                    $code .= '/* '.$comment." */\r\n";
                    $code = $this->addTab($code, $values_tabs);
                }

                if( $should_print_key )
                    $code .= $key." => ";

                $code .= $this->arrayRender($val, $next_level_tabs, $new_array_path ).$comma;

            }else{

                if( $should_print_key )
                    $code .= $key." => ";

                $val = $this->prepareValueForRender($val);

                $code .= $val.$comma;

                if( $comment ){
                    $code .= ' //'.$comment;
                }

            }

            $code .= "\r\n";


        }

        $code = $this->addTab($code, $brackets_tabs);
        $code .= "]";

        return $code;

    }


    /**
     * Добавить табуляцию
     * @param string $code
     * @param int $tabs
     * @return string
     */
    public function addTab(string $code, int $tabs = 1): string
    {

        if( $tabs > 0 ) {
            while (true) {

                $tabs--;
                $code .= '    ';

                if ($tabs <= 0) {
                    break;
                }
            }
        }

        return $code;
    }


    /**
     * Подготовка значения (подставляем кавычки если нужно)
     * @param $value
     * @return false|mixed|string
     */
    public function prepareValueForRender( $value ){

        if( is_null($value) || !is_array($value) && strtoupper($value) === 'NULL' )
            return 'null';

        if( $value === true )
            return 'true';

        if( $value === false )
            return 'false';

        if( $value == 'true' || $value == 'false' )
            return $value;


        if( is_array($value) ){

            foreach( $value as $k => $v )
                $value[$k] = $this->prepareValueForRender($v);


        }else if( (string)((float)$value) !== (string)$value ){

            if(
                !Str::startsWith($value,'"')
                && !Str::startsWith($value,"'")
                && $value !== 'null'
            ){
                $value = "'".$value."'";
            }

        }


        //Экранируем кавычки
        if( !is_array($value) ){

            if( Str::startsWith($value,'"') ){
                $value = mb_substr($value, 1);
                $value = mb_substr($value, 0, -1);
                $value = addslashes($value);
                $value = str_replace("\'", "'", $value); //убирает экранирование одинарных кавычек
                $value = '"'.$value.'"';
            }


            if( Str::startsWith($value,"'") ){
                $value = mb_substr($value, 1);
                $value = mb_substr($value, 0, -1);
                $value = addslashes($value);
                $value = str_replace('\"', '"', $value); //убирает экранирование двойных кавычек
                $value = "'".$value."'";
            }

        }


        return $value;
    }


    /**
     * Получить значения для массива из данных
     * @param $field
     * @param $array
     * @param null $current_key_path
     * @return array|false|string
     */
    public function getValueForField( $field, $array, $current_key_path = null){

        //Если это массив элементов
        if( in_array($field['type'], self::ARRAY_TYPES) ){

            //Если это массив с локализацией
            if( isset($field['localization']) && $field['localization'] ){

                $field_value = [];

                foreach( $array as $lang_key => $lang_array ){

                    $field_value[$lang_key] = $this->getForeachValue( $field, $lang_array, $current_key_path, $lang_key );

                }


            //Без локализации
            }else{

                $field_value = $this->getForeachValue( $field, $array, $current_key_path );

            }



            if( count($field_value) == 1 && $field_value[0] == null )
                $field_value = [];


        //Элемент - не массив
        }else{

            //Если у поля есть локализация
            if( isset($field['localization']) && $field['localization'] ){


                $field_value = $array;


            //Если у поля нет локализации
            }else{


                //Если у родителя есть локализация
                if( isset($field['parent_item']) && $field['parent_item']['localization'] ){

                    $field_value = $array;


                //Если у родителя нет локализации
                }else{

                    //Если это тип поля, который нужно сохранять как массив
                    if( $field['type'] == 'select_multiple' || $field['type'] == 'checkbox_multiple' || $field['type'] == 'images' || $field['type'] == 'file' || $field['type'] == 'files' ){

                        $field_value = (array)$array;


                    //Если это другой тип
                    }else{

                        $field_value = (is_array($array)) ? json_encode($array, JSON_UNESCAPED_UNICODE) : $array;

                    }

                }

            }

        }

        return $field_value;

    }


    /**
     * Get Foreach Value for Field
     * @param $field
     * @param $array
     * @param $current_key_path
     * @param null $lang_key
     * @return array
     */
    public function getForeachValue( $field, $array, $current_key_path, $lang_key = null ): array
    {

        $field_value = [];

        if( !isset($field['fields']) )
            $this->error('You must specify fields for the array.');

        if( !isset($field['fields'][0]) ){
            $fields = [ $field['fields'] ];
        }else{
            $fields = $field['fields'];
        }

        $lang_key_str = '';
        if( !is_null($lang_key) ){
            $lang_key_str = '.'.$lang_key;
        }

        $i = 0;
        foreach( (array)$array as $key => $value ){

            //Если тип поля - массив с прописанными ключами
            if( $field['type'] == 'array_with_keys' ){


                if( is_null($current_key_path) ){

                    $key_path = str_replace('.', '_', $field['manage_key']).$lang_key_str.'.'.$key;

                }else{

                    $key_path = $current_key_path.'.'.$field['key'].$lang_key_str.'.'.$key;

                }

                $field_key = $this->getFieldsKeys()[$key_path] ?? 0;

                //Если это обычный массив, без указания индекса
            }elseif( $field['type'] == 'array' ){

                $field_key = $i;

                //Если это массив, где ключи - индекс, который от 1 начинается
            }else{

                $field_key = $key;

            }

            //Если кол-во полей 1 и не указан ключ для первого поля
            if( count($fields) == 1 && !isset($fields[0]['key']) ){

                $child_field = $fields[0];
                $field_value[$field_key] = $this->getValueForField($child_field, $value, $key_path ?? null);


            //Если несколько разных полей в массиве
            }else{

                foreach( $value as $child_field_key => $child_value ){

                    //Ищем нужно поле
                    $child_field = null;
                    foreach( $fields as $f ){

                        if( $f['key'] == $child_field_key ){
                            $child_field = $f;
                        }

                    }

                    if( !isset($child_field) )
                        continue;

                    $field_value[$field_key][$child_field_key] = $this->getValueForField($child_field, $child_value, $key_path ?? null);

                }


            }

            $i++;

        }

        return $field_value;

    }


    /**
     * Получить прописанные ключи для полей (если у поля тип array_with_keys)
     */
    public $fields_keys = null;
    public function getFieldsKeys(): ?array
    {

        if( !is_null($this->fields_keys) )
            return $this->fields_keys;

        $keys = [];

        foreach( $this->data as $field_key => $field_value ){

            if( Str::endsWith($field_key, '__keys__') ){

                $field_key = str_replace('__keys__', '', $field_key);

                if( is_array($field_value) )
                    $keys = $this->getFieldsKeysChildren( $keys, $field_value, $field_key);

            }

        }

        $this->fields_keys = $keys;
        return $this->fields_keys;

    }

    /**
     * Получить прописанные ключи для полей (дочерние)
     * @param array $keys
     * @param array $array
     * @param string $field_key
     * @return array
     */
    public function getFieldsKeysChildren( array $keys, array $array, string $field_key ): array
    {

        foreach( $array as $key => $value ) {

            if( $key == '__key__' )
                $keys[$field_key] = $value;

            if( is_array($value) )
                $keys = $this->getFieldsKeysChildren( $keys, $value, $field_key.'.'.$key);

        }

        return $keys;
    }


    /**
     * Получить все префиксы routes настроек для правильного подсвечивания активного меню
     */
    public function getAllRoutesPrefixes(){

        $prefixes = ['admin.settings'];

        foreach( $this->getAllSections() as $section_data ){

            if( isset($section_data['route']) )
                $prefixes[] = $section_data['route'];

        }

        return $prefixes;

    }



    /**
     * Выбрать все секции из всех pages
     */
    public function getAllSections(): array
    {

        $pages = config('settings_managing');
        $sections = [];

        foreach( $pages as $page ){

            foreach( $page['sections'] ?? [] as $section_key => $section_data ){

                if( isset($sections[$section_key]) )
                    $this->error('You have the same key registered twice in different sections: '.$section_key);

                $sections[$section_key] = $section_data;

            }

        }

        return $sections;

    }


    /**
     * Выбросить ошибку
     * @param string $text
     */
    public function error( string $text ): never
    {

        abort(403, $text);

    }

}
