<?php 

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Modules\Product\Models\Product;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Product::class, function (Faker $faker) {
    $data = [];
           $data['is_active'] = rand(0,1);
         
        
    return $data;
});
