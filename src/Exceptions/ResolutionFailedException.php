<?php

declare(strict_types=1);

namespace Jgss\LaravelPestScenarios\Exceptions;

use Exception;
use Jgss\LaravelPestScenarios\Exceptions\Traits\SkipOrFail;
use Throwable;

final class ResolutionFailedException extends Exception
{
    use SkipOrFail;

    protected static string $key = 'resolution';

    public static function formRequestNotFound(string $formRequestClass): Throwable
    {
        return self::skipOrFail("Unable to find form request class : '$formRequestClass'.");
    }

    public static function formRequestNotExtending(string $formRequestClass): Throwable
    {
        return self::skipOrFail("Provided class '$formRequestClass' doesn't extend FormRequest.");
    }

    public static function formRequestModelNotFound(string $field, string $value): Throwable
    {
        return self::skipOrFail("Unable to find model '$field' with value '$value'.");
    }

    public static function policyClassNotFound(string $policyClass): Throwable
    {
        return self::skipOrFail("Unable to find policy class : '$policyClass'.");
    }

    public static function policyMethodNotFound(string $policyClass, string $policyMethod): Throwable
    {
        return self::skipOrFail("Unable to find method '$policyMethod' in '$policyClass' class.");
    }

    public static function routeNameNotFound(string $routeName): Throwable
    {
        return self::skipOrFail("Unable to find route: '$routeName'.");
    }

    public static function routeParametersCasting(): Throwable
    {
        return self::skipOrFail('Unable to cast route parameters as string.');
    }

    public static function routeMethodNotFound(string $routeName): Throwable
    {
        return self::skipOrFail("Unable to resolve HTTP method for route: '$routeName'.");
    }

    public static function ruleClassNotFound(string $ruleClass): Throwable
    {
        return self::skipOrFail("Unable to find rule class : '$ruleClass'.");
    }
}
