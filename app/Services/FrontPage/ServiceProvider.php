<?php

namespace App\Services\FrontPage;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Http\Request;

class ServiceProvider extends BaseServiceProvider
{

    protected $defer = true;

    public function register()
    {
        $this->app->singleton('frontPage', function () {
            return new FrontPage();
        });
    }
    
    public function provides()
    {
        return ['frontPage'];
    }
}
