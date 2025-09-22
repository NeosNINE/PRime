<?php

namespace App\Http\Controllers\Debug;

use App\Http\Controllers\Controller;

class TestGitIgnoreController extends Controller
{

    /**
     *  - GIT IGNORE -
     * Здесь можно прописывать код, функции и т.д. и тестировать в браузере по адресу /debug/test_git_ignore
     */
    public function index()
    {

        return view('debug.test_git_ignore',[

        ]);

    }
}