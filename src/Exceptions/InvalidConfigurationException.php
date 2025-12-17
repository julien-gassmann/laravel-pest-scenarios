<?php

declare(strict_types=1);

namespace Jgss\LaravelPestScenarios\Exceptions;

use Jgss\LaravelPestScenarios\Exceptions\Traits\SkipOrFail;
use PHPUnit\Framework\AssertionFailedError;

final class InvalidConfigurationException extends AssertionFailedError
{
    use SkipOrFail;

    protected static string $key = 'configuration';

    public static function unknownKey(string $resolverName, string $key): AssertionFailedError
    {
        $availableKeys = implode(', ', array_keys((array) config('pest-scenarios.resolvers.'.$resolverName, [])));

        return self::skipOrFail(
            "Unknown resolver key '$key' in 'resolvers.".$resolverName."'. ".
            'Available keys: '.$availableKeys
        );
    }
}
