# Laravel Keys

[![Latest Version on Packagist](https://img.shields.io/packagist/v/iak/keys.svg?style=flat-square)](https://packagist.org/packages/iak/keys)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/iak/keys/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/iak/keys/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/iak/keys/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/iak/keys/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/iak/keys.svg?style=flat-square)](https://packagist.org/packages/iak/keys)

## The problem

We use string keys all across our application. We got keys for cache, rate limiting, queued jobs, sessions, broadcast channels, container aliases and so much more.

These keys can be hard to keep track of. You also need to remember what convention you used, sometimes build up keys from several dynamic values such as id's, and it's hard to get an overview of what keys are actually in use. It's also easy for typos to sneak in, as these are just strings.

This package solves this issue, by centralizing where your keys are defined, and give you a consistent and easy way to access them.

## Features

- **Configurable Key Templates**: Define key formats in configuration files
- **Dynamic Parameters**: Define your dynamic values in the configuration, and the key class will fill them in for you
- **Parameter Validation**: Automatic validation of required parameters and detection of extra parameters
- **Multiple Key Types**: Built-in support for cache, queue, event, session, and many more key types
- **Laravel Integration**: Seamless integration with Laravel's configuration system

## Installation

You can install the package via composer:

```bash
composer require iak/keys
```

Then publish the config file with:

```bash
php artisan vendor:publish --tag="keys-config"
```

## Usage

First, define your key templates in the configuration file:

```php
// config/keys.php
return [
    'cache' => [
        'product-data' => 'product:{productId},variant:{variantId}',
    ],
    'limit' => [
        'password-reset' => 'password-reset:{userId}' 
    ]
];
```

Then access the keys using the Key helper class.

```php
use Iak\Key\Key;

// Using named parameters
Key::cache('product-data', productId: 123, variantId: 456);

// Using array parameters
Key::cache('product-data', [
    'productId' => 123,
    'variantId' => 456
]);

// Using parameter position
Key::cache('user.action', 123, 456);

// The result for all above: "product:123,variant:456"

```

### Built-in Key Types

The package comes with many built-in key types:

```php
// Cache keys
Key::cache('user.profile', id: 123);

// Queue keys
Key::queue('email.notification', type: 'welcome', userId: 456);

// Event keys
Key::event('user.action', action: 'login', userId: 789);

// Session keys
Key::session('user.data', userId: 123, sessionId: 'abc123');

// Job keys
Key::job('process.data', jobType: 'import', priority: 'high');

// Lock keys
Key::lock('', version: 'v1', resource: 'users');
```

There are support for many different types of keys. 
For example: tag, lock, channel, broadcast, limit, middleware, 
view, translation, command, container, feature, notification, throttle,
disk, policy, guard, schedule, tenant, experiment, test, mail, service,
flash, alias, provider, raw, config

### Dynamic Method Calling

You can also use dynamic method calling for custom key types:

```php
// Define a custom key type in config
// config/keys.php
return [
    'custom' => [
        'api.request' => 'api:{endpoint}:{method}:{timestamp}',
    ],
];

// Use it dynamically
$key = Key::custom('api.request', 
    endpoint: 'users',
    method: 'GET',
    timestamp: time()
);
// Result: "api:users:GET:1234567890"
```

### Error Handling

The package validates parameters and provides helpful error messages:

```php
// Missing required parameter
Key::cache('user.profile'); 
// Throws: Missing required parameters: id

// Extra parameter not in template
Key::cache('user.profile', id: 123, extra: 'value'); // Throws: Extra parameters: extra

// Non-existent key
Key::cache('nonexistent.key', id: 123); // Throws: Key not found in config
```

## Configuration

### Key Template Format

Key templates use curly braces `{}` to define parameter placeholders:

```php
'user.profile' => 'user:profile:{id}',
'product.details' => 'product:{category}:{id}:{variant}',
'api.request' => 'api:{version}:{endpoint}:{method}',
```

### Parameter Rules

- All parameters defined in the template must be provided
- Extra parameters not defined in the template will cause an exception
- The same parameter can be used multiple times in a template
- Parameters can be strings, numbers, or any scalar value

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

## Credits

- [Isak Berglind](https://github.com/iaK)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
