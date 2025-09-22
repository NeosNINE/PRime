<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\System\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;


class AdminRolesController extends Controller
{

    /**
     * Просмотр списка ролей
     * @throws \Exception
     */
    public function browse( Request $request ): View
    {

        roles()->checkAccessWithAbort('roles.browse');

        return view('admin.app.roles.browse', [
            'roles' => roles()->get($request)
        ]);

    }


    /**
     * Добавить роль (Форма)
     */
    public function add(): View
    {

        roles()->checkAccessWithAbort('roles.add');

        return view('admin.app.roles.add');

    }


    /**
     * Добавить роль (Сохранить)
     * @throws \Throwable
     */
    public function addSave( Request $request ): Role
    {

        roles()->checkAccessWithAbort('roles.add');

        return roles()->add( $request );

    }


    /**
     * Редактировать роль (Форма)
     */
    public function edit( Role $role ): View
    {

        roles()->checkAccessWithAbort('roles.edit');

        return view('admin.app.roles.edit',[
            'role' => $role
        ]);

    }


    /**
     * Редактировать роль (Сохранить)
     * @throws \Throwable
     */
    public function editSave( Role $role, Request $request ): Role
    {

        roles()->checkAccessWithAbort('roles.edit');

        return roles()->edit( $role, $request );

    }


    /**
     * Удалить роль
     * @throws \Throwable
     */
    public function delete( Role $role ): ?bool
    {

        roles()->checkAccessWithAbort('roles.delete');

        return roles()->delete($role);

    }


    /**
     * Восстановить роль (из архива)
     * @throws \Throwable
     */
    public function restore( $role_id ): Role
    {

        roles()->checkAccessWithAbort('roles.delete');

        return roles()->restore($role_id);

    }

}
