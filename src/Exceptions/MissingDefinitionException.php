<?php

declare(strict_types=1);

namespace Jgss\LaravelPestScenarios\Exceptions;

use Exception;
use Throwable;

final class MissingDefinitionException extends Exception
{
    public static function commandSignature(): Throwable
    {
        return new self('Artisan command signature is missing in context definition.');
    }

    public static function formRequestClass(): Throwable
    {
        return new self('FormRequest class is missing in context definition.');
    }

    public static function policyClass(): Throwable
    {
        return new self('Policy class is missing in context definition.');
    }

    public static function routeName(): Throwable
    {
        return new self('Route name is missing in context definition.');
    }

    public static function ruleClass(): Throwable
    {
        return new self('Rule class is missing in context definition.');
    }
}
