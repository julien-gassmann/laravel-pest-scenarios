<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Config;

use Closure;
use PHPUnit\Framework\SkippedTestSuiteError;

/**
 * @template T
 */
abstract class BaseConfigResolver
{
    protected static string $key;

    /**
     * @return ?T
     *
     * @throws SkippedTestSuiteError if resolver is not defined
     */
    public static function get(string $name)
    {
        /** @var array<array-key, (Closure(): ?T)|T|void> $resolvers */
        $resolvers = config('pest-scenarios.resolvers.'.static::$key, []);

        if (! array_key_exists($name, $resolvers)) {
            throw new SkippedTestSuiteError(
                "Unknown resolver key '$name' in 'resolvers.".static::$key."'. ".
                'Available keys: '.implode(', ', array_keys($resolvers))
            );
        }

        return is_callable($resolvers[$name])
            ? $resolvers[$name]()
            : $resolvers[$name];
    }
}
