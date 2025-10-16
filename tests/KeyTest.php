<?php

use Iak\Key\Key;

// Data provider for all methods
dataset('methodProvider', [
    'cache', 'queue', 'event', 'tag', 'lock', 'channel', 'broadcast', 'rateLimit',
    'session', 'job', 'middleware', 'route', 'view', 'translation', 'command',
    'container', 'feature', 'notification', 'throttle', 'disk', 'policy', 'guard',
    'schedule', 'tenant', 'experiment', 'test', 'mail', 'service', 'flash',
    'alias', 'provider', 'raw', 'config'
]);

it('generates cache key', function () {
    config()->set('keys.cache.product.book', 'product-book:{id}');
    
    expect(Key::cache('product.book', ['id' => 123]))->toBe('product-book:123');
});

it('handles multiple parameters', function () {
    config()->set('keys.cache.test.multiple', 'test:{first}:{second}');
    
    expect(Key::cache('test.multiple', [
        'first' => 'abc',
        'second' => 'xyz'
    ]))->toBe('test:abc:xyz');
});

it('generates cache key with named parameters', function () {
    config()->set('keys.cache.test.multiple', 'test:{first}:{second}');
    
    expect(Key::cache('test.multiple',
        first: 'abc',
        second: 'xyz'
    ))->toBe('test:abc:xyz');
});

it('throws exception for non existent cache key', function () {
    expect(fn () => Key::cache('non.existent', ['id' => 123]))
        ->toThrow(\InvalidArgumentException::class, "Key 'cache.non.existent' not found in config/keys.php");
});

it('throws exception when required parameters are missing', function () {
    config()->set('keys.cache.test.multiple', 'test:{first}:{second}');

    expect(fn () => Key::cache('test.multiple', ['first' => 'abc']))
        ->toThrow(\InvalidArgumentException::class, "Key 'cache.test.multiple' is missing required parameters: second");
});

it('throws exception when extra parameters provided', function () {
    config()->set('keys.cache.test.single', 'test:{id}');

    expect(fn () => Key::cache('test.single', ['id' => 'abc', 'extra' => 'xyz']))
        ->toThrow(\InvalidArgumentException::class, "Key 'cache.test.single' was given extra parameters: extra");
});

it('allows parameter to be used multiple times', function () {
    config()->set('keys.cache.test.repeat', '{id}:{id}:{id}');
    
    expect(Key::cache('test.repeat', ['id' => '123']))->toBe('123:123:123');
});

it('generates cache key with positional parameters', function () {
    config()->set('keys.cache.product.book', 'product-book:{id}');
    
    expect(Key::cache('product.book', '123'))->toBe('product-book:123');
});

it('handles multiple positional parameters', function () {
    config()->set('keys.cache.test.multiple', 'test:{first}:{second}');
    
    expect(Key::cache('test.multiple', 'abc', 'xyz'))->toBe('test:abc:xyz');
});

it('throws exception when positional parameter count mismatch', function () {
    config()->set('keys.cache.test.multiple', 'test:{first}:{second}');

    expect(fn () => Key::cache('test.multiple', 'abc'))
        ->toThrow(\InvalidArgumentException::class, "Key 'cache.test.multiple' expects 2 parameters, 1 given");
});

it('allows positional parameter to be used multiple times', function () {
    config()->set('keys.cache.test.repeat', '{id}:{id}:{id}');
    
    expect(Key::cache('test.repeat', '123', '123', '123'))->toBe('123:123:123');
});

it('handles empty parameter array', function () {
    config()->set('keys.cache.static.key', 'static-key');
    
    expect(Key::cache('static.key', []))->toBe('static-key');
});

it('handles zero parameters', function () {
    config()->set('keys.cache.static.key', 'static-key');
    
    expect(Key::cache('static.key'))->toBe('static-key');
});

