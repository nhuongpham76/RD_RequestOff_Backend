<?php

Route::namespace('Common')->group(function () {
    Route::get('/list-status-user', 'CommonController@getStatusUser');
    Route::get('/list-team', 'CommonController@getListTeam');
    Route::get('/list-role', 'CommonController@getListRole');
});