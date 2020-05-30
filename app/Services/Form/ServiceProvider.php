<?php

namespace App\Services\Form;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    protected $defer = true;

    public function register()
    {
        $this->app->singleton('form', function () {
            return new Builder();
        });
    }

    public function provides()
    {
        return ['form'];
    }
}
