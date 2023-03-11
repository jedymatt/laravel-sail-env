<?php

namespace Jedymatt\LaravelSailEnv;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Jedymatt\LaravelSailEnv\Console\SailEnvCommand;

class LaravelSailEnvServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SailEnvCommand::class,
            ]);
        }
    }

    public function provides()
    {
        return [
            SailEnvCommand::class,
        ];
    }
}
