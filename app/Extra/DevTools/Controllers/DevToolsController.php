<?php

namespace App\Extra\DevTools\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Psr\SimpleCache\InvalidArgumentException;

class DevToolsController extends Controller
{

    /**
        Только для разработчиков
     */
    public function __construct(){
        $this->middleware('checkAccess:dev.*');
    }


    /**
     * Start Page Dev Tools
     */
    public function index(): RedirectResponse
    {

        if( isProduction() )
            return redirect()->route('admin.dev_tools.logs');

        return redirect()->route('admin.dev_tools.code');

    }


    /**
     * Secret Config Page
     */
    public function secretConfig(): View|RedirectResponse
    {

        return view('admin.app.dev_tools.secret_config',[
            'content' => devTools()->getSecretConfigContent(),
            'title' => 'Secret Config',
            'favicon' => devTools()->getFaviconUrl()
        ]);

    }


    /**
     * Console Page
     */
    public function console(): View
    {

        return view('admin.app.dev_tools.console',[
            'commands' => devTools()->getConsoleCommands(),
            'title' => 'Console',
            'favicon' => devTools()->getFaviconUrl()
        ]);

    }


    /**
     * Console Page
     */
    public function consoleCommandRun( Request $request ): Response
    {

        return response(
            devTools()->consoleCommandRun( $request->input('command') )
        );

    }


    /**
     * Log Page
     * @throws InvalidArgumentException
     */
    public function logs( Request $request ): View
    {

        $logs = devTools()->getLogsData( $request->input('type') );

        return view('admin.app.dev_tools.logs',[
            'logs' => paginateItems($logs),
            'title' => 'Logs',
            'favicon' => devTools()->getFaviconUrl()
        ]);

    }


    /**
     * Clear Logs
     */
    public function clearLogs( Request $request ): void
    {

        devTools()->clearLogs();

    }


    /**
     * Code Page
     */
    public function code( Request $request ): View
    {

        if( isProduction() )
            abort(422, 'It is bad idea to code on production.');

        return view('admin.app.dev_tools.code',[
            'title' => 'Code',
            'favicon' => devTools()->getFaviconUrl()
        ]);

    }


    /**
     * Models Page
     */
    public function models( Request $request ): View
    {

        return view('admin.app.dev_tools.models',[
            'title' => 'Models',
            'favicon' => devTools()->getFaviconUrl(),
            'models' => devTools()->getModelsInfo()
        ]);

    }



    /**
     * Обновить helpers.php
     */
    public function refreshHelpers(): string
    {

        devTools()->refreshHelpers();

        return 'Helpers.php successfully updated.';

    }


    /**
     * Сгенерировать ide-helpers
     */
    public function ideHelpers(): string
    {

        devTools()->ideHelpers();

        return 'IDE helpers successfully updated.';

    }


    /**
     * Сгенерировать Schema
     */
    public function modelsSchemaGenerate(): string
    {

        return devTools()->modelsSchemaGenerate();

    }


    /**
     * Обновить все файлы локалей в соответствии с текущими данными в базе данных
     */
    public function localizationRefresh(): string
    {

        return devTools()->localizationRefresh();

    }


    /**
     * Выгрузить локали из файлов в базу данных
     */
    public function localizationLoadToDb(): string
    {

        return devTools()->localizationLoadToDb();

    }


    /**
     * Проверить локализацию, найти потенциальные ошибки
     */
    public function localizationCheck(): string
    {

        return devTools()->localizationCheck();

    }


    /**
     * Запустить npm run dev
     */
    public function npmRunDev(): string
    {

        return devTools()->npmRunDev();

    }


    /**
     * Проверить запушен ли queue демон
     */
    public function queueCheck(): string
    {

        return devTools()->queueCheck();

    }


    /**
     * Запустить queue демона
     */
    public function queueStart(): void
    {

        devTools()->queueStart();

    }



}
