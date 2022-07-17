<?php

namespace Jedymatt\LaravelEnvSail;

use Illuminate\Support\ServiceProvider;

class LaravelEnvSailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\EnvSailCommand::class,
            ]);
        }
    }

    public function provides()
    {
        return [
            Console\EnvSailCommand::class,
        ];
    }
}
