<?php

declare(strict_types=1);

namespace Jgss\LaravelPestScenarios\Exceptions;

use Exception;
use Jgss\LaravelPestScenarios\Exceptions\Traits\SkipOrFail;
use Throwable;

final class InvalidConfigurationException extends Exception
{
    use SkipOrFail;

    protected static string $key = 'configuration';

    public static function unknownKey(string $resolverName, string $key): Throwable
    {
        $availableKeys = implode(', ', array_keys((array) config('pest-scenarios.resolvers.'.$resolverName, [])));

        return self::skipOrFail(
            "Unknown resolver key '$key' in 'resolvers.".$resolverName."'. ".
            'Available keys: '.$availableKeys
        );
    }
}
