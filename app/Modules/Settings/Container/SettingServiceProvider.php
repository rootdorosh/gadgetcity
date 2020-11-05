<?php

namespace App\Modules\Settings\Container;

use Illuminate\Support\ServiceProvider;
use App\Modules\Settings\Models\Settings as EloquentStorage;

/**
 * Class SettingServiceProvider.
 */
class SettingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Setting', Setting::class);
        $this->app->bind(SettingStorageContract::class, EloquentStorage::class);
    }
}
