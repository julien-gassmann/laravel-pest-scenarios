<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Contracts\Validation\ValidationRule;
use Jgss\LaravelPestScenarios\Exceptions\MissingDefinitionException;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\RuleResolver;
use Throwable;

trait HasRuleContext
{
    // ------------------- Getters -------------------

    /**
     * @return class-string<ValidationRule>
     *
     * @throws Throwable
     */
    public function getRuleClass(): string
    {
        return $this->ruleClass ?? throw MissingDefinitionException::ruleClass();
    }

    // ------------------- Resolvers -------------------

    /**
     * @param  array<int, mixed>  $parameters
     *
     * @throws Throwable
     */
    public function getRuleInstance(array $parameters = []): ValidationRule
    {
        return RuleResolver::resolve($this->getRuleClass(), $this->payload, $parameters);
    }
}
