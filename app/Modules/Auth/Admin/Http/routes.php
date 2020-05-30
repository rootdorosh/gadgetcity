<?php

Route::name('admin.auth.')
    ->namespace('\App\Modules\Auth\Admin\Http\Controllers')
    ->prefix('admin/auth')
    ->group(function () {
        Route::get('login', 'LoginController@form')->name('login.form');
        Route::post('login', 'LoginController@submit')->name('login.submit');
        Route::post('remind-password/email', 'RemindPasswordController@email')->name('remind_password.email');
        Route::post('remind-password/input', 'RemindPasswordController@input')->name('remind_password.input');
        Route::get('logout', 'LoginController@logout')->middleware('auth')->name('logout');
});