<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminProfileController extends Controller
{

    /**
        Только для тех, у кого есть доступ к Admin
    */
    public function __construct(){
        $this->middleware('checkAccess:*');
    }


    /**
     * Просмотр профиля
     */
    public function read(): View
    {
        return view('admin.app.profile.read',[
            'user' => Auth::user()
        ]);
    }


    /**
     * Редактирование профиля
     */
    public function edit(): View
    {
        return view('admin.app.profile.edit',[
            'user' => Auth::user()
        ]);
    }



    /**
        Редактирование профиля (отправка формы)
     */
    public function editSave( Request $request ): User
    {

        return users()->edit( Auth::user(), $request );

    }

}
