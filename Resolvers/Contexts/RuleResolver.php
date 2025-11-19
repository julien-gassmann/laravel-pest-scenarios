<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Illuminate\Contracts\Validation\ValidationRule;
use PHPUnit\Framework\SkippedTestSuiteError;

abstract class RuleResolver
{
    /**
     * @param  class-string<ValidationRule>  $ruleClass
     * @param  array<string, mixed>  $payload
     * @param  array<int, mixed>  $parameters
     *
     * @throws SkippedTestSuiteError if the rule cannot be found
     */
    public static function resolve(string $ruleClass, array $payload, array $parameters = []): ValidationRule
    {
        if (! class_exists($ruleClass)) {
            throw new SkippedTestSuiteError("Unable to find rule class : '$ruleClass'.");
        }

        $rule = new $ruleClass(...$parameters);

        if (method_exists($rule, 'setData')) {
            $rule->setData($payload);
        }

        return $rule;
    }
}
