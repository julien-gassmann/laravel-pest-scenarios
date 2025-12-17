<?php

declare(strict_types=1);

namespace Jgss\LaravelPestScenarios\Exceptions;

use Jgss\LaravelPestScenarios\Exceptions\Traits\SkipOrFail;
use PHPUnit\Framework\AssertionFailedError;

final class ResolutionFailedException extends AssertionFailedError
{
    use SkipOrFail;

    protected static string $key = 'resolution';

    public static function formRequestNotFound(string $formRequestClass): AssertionFailedError
    {
        return self::skipOrFail("Unable to find form request class : '$formRequestClass'.");
    }

    public static function formRequestNotExtending(string $formRequestClass): AssertionFailedError
    {
        return self::skipOrFail("Provided class '$formRequestClass' doesn't extend FormRequest.");
    }

    public static function formRequestModelNotFound(string $field, string $value): AssertionFailedError
    {
        return self::skipOrFail("Unable to find model '$field' with value '$value'.");
    }

    public static function policyClassNotFound(string $policyClass): AssertionFailedError
    {
        return self::skipOrFail("Unable to find policy class : '$policyClass'.");
    }

    public static function policyMethodNotFound(string $policyClass, string $policyMethod): AssertionFailedError
    {
        return self::skipOrFail("Unable to find method '$policyMethod' in '$policyClass' class.");
    }

    public static function routeNameNotFound(string $routeName): AssertionFailedError
    {
        return self::skipOrFail("Unable to find route: '$routeName'.");
    }

    public static function routeParametersCasting(): AssertionFailedError
    {
        return self::skipOrFail('Unable to cast route parameters as string.');
    }

    public static function routeMethodNotFound(string $routeName): AssertionFailedError
    {
        return self::skipOrFail("Unable to resolve HTTP method for route: '$routeName'.");
    }

    public static function ruleClassNotFound(string $ruleClass): AssertionFailedError
    {
        return self::skipOrFail("Unable to find rule class : '$ruleClass'.");
    }
}
