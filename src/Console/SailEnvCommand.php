<?php

namespace Jedymatt\LaravelSailEnv\Console;

use Illuminate\Console\Command;
use Laravel\Sail\Console\Concerns\InteractsWithDockerComposeServices;
use Symfony\Component\Yaml\Yaml;

class SailEnvCommand extends Command
{
    use InteractsWithDockerComposeServices;

    private const COMPOSE_FILES = [
        'compose.yaml',
        'compose.yml',
        'docker-compose.yml',
        'docker-compose.yaml',
    ];

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
        $composeFile = $this->findComposeFile();

        if ($composeFile === null) {
            $this->error('No compose file found (compose.yaml or docker-compose.yml). Please run "php artisan sail:install" first.');

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

        $services = $this->getServicesFromCompose($composeFile);

        $this->comment('Detected services from '.basename($composeFile).': ['.implode(',', $services).']');

        $this->replaceEnvVariables($services);

        $this->info('Successfully configured .env file.');
    }

    /**
     * Sail writes compose.yaml since v1.46.0 and docker-compose.yml before it.
     * Check names in the order `docker compose` does when COMPOSE_FILE isn't set.
     */
    protected function findComposeFile(): ?string
    {
        foreach (self::COMPOSE_FILES as $file) {
            if (file_exists($path = $this->laravel->basePath($file))) {
                return $path;
            }
        }

        return null;
    }

    protected function getServicesFromCompose(string $composeFile): array
    {
        $compose = Yaml::parseFile($composeFile);

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
