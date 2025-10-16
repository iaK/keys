<?php

namespace Iak\Key;

use InvalidArgumentException;

class Key
{
    /**
     * Get a formatted key from the specified section using named parameters.
     *
     * @param  string  $section  The config section (cache, queue, event)
     * @param  string  $key  Dot notation key from config
     * @param  mixed  ...$parameters  Named parameters to be inserted into the key format
     *
     * @throws InvalidArgumentException if the key doesn't exist in config, any of the required parameters are missing, or if any extra parameters are provided.
     */
    protected static function getKey(string $section, string $key, mixed ...$parameters): string
    {
        // If no parameters provided, treat as positional
        if (empty($parameters)) {
            return static::getPositional($section, $key, $parameters);
        }

        // Flatten the parameters array into a single level array, but save the array keys.
        $flattened = collect($parameters)
            ->flatMap(fn ($value, $key) => is_array($value) ? $value : [$key => $value])
            ->toArray();

        // Check if the parameters are numerical keys, if so, treat as positional parameters
        if (array_keys($flattened) === range(0, count($flattened) - 1)) {
            return static::getPositional($section, $key, $flattened);
        }

        return static::get($section, $key, $flattened);
    }

    /**
     * Get a formatted key from the specified section using named parameters.
     *
     * @param  string  $section  The config section (cache, queue, event)
     * @param  string  $key  Dot notation key from config
     * @param  array<string,mixed>  $parameters  Named parameters to be inserted into the key format
     *
     * @throws InvalidArgumentException if the key doesn't exist in config, or if required parameters are missing
     */
    protected static function get(string $section, string $key, array $parameters): string
    {
        $format = config("keys.{$section}.{$key}");

        if ($format === null) {
            throw new InvalidArgumentException("Key '{$section}.{$key}' not found in config/keys.php");
        }

        // Find all placeholders in format {name}
        preg_match_all('/{([^}]+)}/', $format, $matches);
        $requiredParams = $matches[1];

        // Check for missing parameters
        $missingParams = array_diff($requiredParams, array_keys($parameters));

        if (! empty($missingParams)) {
            throw new InvalidArgumentException(
                "Key '{$section}.{$key}' is missing required parameters: ".implode(', ', $missingParams)
            );
        }

        // Check for extra parameters
        $extraParams = array_diff(array_keys($parameters), $requiredParams);
        if (! empty($extraParams)) {
            throw new InvalidArgumentException(
                "Key '{$section}.{$key}' was given extra parameters: ".implode(', ', $extraParams)
            );
        }

        return preg_replace_callback(
            '/{([^}]+)}/',
            fn ($match) => $parameters[$match[1]],
            $format
        );
    }

    /**
     * Get a formatted key from the specified section using positional parameters.
     *
     * @param  string  $section  The config section (cache, queue, event)
     * @param  string  $key  Dot notation key from config
     * @param  array<int,mixed>  $parameters  Positional parameters to be inserted into the key format
     *
     * @throws InvalidArgumentException if the key doesn't exist in config, or if the number of parameters doesn't match
     */
    protected static function getPositional(string $section, string $key, array $parameters): string
    {
        $format = config("keys.{$section}.{$key}");

        if ($format === null) {
            throw new InvalidArgumentException("Key '{$section}.{$key}' not found in config/keys.php");
        }

        // Find all placeholders in format {name}
        preg_match_all('/{([^}]+)}/', $format, $matches);
        $requiredCount = count($matches[1]);
        $providedCount = count($parameters);

        if ($requiredCount !== $providedCount) {
            throw new InvalidArgumentException(
                "Key '{$section}.{$key}' expects {$requiredCount} parameters, {$providedCount} given"
            );
        }

        // Replace placeholders with values in order
        return preg_replace_callback(
            '/{[^}]+}/',
            function () use (&$parameters) {
                return array_shift($parameters);
            },
            $format
        );
    }

    /**
     * Dynamically call the getKey method with the given name and arguments.
     *
     * @param  array<mixed>  $arguments
     */
    public static function __callStatic(string $name, array $arguments): string
    {
        return static::getKey($name, ...$arguments);
    }

    public static function cache(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function queue(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function event(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function tag(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function lock(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function channel(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function broadcast(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function limit(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function session(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function job(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function middleware(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function route(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function view(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function translation(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function command(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function container(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function feature(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function notification(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function throttle(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function disk(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function policy(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function guard(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function schedule(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function tenant(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function experiment(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function test(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function mail(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function service(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function flash(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function alias(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function provider(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function raw(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }

    public static function config(string $key, mixed ...$parameters): string
    {
        return static::getKey(__FUNCTION__, $key, ...$parameters);
    }
}
