<?php

Route::name('admin.product.')
    ->namespace('\App\Modules\Product\Admin\Http\Controllers')
    ->prefix('admin/product')
    ->middleware('auth')
    ->group(function () {
        Route::get('products/import-availability', 'ProductController@importAvailability')->name('products.import-availability');
        Route::post('products/import-availability', 'ProductController@importAvailabilityPost')->name('products.import-availability.post');
        Route::get('products/autocomplete', 'ProductController@autocomplete')->name('products.autocomplete');
        Route::get('products/price-report', 'ProductController@priceReport')->name('products.price-report');
        Route::resource('products', 'ProductController')->except(['show']);

        Route::get('providers/export-members', 'ProviderController@exportMembers')->name('provider.export-members');
        Route::resource('providers', 'ProviderController');

        Route::put('provider-items/bulk-toggle', 'ProviderItemController@bulkToggle')->name('provider-items.bulk-toggle');
        Route::put('provider-items/set-product', 'ProviderItemController@setProduct')->name('provider-items.set-product');
        Route::delete('provider-items/remove-price/{priceId}', 'ProviderItemController@removePrice');
        Route::resource('provider-items', 'ProviderItemController');

    });
