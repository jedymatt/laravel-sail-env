<?php

namespace Jedymatt\LaravelSailEnv;

use Illuminate\Support\ServiceProvider;

class LaravelSailEnvServiceProvider extends ServiceProvider
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
                Console\SailEnvCommand::class,
            ]);
        }
    }

    public function provides()
    {
        return [
            Console\SailEnvCommand::class,
        ];
    }
}
