<?php

namespace Jedymatt\LaravelSailEnv\Console;

use Laravel\Sail\Console\InstallCommand;

class SailEnvCommand extends InstallCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:env
                            {--O|overwrite : Whether to overwrite the .env file if it exists}';

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
        if ($this->option('overwrite')) {
            $this->comment('Overwriting environment variables');
            copy($this->laravel->basePath('.env.example'), $this->laravel->basePath('.env'));
        }

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

        $this->comment('Detected services from docker-compose.yml: ['.implode(',', $services).']');

        $this->replaceEnvVariables($services);

        $this->info('Successfully configured .env file.');
    }

    protected function servicesFromDockerCompose(): array
    {
        $environment = file_get_contents($this->laravel->basePath('docker-compose.yml'));

        $regex = '/'.implode('|', array_map(function ($service) {
            return '(?<=\s)'.$service.'(?=:)'; // Match service name followed by ':' (e.g. mysql:) and preceded by whitespace
        }, $this->services)).'/';

        preg_match_all($regex, $environment, $matches);

        return array_values($matches[0]);
    }
}
