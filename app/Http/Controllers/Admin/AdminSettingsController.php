<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class AdminSettingsController extends Controller
{

    /**
        Только для авторизованных админов (проверка)
    */
    public function __construct(){
        $this->middleware('checkAccess:settings.*');
    }


    /**
     * Главная страница настроек
     */
    public function index(): RedirectResponse
    {

        $sections = settings()->getAllSections();
        if( !count($sections) )
            settings()->error('More than one setting is not specified in the configuration. Fill in the settings_managing.php file.');

        $first_section_key = array_keys($sections);
        $first_section_key = array_shift($first_section_key);
        $first_section = $sections[$first_section_key];

        if( isset($first_section['manage']) ){

            return redirect()->route('admin.settings.section', $first_section_key);


        }else if( isset($first_section['route']) ){

            return redirect()->route($first_section['route']);


        }else if( isset($first_section['href']) ){

            return redirect()->to($first_section['href']);

        }

        settings()->error('First section not specified manage, route or href key.');

    }


    /**
     * Показать определенный раздел
     */
    public function section( $section_key ): View|RedirectResponse
    {

        $section = settings()->getSection( $section_key );

        if( isset($section['manage']) ){

            $page = settings()->getPageInfo($section_key);

            return view('admin.app.settings.section', [
                'section' => settings()->getSection( $section_key ),
                'title' => $page['title'] ?? null
            ]);



        }else if( isset($section['route']) ){

            return redirect()->route($section['route']);


        }else if( isset($section['href']) ){

            return redirect()->to($section['href']);

        }

        settings()->error('Section not specified manage, route or href key.');

    }


    /**
     * Сохранить настройки
     */
    public function save( $section_key, Request $request ): bool
    {

        return settings()->save( $section_key, $request->toArray() );

    }

}
