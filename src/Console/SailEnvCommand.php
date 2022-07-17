<?php

namespace Jedymatt\LaravelSailEnv\Console;

use Laravel\Sail\Console\InstallCommand;
use Symfony\Component\Yaml\Yaml;

class SailEnvCommand extends InstallCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:env';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure the environment variables for the application';

    /**
     * Sail services
     *
     * @var string[]
     */
    protected $services = [
        'mysql',
        'pgsql',
        'mariadb',
        'redis',
        'memcached',
        'meilisearch',
        'minio',
        'mailhog',
        'selenium',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Create .env file if it doesn't exist
        if (! file_exists($this->laravel->basePath('.env'))) {
            $this->warn('No .env file found. Creating .env file from .env.example');
            copy($this->laravel->basePath('.env.example'), $this->laravel->basePath('.env'));
        }

        if (! file_exists($this->laravel->basePath('docker-compose.yml'))) {
            $this->error('docker-compose.yml not found. Please run "php artisan sail:install" first.');

            return 1;
        }

        $services = $this->servicesFromDockerCompose();

        $this->comment('Service(s) detected from docker-compose.yml: ['.implode(',', $services).']');

        $this->replaceEnvVariables($services);

        $this->info('Successfully configured .env file.');
    }

    protected function servicesFromDockerCompose(): array
    {
        $dockerCompose = Yaml::parseFile($this->laravel->basePath('docker-compose.yml'));

        $sailServices = array_filter($dockerCompose['services'], function ($service) {
            return in_array($service, $this->services);
        }, ARRAY_FILTER_USE_KEY);

        return array_keys($sailServices);
    }
}
