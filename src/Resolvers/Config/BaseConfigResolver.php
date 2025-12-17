<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Config;

use Closure;
use Jgss\LaravelPestScenarios\Exceptions\InvalidConfigurationException;

/**
 * @template T
 */
abstract class BaseConfigResolver
{
    protected static string $key;

    /**
     * @return ?T
     */
    public static function get(string $name)
    {
        /** @var array<array-key, (Closure(): ?T)|T|void> $resolvers */
        $resolvers = config('pest-scenarios.resolvers.'.static::$key, []);

        if (! array_key_exists($name, $resolvers)) {
            throw InvalidConfigurationException::unknownKey(static::$key, $name);
        }

        return is_callable($resolvers[$name])
            ? $resolvers[$name]()
            : $resolvers[$name];
    }
}
