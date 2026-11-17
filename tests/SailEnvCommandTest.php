<?php

namespace Jedymatt\LaravelSailEnv\Tests;

use Illuminate\Support\Facades\Artisan;

class SailEnvCommandTest extends TestCase
{
    /**
     * The values Sail rewrites, in a form every supported Sail version
     * (1.20 and up) replaces the same way.
     */
    private const ENV_EXAMPLE = <<<'ENV'
APP_NAME=Laravel
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
REDIS_HOST=127.0.0.1

ENV;

    protected function setUp(): void
    {
        parent::setUp();

        $this->writeProjectFile('.env.example', self::ENV_EXAMPLE);
    }

    public function test_it_configures_env_from_compose_yaml()
    {
        $this->writeComposeFile('compose.yaml', ['laravel.test', 'pgsql', 'redis']);
        $this->writeProjectFile('.env', self::ENV_EXAMPLE);

        $exitCode = Artisan::call('sail:env');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Detected services from compose.yaml: [pgsql,redis]', Artisan::output());
        $this->assertSame('pgsql', $this->envValue('DB_CONNECTION'));
        $this->assertSame('pgsql', $this->envValue('DB_HOST'));
        $this->assertSame('redis', $this->envValue('REDIS_HOST'));
    }

    public function test_it_configures_env_from_legacy_docker_compose_yml()
    {
        $this->writeComposeFile('docker-compose.yml', ['laravel.test', 'mysql', 'redis']);
        $this->writeProjectFile('.env', self::ENV_EXAMPLE);

        $exitCode = Artisan::call('sail:env');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Detected services from docker-compose.yml: [mysql,redis]', Artisan::output());
        $this->assertSame('mysql', $this->envValue('DB_HOST'));
        $this->assertSame('redis', $this->envValue('REDIS_HOST'));
    }

    public function test_it_prefers_compose_yaml_over_docker_compose_yml()
    {
        $this->writeComposeFile('compose.yaml', ['pgsql']);
        $this->writeComposeFile('docker-compose.yml', ['mysql']);
        $this->writeProjectFile('.env', self::ENV_EXAMPLE);

        Artisan::call('sail:env');

        $this->assertStringContainsString('Detected services from compose.yaml: [pgsql]', Artisan::output());
        $this->assertSame('pgsql', $this->envValue('DB_HOST'));
    }

    public function test_it_prefers_docker_compose_yml_over_docker_compose_yaml_like_docker_compose_does()
    {
        $this->writeComposeFile('docker-compose.yaml', ['pgsql']);
        $this->writeComposeFile('docker-compose.yml', ['mysql']);
        $this->writeProjectFile('.env', self::ENV_EXAMPLE);

        Artisan::call('sail:env');

        $this->assertStringContainsString('Detected services from docker-compose.yml: [mysql]', Artisan::output());
        $this->assertSame('mysql', $this->envValue('DB_HOST'));
    }

    public function test_it_ignores_services_sail_does_not_manage()
    {
        $this->writeComposeFile('compose.yaml', ['laravel.test', 'nginx', 'redis', 'my-worker']);
        $this->writeProjectFile('.env', self::ENV_EXAMPLE);

        Artisan::call('sail:env');

        $this->assertStringContainsString('Detected services from compose.yaml: [redis]', Artisan::output());
    }

    public function test_it_creates_env_from_env_example_when_env_is_missing()
    {
        $this->writeComposeFile('compose.yaml', ['redis']);

        $exitCode = Artisan::call('sail:env');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('No .env file found', Artisan::output());
        $this->assertSame('Laravel', $this->envValue('APP_NAME'));
        $this->assertSame('redis', $this->envValue('REDIS_HOST'));
    }

    public function test_it_keeps_existing_env_values_by_default()
    {
        $this->writeComposeFile('compose.yaml', ['redis']);
        $this->writeProjectFile('.env', self::ENV_EXAMPLE."MY_SECRET=keep-me\n");

        Artisan::call('sail:env');

        $this->assertSame('keep-me', $this->envValue('MY_SECRET'));
        $this->assertSame('redis', $this->envValue('REDIS_HOST'));
    }

    public function test_overwrite_option_resets_env_from_env_example()
    {
        $this->writeComposeFile('compose.yaml', ['redis']);
        $this->writeProjectFile('.env', self::ENV_EXAMPLE."MY_SECRET=drop-me\n");

        Artisan::call('sail:env', ['--overwrite' => true]);

        $this->assertNull($this->envValue('MY_SECRET'));
        $this->assertSame('redis', $this->envValue('REDIS_HOST'));
    }

    public function test_it_fails_without_a_compose_file_and_leaves_env_alone()
    {
        $this->writeProjectFile('.env', self::ENV_EXAMPLE);

        $exitCode = Artisan::call('sail:env');

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('No compose file found', Artisan::output());
        $this->assertSame(self::ENV_EXAMPLE, $this->readProjectFile('.env'));
    }
}
