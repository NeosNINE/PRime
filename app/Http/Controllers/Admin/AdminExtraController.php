<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class AdminExtraController extends Controller
{

    /**
     *  Только для авторизованных админов (проверка)
    */
    public function __construct(){
        $this->middleware('checkAccess:*');
    }


    /**
     * Получить различную информацию для клиента с сервера
     * @throws \Exception
     */
    public function getInformationForClient( Request $request ): array
    {

        return admin()->getInformationForClient( $request->toArray() );

    }


    /**
     * Вывести информацию об объекте в таблицу.
     * Нужно для обновления таблиц сущностей при добавлении/редактировании сущности.
     * @throws \Exception
     */
    public function getHTMLTableRow( Request $request ): string
    {

        return admin()->getHTMLTableRow( $request->input('id'), $request->input('type'), $request->input('template') );

    }


    /**
     * Получить HTML шаблона Blade
     */
    public function loadHTMLView( Request $request ): string
    {

        $blade_key = $request->input('blade_key');
        $data = (array)$request->input('data');

        $check_access = [
            //'template_key' => 'access_key'
        ];

        if( !array_key_exists($blade_key, $check_access) )
            abort(403, 'Нет прав доступа для загрузки шаблона: '.$blade_key.'.');


        roles()->checkAccessWithAbort($check_access[$blade_key]);


        return view($blade_key, $data)->render();
    }

}
