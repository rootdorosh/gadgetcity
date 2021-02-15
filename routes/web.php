<?php

use App\Base\CoreHelper;
use App\Modules\Parser\Models\RestaurantItem;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('', ['as' => 'index', 'uses' => 'HomeController@index']);
Route::get('/pattern', function () {
    (new \App\Services\Parser\ParserService())->applyCustomTemplatesTest(request()->get('t'));
});

Route::get('flush', function() {
    $item = RestaurantItem::where('id', 1)->first();
    return (new \App\Modules\Parser\Services\Parsing\RestaurantPageParsingService)->handle($item);
    \Cache::flush();
});

foreach (CoreHelper::getModules() as $module) {
    $file = app_path() . '/Modules/' . $module . '/Admin/Http/routes.php';
    if (is_file($file)) {
        include $file;
    }
}



Route::name('price-report.export')
    ->namespace('\App\Modules\Product\Admin\Http\Controllers')
    ->prefix('price-report/export')
    ->group(function () {
        Route::get('xml', 'ProductController@exportPriceReportXml')->name('xml');
        Route::get('xls', 'ProductController@exportPriceReportXls')->name('xls');
    });
