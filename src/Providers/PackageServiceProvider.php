<?php

namespace Cheppers\LaravelBase\Providers;

use Cheppers\LaravelBase\Console\MakeApiResourceCommand;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('cheppers.make.apiresource', function () {
            return new MakeApiResourceCommand();
        });
        $this->commands('cheppers.make.apiresource');
    }
}
