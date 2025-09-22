<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUsersController extends Controller
{


    /**
        Просмотр списка пользователей
    */
    public function browse( Request $request ): View
    {

        roles()->checkAccessWithAbort('users.browse');

        return view('admin.app.users.browse', [
            'users' => users()->get( $request )
        ]);

    }



    /**
     * Добавить пользователя (Форма)
     */
    public function add(): View
    {

        roles()->checkAccessWithAbort('users.add');

        return view('admin.app.users.add');

    }



    /**
     * Добавить пользователя (Сохранить)
     */
    public function addSave( Request $request ): User
    {

        roles()->checkAccessWithAbort('users.add');

        return users()->add( $request );

    }



    /**
        Просмотр пользователя
    */
    public function read( User $user ): View
    {

        roles()->checkAccessWithAbort('users.read');

        return view('admin.app.users.read', [
            'user' => $user
        ]);

    }



    /**
        Изменить пользователя (HTML шаблон)
    */
    public function edit( User $user ): View
    {

        roles()->checkAccessWithAbort('users.edit');

        return view('admin.app.users.edit', [
            'user' => $user
        ]);

    }



    /**
        Изменить пользователя (отправка формы)
    */
    public function editSave( User $user, Request $request ): User
    {

        roles()->checkAccessWithAbort('users.edit');

        return users()->edit( $user, $request );

    }



    /**
        Удалить пользователя
    */
    public function delete( User $user ): ?bool
    {

        roles()->checkAccessWithAbort('users.delete');

        return users()->delete( $user );

    }



    /**
     * Авторизоваться за юзера
     */
    public function impersonate( User $user, Request $request ): RedirectResponse
    {

        users()->impersonate( $user, $request->input('key') );

        return redirect()->to( RouteServiceProvider::getRedirectUrl() );

    }


    /**
     * Вернуться назад за админа
     */
    public function leaveImpersonation( Request $request ): RedirectResponse|bool
    {

        users()->leaveImpersonation();

        if( $request->ajax() )
            return true;

        return redirect()->to( roles()->getFirstAccessURL() );

    }


}
