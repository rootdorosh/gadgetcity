<?php 
use Illuminate\Support\Str
?>

Route::name('admin.{{ Str::kebab($moduleName) }}.')
    ->namespace('\App\Modules\{{ $moduleName }}\Admin\Http\Controllers')
    ->prefix('admin/{{ Str::kebab($moduleName) }}')
    ->middleware('auth')
    ->group(function () {
    <?php foreach ($modelsData as $model): if (empty($model['routes'])){ continue; }?>    
        @if(isset($model['routes']['update_verb']) && $model['routes']['update_verb'] === 'POST')
Route::post('<?= $model['routes']['path']?>/{{{ Str::camel($model['name']) }}}', '<?= $model['name']?>Controller@update')->name('<?= Str::kebab($model['name_plural'])?>.update');
        Route::resource('<?= $model['routes']['path']?>', '<?= $model['name']?>Controller')->except('update');
        @else
Route::resource('<?= $model['routes']['path']?>', '<?= $model['name']?>Controller');
        @endif
    <?php endforeach?>
        
});