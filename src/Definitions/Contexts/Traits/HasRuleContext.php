<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Contracts\Validation\ValidationRule;
use Jgss\LaravelPestScenarios\Exceptions\MissingDefinitionException;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\RuleResolver;

trait HasRuleContext
{
    // ------------------- Getters -------------------

    /**
     * @return class-string<ValidationRule>
     */
    public function getRuleClass(): string
    {
        return $this->ruleClass ?? throw MissingDefinitionException::ruleClass();
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
