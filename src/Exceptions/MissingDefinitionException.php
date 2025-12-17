<?php

declare(strict_types=1);

namespace Jgss\LaravelPestScenarios\Exceptions;

use PHPUnit\Framework\AssertionFailedError;

final class MissingDefinitionException extends AssertionFailedError
{
    public static function commandSignature(): MissingDefinitionException
    {
        return new self('Artisan command signature is missing in context definition.');
    }

    public static function formRequestClass(): MissingDefinitionException
    {
        return new self('FormRequest class is missing in context definition.');
    }

    public static function policyClass(): MissingDefinitionException
    {
        return new self('Policy class is missing in context definition.');
    }

    public static function routeName(): MissingDefinitionException
    {
        return new self('Route name is missing in context definition.');
    }

    public static function ruleClass(): MissingDefinitionException
    {
        return new self('Rule class is missing in context definition.');
    }
}
