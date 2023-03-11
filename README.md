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
It would be tedious specially when you have custom configuration in your *docker-compose.yml* because `sail:install` command overwrites your *docker-compose.yml* file.


## How?

__[v1.1.5 or newer]__ It reads the services of sail inside docker-compose.yml file using yaml parser.
Then, It uses the sail's _InteractsWithDockerComposeServices_ trait to replace env variables so that it keeps in sync to _laravel/sail_ package.

__[v1.1.4 or older]__ It reads the services of sail inside docker-compose.yml file using regex.
Then, It uses the sail's *InstallCommand's replaceEnvVariables* method so that it keeps in sync to *laravel/sail* package.


## Notes

v1.1.5 or newer is only compatible to laravel/sail v1.20.0 and up.

## Found Bugs?

Report to [GitHub Issues](https://github.com/jedymatt/laravel-env-sail/issues)

## Have suggestions?

Feel free to create discussion in [GitHub Discussions](https://github.com/jedymatt/laravel-sail-env/discussions)
