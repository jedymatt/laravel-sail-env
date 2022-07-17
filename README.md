# laravel-env-sail

Configures .env file to match the sail environtment variable's requirements.

## How?

It reads the services inside docker-compose.yml file.
It extends the sail's InstallCommand and uses its replaceEnvVariables method.

## Installation

```bash
composer require jedymatt/laravel-env-sail
```

## Usage

```bash
php artisan env-sail
```

## Found Bugs?

Report to [GitHub Issues](https://github.com/jedymatt/laravel-env-sail/issues)

## Have suggestions?

Report to [GitHub Issues](https://github.com/jedymatt/laravel-env-sail/issues)
