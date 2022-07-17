# laravel-env-sail

Configures .env file to match the sail environtment variable's requirements.

## Why is this created?

To configure the .env file without having to run `php artisan sail:install` again just to replace the variables in .env file.
It would be tedious specially when you have custom configuration in your `docker-compose.yml` because `sail:install` command overwrites your `docker-compose.yml` file.


## How?

It reads the services inside docker-compose.yml file. Then, it extends the sail's InstallCommand and uses its replaceEnvVariables method.

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
