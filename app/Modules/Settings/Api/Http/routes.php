<?php

Route::name('api.settings.')
    ->namespace('\App\Modules\Settings\Api\Http\Controllers')
    ->middleware(['api'])
    ->group(function () {

        Route::get('about-biocad', 'SettingsController@about')->name('about');
});
