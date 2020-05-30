<?php 
use Illuminate\Support\Str;

$tab5 = "                    ";
$tab4 = "                ";
$tab3 = "            ";
$tab2 = "        ";
$tab1 = "    ";
?>

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Modules\{{ $moduleName }}\Models\{{ $model['name'] }};

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

$factory->define({{ $model['name'] }}::class, function (Faker $faker) {
    $data = [];
    @foreach ($model['fields'] as $attr => $field) @if (!empty($field['faker']))
    $data['{{$attr}}'] = {!! $field['faker'] !!};
    @endif @endforeach
    
    @if (!empty($model['translatable']))
    @foreach ($model['translatable']['fields'] as $attr => $field)
    foreach (config('translatable.locales') as $locale) {
        $data[$locale]['{{ $attr }}'] = {!! $field['faker'] !!};
    }
    @endforeach
    @endif
    
    return $data;
});
