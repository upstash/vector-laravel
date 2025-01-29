# Upstash Vector SDK for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/upstash/vector-laravel.svg?style=flat-square)](https://packagist.org/packages/upstash/vector-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/upstash/vector-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/upstash/vector-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/upstash/vector-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/upstash/vector-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/upstash/vector-laravel.svg?style=flat-square)](https://packagist.org/packages/upstash/vector-laravel)

Upstash Vector is an HTTP serverless Vector Database.

You can store, query, and retrieve vectors from your application, use it to power your search, and more.

You can read more about Upstash Vector [here](https://docs.upstash.com/vector).

## Installation

You can install the package via composer:

```bash
composer require upstash/vector-laravel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="vector-config"
```

This is the contents of the published config file:

```php
return [
    'default' => env('UPSTASH_VECTOR_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'url' => env('UPSTASH_VECTOR_REST_URL'),
            'token' => env('UPSTASH_VECTOR_REST_TOKEN'),
        ],
    ],
];
```

## Usage

```php
use Upstash\Vector\Laravel\Facades\Vector;

$info = Vector::getInfo();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
