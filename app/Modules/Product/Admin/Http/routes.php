<?php

Route::name('admin.product.')
    ->namespace('\App\Modules\Product\Admin\Http\Controllers')
    ->prefix('admin/product')
    ->middleware('auth')
    ->group(function () {
        Route::get('products/autocomplete', 'ProductController@autocomplete')->name('products.autocomplete');
        Route::resource('products', 'ProductController');

        Route::resource('providers', 'ProviderController');

        Route::put('provider-items/bulk-toggle', 'ProviderItemController@bulkToggle')->name('provider-items.bulk-toggle');
        Route::put('provider-items/set-product', 'ProviderItemController@setProduct')->name('provider-items.set-product');
        Route::resource('provider-items', 'ProviderItemController');

    });
