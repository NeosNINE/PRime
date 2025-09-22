<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminIndexController extends Controller
{
    public function index(): RedirectResponse
    {
        // Log first admin panel access per session to avoid duplicates
        if (!Session::get('admin_login_logged')) {
            Session::put('admin_login_logged', true);
            if (Auth::check()) {
                activity('login')->causedBy(Auth::user())->log('Вход в админ-панель');
            }
        }
        return redirect()->to( roles()->getFirstAccessURL() );
    }
}
