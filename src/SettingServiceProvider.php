<?php

namespace JobMetric\EnvModifier;

use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('Setting', function ($app) {
            return new Setting($app);
        });
    }
}
