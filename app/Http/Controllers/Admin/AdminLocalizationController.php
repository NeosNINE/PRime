<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLocalizationController extends Controller
{


    /**
        Проверка доступа
     */
    public function __construct(){
        $this->middleware('checkAccess:localization.*');
    }



    /**
     * Режим поиска на сайте
     */
    public function adminEditMode( Request $request ): RedirectResponse
    {

        if( $request->input('cancel') == 'true' ){

            session()->put('localization_edit_mode', false);
            return redirect()->route('admin.localization.browse');


        }else{

            session()->put('localization_edit_mode', true);
            return redirect()->route('index');

        }

    }



    /**
        Просмотр списка разделов и контента
    */
    public function browse( Request $request ): View
    {

        $locals = false;

        if( $request->input('section') )
            $locals = localization()->getAllLocals( $request->input('section') );

        if( $request->input('search') )
            $locals = localization()->getSearchLocals( $request->input('search') );

        if( $request->input('key') )
            $locals = localization()->getLocalsByKey( $request->input('key') );


        return view('admin.app.localization.browse', [
            'sections' => localization()->getAllSections(),
            'section' => localization()->getSection( $request->input('section') ),
            'locals' => $locals
        ]);

    }


    /**
     * Сохранить локали
     */
    public function save( Request $request ): string
    {

        return localization()->localsSave( $request->input('locals') );

    }


}
