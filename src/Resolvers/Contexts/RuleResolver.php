<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Illuminate\Contracts\Validation\ValidationRule;
use Jgss\LaravelPestScenarios\Exceptions\ResolutionFailedException;
use Throwable;

final readonly class RuleResolver
{
    /**
     * @param  class-string<ValidationRule>  $ruleClass
     * @param  array<string, mixed>  $payload
     * @param  array<int, mixed>  $parameters
     *
     * @throws Throwable
     */
    public static function resolve(string $ruleClass, array $payload, array $parameters = []): ValidationRule
    {
        if (! class_exists($ruleClass)) {
            throw ResolutionFailedException::ruleClassNotFound($ruleClass);
        }

        $rule = new $ruleClass(...$parameters);

        if (method_exists($rule, 'setData')) {
            $rule->setData($payload);
        }

        return $rule;
    }
}
