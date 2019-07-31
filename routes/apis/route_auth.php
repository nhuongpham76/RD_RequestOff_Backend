<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Auth')->prefix('auth')->group(function () {
    Route::post('login', 'LoginController')->name('auth.login');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('info', 'InfoController')->name('auth.info');
        Route::post('logout', 'LogoutController')->name('auth.logout');
    });
});
