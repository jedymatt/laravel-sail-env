# laravel-sail-env

![Packagist Downloads](https://img.shields.io/packagist/dm/jedymatt/laravel-sail-env?style=flat-square)
![Packagist Version](https://img.shields.io/packagist/v/jedymatt/laravel-sail-env?style=flat-square)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/jedymatt/laravel-sail-env?style=flat-square)

Configures .env file to match the sail environment variable's requirements.


## Installation

Install as development dependency:

```bash
composer require --dev jedymatt/laravel-sail-env
```

## Usage

To configure .env file:

```bash
php artisan sail:env
```


## Why is this created?

To configure the .env file without having to run `php artisan sail:install` again just to replace the variables in .env file.
It would be tedious specially when you have custom configuration in your `docker-compose.yml` because `sail:install` command overwrites your `docker-compose.yml` file.


## How?

It reads the services inside docker-compose.yml file.
It extends the sail's `InstallCommand` class and uses its `replaceEnvVariables` method.


## Found Bugs?

Report to [GitHub Issues](https://github.com/jedymatt/laravel-env-sail/issues)

## Have suggestions?

Report to [GitHub Issues](https://github.com/jedymatt/laravel-env-sail/issues)
