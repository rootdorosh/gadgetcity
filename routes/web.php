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
