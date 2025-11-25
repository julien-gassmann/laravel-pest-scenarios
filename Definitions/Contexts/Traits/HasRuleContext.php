<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Contracts\Validation\ValidationRule;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\RuleResolver;
use PHPUnit\Framework\SkippedTestSuiteError;

trait HasRuleContext
{
    // ------------------- Getters -------------------

    /**
     * @return class-string<ValidationRule>
     */
    public function getRuleClass(): string
    {
        return $this->ruleClass
            ?? throw new SkippedTestSuiteError('Rule class is missing in context definition');
    }

    // ------------------- Resolvers -------------------

    /**
     * @param  array<int, mixed>  $parameters
     */
    public function getRuleInstance(array $parameters = []): ValidationRule
    {
        return RuleResolver::resolve($this->getRuleClass(), $this->payload, $parameters);
    }
}
