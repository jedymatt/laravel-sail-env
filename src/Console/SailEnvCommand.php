<?php

namespace Jedymatt\LaravelSailEnv\Console;

use Illuminate\Console\Command;
use Laravel\Sail\Console\Concerns\InteractsWithDockerComposeServices;
use Symfony\Component\Yaml\Yaml;

class SailEnvCommand extends Command
{
    use InteractsWithDockerComposeServices;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:env
                            {--O|overwrite : Whether to overwrite the existing .env file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure the environment variables for the application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! file_exists($this->laravel->basePath('docker-compose.yml'))) {
            $this->error('docker-compose.yml not found. Please run "php artisan sail:install" first.');

            return 1;
        }

        if ($this->option('overwrite')) {
            $this->comment('Overwriting environment variables');
            $this->createEnvFile();
        }

        // Create .env file if it doesn't exist
        if (! file_exists($this->laravel->basePath('.env'))) {
            $this->warn('No .env file found. Creating .env file from .env.example');
            $this->createEnvFile();
        }

        $services = $this->getServicesFromCompose();

        $this->comment('Detected services from docker-compose.yml: ['.implode(',', $services).']');

        $this->replaceEnvVariables($services);

        $this->info('Successfully configured .env file.');
    }

    protected function getServicesFromCompose(): array
    {
        $compose = Yaml::parseFile($this->laravel->basePath('docker-compose.yml'));

        return collect($compose['services'])
            ->filter(function ($service, $key) {
                return in_array($key, $this->services);
            })
            ->keys()
            ->toArray();
    }

    protected function createEnvFile(): void
    {
        copy($this->laravel->basePath('.env.example'), $this->laravel->basePath('.env'));
    }
}
