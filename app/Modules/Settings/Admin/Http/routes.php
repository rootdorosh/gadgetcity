<?php

Route::name('admin.settings.')
    ->namespace('\App\Modules\Settings\Admin\Http\Controllers')
    ->prefix('admin/settings')
    ->middleware('auth')
    ->group(function () {

        Route::get('', 'SettingsController@index')->name('index');
        Route::post('', 'SettingsController@update')->name('update');
});

