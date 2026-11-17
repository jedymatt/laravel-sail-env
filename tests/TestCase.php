<?php

namespace Jedymatt\LaravelSailEnv\Tests;

use Illuminate\Filesystem\Filesystem;
use Jedymatt\LaravelSailEnv\LaravelSailEnvServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Yaml\Yaml;

abstract class TestCase extends Orchestra
{
    /**
     * A throwaway project folder that sail:env reads and writes.
     *
     * @var string
     */
    protected $projectPath;

    /**
     * @var string
     */
    private $originalBasePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectPath = sys_get_temp_dir().'/laravel-sail-env-'.uniqid();
        mkdir($this->projectPath);

        $this->originalBasePath = $this->app->basePath();
        $this->app->setBasePath($this->projectPath);
    }

    protected function tearDown(): void
    {
        $this->app->setBasePath($this->originalBasePath);
        (new Filesystem)->deleteDirectory($this->projectPath);

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [LaravelSailEnvServiceProvider::class];
    }

    protected function writeComposeFile(string $fileName, array $services): void
    {
        $compose = ['services' => array_fill_keys($services, ['image' => 'example'])];

        $this->writeProjectFile($fileName, Yaml::dump($compose, 4));
    }

    protected function writeProjectFile(string $fileName, string $contents): void
    {
        file_put_contents($this->projectPath.'/'.$fileName, $contents);
    }

    protected function readProjectFile(string $fileName): string
    {
        return file_get_contents($this->projectPath.'/'.$fileName);
    }

    protected function envValue(string $key): ?string
    {
        $found = preg_match('/^'.preg_quote($key, '/').'=(.*)$/m', $this->readProjectFile('.env'), $matches);

        return $found ? $matches[1] : null;
    }
}
