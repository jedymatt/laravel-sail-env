<?php

namespace Jedymatt\LaravelSailEnv\Console;

use Illuminate\Console\Command;
use Laravel\Sail\Console\Concerns\InteractsWithDockerComposeServices;

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
        if ($this->option('overwrite')) {
            $this->comment('Overwriting environment variables');
            $this->createEnvFile();
        }

        // Create .env file if it doesn't exist
        if (! file_exists($this->laravel->basePath('.env'))) {
            $this->warn('No .env file found. Creating .env file from .env.example');
            $this->createEnvFile();
        }

        if (! file_exists($this->laravel->basePath('docker-compose.yml'))) {
            $this->error('docker-compose.yml not found. Please run "php artisan sail:install" first.');

            return 1;
        }

        $services = $this->getServicesFromCompose();

        $this->comment('Detected services from docker-compose.yml: ['.implode(',', $services).']');

        $this->replaceEnvVariables($services);

        $this->info('Successfully configured .env file.');
    }

    protected function getServicesFromCompose(): array
    {
        $dockerComposeContent = file_get_contents($this->laravel->basePath('docker-compose.yml'));

        $regex = '/'.implode('|', array_map(function ($service) {
            return '(?<=[^\S]\s)'.$service.'(?=:)'; // Match service name followed by ':' (e.g. mysql:) and preceded only by whitespace
        }, $this->services)).'/';

        preg_match_all($regex, $dockerComposeContent, $matches);

        return array_values($matches[0]);
    }

    protected function createEnvFile(): void
    {
        copy($this->laravel->basePath('.env.example'), $this->laravel->basePath('.env'));
    }
}
