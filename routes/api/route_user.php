<?php

Route::namespace('Users')->prefix('users')->group(function () {
    Route::get('/{id}', 'ShowController')->name('users.show');
    Route::get('', 'ListController')->name('users.index');
    Route::post('', 'CreateController')->name('users.create');
    Route::put('/{id}', 'UpdateController')->name('users.update');
    Route::delete('/{id}', 'DeleteController')->name('users.delete');
});