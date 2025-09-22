<?php

use App\Extra\DevTools\Controllers\DevToolsController;

Route::prefix('admin')->name('admin.')->group(function(){

    Route::prefix('dev')->name('dev_tools.')->controller(DevToolsController::class)->group(function(){
        Route::get('', 'index')->name('index');
        Route::get('/logs', 'logs')->name('logs');
        Route::post('/logs-clear', 'clearLogs')->name('clear_logs');
        Route::get('/code', 'code')->name('code');
        Route::get('/models', 'models')->name('models');
        Route::get('/secret-config', 'secretConfig')->name('secret_config');
        Route::get('/console', 'console')->name('console');
        Route::post('/refresh-helpers', 'refreshHelpers')->name('refresh_helpers');
        Route::post('/ide-helpers', 'ideHelpers')->name('ide_helpers');
        Route::post('/models-schema-generate', 'modelsSchemaGenerate')->name('models_schema_generate');
        Route::post('/localization-refresh', 'localizationRefresh')->name('localization_refresh');
        Route::post('/localization-load-to-db', 'localizationLoadToDb')->name('localization_load_to_db');
        Route::post('/localization-check', 'localizationCheck')->name('localization_check');
        Route::post('/queue-check', 'queueCheck')->name('queue_check');
        Route::post('/queue-start', 'queueStart')->name('queue_start');
        Route::post('/console-command-run', 'consoleCommandRun')->name('console_command_run');
        Route::post('/npm-run-dev', 'npmRunDev')->name('npm_run_dev');
    });

});
