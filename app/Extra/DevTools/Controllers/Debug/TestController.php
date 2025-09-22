<?php

namespace App\Extra\DevTools\Controllers\Debug;

use App\Http\Controllers\Controller;

class TestController extends Controller
{

    /**
     * Здесь можно прописывать код, функции и т.д. и тестировать в браузере по адресу /debug/test
     */
    public function index()
    {

        return view('debug.test',[

        ]);

    }
}
