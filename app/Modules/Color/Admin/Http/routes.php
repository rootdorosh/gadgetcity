<?php 

Route::name('admin.color.')
    ->namespace('\App\Modules\Color\Admin\Http\Controllers')
    ->prefix('admin/color')
    ->middleware('auth')
    ->group(function () {
        
        Route::resource('colors', 'ColorController');
                    
});