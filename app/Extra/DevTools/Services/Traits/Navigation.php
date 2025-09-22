<?php

namespace App\Extra\DevTools\Services\Traits;

use Illuminate\Support\Facades\Route;

trait Navigation
{

    /**
     * Навигация в Dev Tools
     */
    public function getNavigation(): array
    {

        $links = [
            [
                'href' => route('admin.dev_tools.logs'),
                'name' => 'Logs'
            ],
            [
                'href' => route('admin.dev_tools.code'),
                'name' => 'Code'
            ],
            [
                'href' => route('admin.dev_tools.models'),
                'name' => 'Models'
            ],
            [
                'href' => route('admin.dev_tools.console'),
                'name' => 'Console'
            ]
        ];


        if( Route::has('debug.test') )
            $links[] = [
                'href' => route('debug.test'),
                'name' => 'Test Page',
                'target' => '_blank'
            ];


        if( Route::has('debug.test_git_ignore') )
            $links[] = [
                'href' => route('debug.test_git_ignore'),
                'name' => 'Test Page <small>(Git Ignore)</small>',
                'target' => '_blank'
            ];


        if( roles()->isSuperAdmin() )
            $links[] = [
                'href' => route('admin.dev_tools.secret_config'),
                'name' => 'Secret Config'
            ];

        return $links;

    }

}
