<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


require __DIR__.'/includes/guest.php';
require __DIR__.'/includes/user.php';
require __DIR__.'/includes/admin.php';
require __DIR__.'/includes/extra.php';
require __DIR__.'/includes/auth.php';

require app_path('Extra/DevTools/Routes/dev_tools.php');

if( isDebug() )
    require app_path('Extra/DevTools/Routes/debug.php');
