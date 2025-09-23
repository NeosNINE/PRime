<?php

use App\Http\Controllers\Admin\AdminIndexController;
use App\Http\Controllers\Admin\AdminExtraController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\AdminRolesController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminPaymentsController;
use App\Http\Controllers\Admin\AdminEmailsController;
use App\Http\Controllers\Admin\AdminPromoCodesController;
use App\Http\Controllers\Admin\AdminLogsController;
use App\Http\Controllers\Admin\AdminProvidersController;
use App\Http\Controllers\Admin\AdminServicesController;
use App\Http\Controllers\Admin\AdminOrdersController;
use App\Http\Controllers\Admin\AdminLocalizationController;
use App\Http\Controllers\Admin\AdminProfileController;

Route::prefix('admin')->name('admin.')->group(function(){


    //Index
    Route::controller(AdminIndexController::class)->group(function(){
        Route::get('/', 'index')->name('index');
    });


    //Пользователи
    Route::name('users.')->controller(AdminUsersController::class)->group(function(){
        Route::get('users', 'browse')->name('browse');
        Route::get('user/add', 'add')->name('add');
        Route::post('user/add', 'addSave')->name('add.save');
        Route::get('user/edit/{user}', 'edit')->name('edit');
        Route::put('user/edit/{user}', 'editSave')->name('edit.save');
        Route::delete('user/delete/{user}', 'delete')->name('delete');
        Route::get('user/impersonate/{user}', 'impersonate')->name('impersonate');
        Route::post('user/leave-impersonation', 'leaveImpersonation')->name('leave_impersonation');
        Route::get('user/{user}', 'read')->name('read');
    });


    //Роли
    Route::prefix('settings')->name('roles.')->controller(AdminRolesController::class)->group(function(){
        Route::get('roles', 'browse')->name('browse');
        Route::get('role/add', 'add')->name('add');
        Route::post('role/add', 'addSave')->name('add.save');
        Route::get('role/edit/{role}', 'edit')->name('edit');
        Route::put('role/edit/{role}', 'editSave')->name('edit.save');
        Route::delete('role/delete/{role}', 'delete')->name('delete');
        Route::post('role/restore/{role_id}', 'restore')->name('restore');
    });


    //Настройки
    Route::prefix('settings')->name('settings.')->controller(AdminSettingsController::class)->group(function(){
        Route::get('', 'index')->name('index');
        Route::get('{section}', 'section')->name('section');
        Route::put('{section}', 'save')->name('save');
    });

    //Платежи
    Route::prefix('payments')->name('payments.')->controller(AdminPaymentsController::class)->group(function(){
        Route::get('', 'browse')->name('browse');
        Route::get('balance', 'balanceForm')->name('balance.form');
        Route::post('balance/change', 'changeBalance')->name('balance');
        Route::get('{transaction}', 'read')->name('read');
        Route::post('{transaction}/accept', 'accept')->name('accept');
        Route::post('{transaction}/refund', 'refund')->name('refund');
    });

    //Провайдеры
    Route::name('providers.')->controller(AdminProvidersController::class)->group(function(){
        Route::get('providers', 'browse')->name('browse');
        Route::get('provider/add', 'add')->name('add');
        Route::post('provider/add', 'addSave')->name('add.save');
        Route::get('provider/{provider}', 'read')->name('read');
        Route::get('provider/edit/{provider}', 'edit')->name('edit');
        Route::put('provider/edit/{provider}', 'editSave')->name('edit.save');
        Route::post('provider/{provider}/activate', 'activate')->name('activate');
        Route::post('provider/{provider}/deactivate', 'deactivate')->name('deactivate');
        Route::post('provider/{provider}/sync-balance', 'syncBalance')->name('sync_balance');
    });

    //Услуги
    Route::name('services.')->controller(AdminServicesController::class)->group(function(){
        Route::get('services', 'browse')->name('browse');
        Route::get('service/add', 'add')->name('add');
        Route::post('service/add', 'addSave')->name('add.save');
        Route::get('service/{service}', 'read')->name('read');
        Route::get('service/edit/{service}', 'edit')->name('edit');
        Route::put('service/edit/{service}', 'editSave')->name('edit.save');
        Route::post('services/bulk', 'bulk')->name('bulk');
        Route::delete('service/{service}', 'delete')->name('delete');
        Route::post('services/provider/{provider}/sync', 'syncProvider')->name('sync_provider');
        Route::post('services/markups', 'saveMarkup')->name('markups.save');
        Route::delete('services/markups/{markup}', 'deleteMarkup')->name('markups.delete');
    });

    //Заказы
    Route::name('orders.')->controller(AdminOrdersController::class)->group(function(){
        Route::get('orders', 'browse')->name('browse');
        Route::get('orders/export', 'export')->name('export');
        Route::get('order/{order}', 'read')->name('read');
        Route::put('order/{order}/status', 'updateStatus')->name('update_status');
    });

    //Промокоды
    Route::prefix('promocodes')->name('promocodes.')->controller(AdminPromoCodesController::class)->group(function(){
        Route::get('', 'browse')->name('browse');
        Route::get('add', 'addForm')->name('add');
        Route::post('add', 'addSave')->name('add.save');
        Route::get('edit/{promocode}', 'edit')->name('edit');
        Route::put('edit/{promocode}', 'editSave')->name('edit.save');
        Route::get('{promocode}', 'read')->name('read');
        Route::delete('delete/{promocode}', 'delete')->name('delete');
        Route::post('deactivate/{promocode}', 'deactivate')->name('deactivate');
        Route::post('activate/{promocode}', 'activate')->name('activate');
    });

    //Логи
    Route::prefix('logs')->name('logs.')->controller(AdminLogsController::class)->group(function(){
        Route::get('', 'browse')->name('browse');
    });


    //Emails
    Route::prefix('emails')->name('emails.')->controller(AdminEmailsController::class)->group(function(){
        Route::get('', 'browse')->name('browse');
        Route::delete('delete/{email}', 'delete')->name('delete');
        Route::post('restore/{email}', 'restore')->name('restore');
        Route::post('resend/all', 'resendAll')->name('resend_all');
        Route::post('resend/{email}', 'resend')->name('resend');
        Route::get('{email}', 'read')->name('read');
    });


    //Profile
    Route::prefix('profile')->name('profile.')->controller(AdminProfileController::class)->group(function(){
        Route::get('', 'read')->name('read');
        Route::get('edit', 'edit')->name('edit');
        Route::put('edit', 'editSave')->name('edit.save');
    });


    //Localization
    Route::prefix('localization')->name('localization.')->controller(AdminLocalizationController::class)->group(function(){
        Route::get('', 'browse')->name('browse');
        Route::get('admin-edit-mode', 'adminEditMode')->name('admin_edit_mode');
        Route::post('save', 'save')->name('save');
    });


    //Extra
    Route::controller(AdminExtraController::class)->group(function(){
        Route::get('extra/getHTMLTableRow', 'getHTMLTableRow')->name('get_html_table_row');
        Route::get('extra/getInformationForClient', 'getInformationForClient')->name('get_information_for_client');
        Route::get('extra/load-html-view', 'loadHTMLView')->name('load_html_view');
    });


});
