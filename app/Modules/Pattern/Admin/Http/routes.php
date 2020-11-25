<?php

Route::name('admin.pattern.')
    ->namespace('\App\Modules\Pattern\Admin\Http\Controllers')
    ->prefix('admin/pattern')
    ->middleware('auth')
    ->group(function () {
        Route::get('patterns/apply', 'PatternController@apply')->name('patterns.apply');
        Route::resource('patterns', 'PatternController');

});
