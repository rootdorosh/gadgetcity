<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use App\Base\CoreHelper;

class ModulesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        foreach (CoreHelper::getModules() as $module) {
            $modulePath = app_path() . '/Modules/' . $module . '/';
            
            //view('user.admin::role.index')
            if (is_dir($modulePath . '/Admin/Views')) {
                $this->loadViewsFrom($modulePath .  'Admin/Views', $module . '.admin');
            }
            if (is_dir($modulePath . '/Front/Views')) {
                $this->loadViewsFrom($modulePath .  'Front/Views', $module . '.front');
            }

            if (is_dir($modulePath .  '/Database/migrations')) {
                $this->loadMigrationsFrom($modulePath . '/Database/migrations');
            }
            
            if (is_dir($modulePath . '/Database/factories')) {
                $this->registerEloquentFactoriesFrom($modulePath . '/Database/factories');
            }
        
            //trans('module::messages.welcome')
            if (is_dir($modulePath . '/Resources/lang')) {
                $this->loadTranslationsFrom($modulePath . '/Resources/lang', Str::snake($module));
            }
        }
    }
    
    /**
     * Register factories.
     *
     * @param  string $path
     * @return void
     */
    protected function registerEloquentFactoriesFrom(string $path)
    {
        $this->app->make(EloquentFactory::class)->load($path);
    }    

    public function register()
    {
        
    }

}