it('handles complex nested parameters', function () {
    config()->set('keys.cache.complex.nested', 'cache:{user_id}:{action}:{resource}:{timestamp}');
    
    expect(Key::cache('complex.nested', [
        'user_id' => 123,
        'action' => 'view',
        'resource' => 'document',
        'timestamp' => 1234567890
    ]))->toBe('cache:123:view:document:1234567890');
});

it('handles parameters with special characters', function () {
    config()->set('keys.cache.special.chars', 'special:{key}:{value}');
    
    expect(Key::cache('special.chars', ['key' => 'user@domain.com', 'value' => 'test-value_123']))->toBe('special:user@domain.com:test-value_123');
});

it('handles numeric parameters', function () {
    config()->set('keys.cache.numeric.test', 'numeric:{id}:{count}');
    
    expect(Key::cache('numeric.test', ['id' => 0, 'count' => 42]))->toBe('numeric:0:42');
});


// Data provider tests for all methods with named parameters
it('works with all methods using named parameters', function (string $method) {
    config()->set("keys.{$method}.test.key", "{$method}:{id}:{type}");
    
    $result = Key::$method('test.key', ['id' => 123, 'type' => 'test']);
    expect($result)->toBe("{$method}:123:test");
})->with('methodProvider');

// Data provider tests for all methods with positional parameters
it('works with all methods using positional parameters', function (string $method) {
    config()->set("keys.{$method}.test.key", "{$method}:{id}:{type}");
    
    $result = Key::$method('test.key', 123, 'test');
    expect($result)->toBe("{$method}:123:test");
})->with('methodProvider');

// Data provider tests for all methods with array parameters
it('works with all methods using array parameters', function (string $method) {
    config()->set("keys.{$method}.test.key", "{$method}:{id}:{type}");
    
    $result = Key::$method('test.key', ['id' => 123, 'type' => 'test']);
    expect($result)->toBe("{$method}:123:test");
})->with('methodProvider');

// Data provider tests for error cases - missing key
it('throws exception for non existent key across all methods', function (string $method) {
    expect(fn () => Key::$method('non.existent', ['id' => 123]))
        ->toThrow(\InvalidArgumentException::class, "Key '{$method}.non.existent' not found in config/keys.php");
})->with('methodProvider');

// Data provider tests for error cases - missing parameters
it('throws exception when required parameters are missing across all methods', function (string $method) {
    config()->set("keys.{$method}.test.multiple", "{$method}:{first}:{second}");

    expect(fn () => Key::$method('test.multiple', ['first' => 'abc']))
        ->toThrow(\InvalidArgumentException::class, "Key '{$method}.test.multiple' is missing required parameters: second");
})->with('methodProvider');

// Data provider tests for error cases - extra parameters
it('throws exception when extra parameters provided across all methods', function (string $method) {
    config()->set("keys.{$method}.test.single", "{$method}:{id}");

    expect(fn () => Key::$method('test.single', ['id' => 'abc', 'extra' => 'xyz']))
        ->toThrow(\InvalidArgumentException::class, "Key '{$method}.test.single' was given extra parameters: extra");
})->with('methodProvider');

// Data provider tests for error cases - positional parameter count mismatch
it('throws exception when positional parameter count mismatch across all methods', function (string $method) {
    config()->set("keys.{$method}.test.multiple", "{$method}:{first}:{second}");

    expect(fn () => Key::$method('test.multiple', 'abc'))
        ->toThrow(\InvalidArgumentException::class, "Key '{$method}.test.multiple' expects 2 parameters, 1 given");
})->with('methodProvider');

// Tests for dynamic method calling
it('supports dynamic method calling', function () {
    config()->set('keys.custom.dynamic', 'custom:{value}');
    
    expect(Key::custom('dynamic', ['value' => 'test']))->toBe('custom:test');
});

it('supports dynamic method calling with positional parameters', function () {
    config()->set('keys.another.test', 'another:{param}');
    
    expect(Key::another('test', 'value'))->toBe('another:value');
});
