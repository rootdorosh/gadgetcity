<?php

Route::name('admin.user.')
    ->namespace('\App\Modules\User\Admin\Http\Controllers')
    ->prefix('admin/user')
    ->middleware('auth')
    ->group(function () {
        
        Route::post('users/{user}', 'UserController@update')->name('users.update'); 
        Route::delete('users/bulk-destroy', 'UserController@bulkDestroy')->name('users.bulkDestroy');       
        Route::put('users/bulk-toggle', 'UserController@bulkToggle')->name('users.bulkToggle');       
        Route::resource('users', 'UserController')->except(['update']);    
        
        Route::delete('roles/bulk-destroy', 'RoleController@bulkDestroy')->name('roles.bulkDestroy');       
        Route::resource('roles', 'RoleController');        
});