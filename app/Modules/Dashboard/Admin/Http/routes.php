<?php

Route::name('admin.')
    ->namespace('\App\Modules\Dashboard\Admin\Http\Controllers')
    ->prefix('admin')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'IndexController@index')->name('dashboard');
});