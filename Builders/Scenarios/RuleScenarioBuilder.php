<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Jgss\LaravelPestScenarios\Definitions\Contexts\RuleContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Rules\InvalidRuleScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Rules\ValidRuleScenario;
use Pest\PendingCalls\TestCall;

final readonly class RuleScenarioBuilder
{
    /**
     * @param  array<int, mixed>  $parameters
     */
    public function valid(
        string $description,
        RuleContext $context,
        mixed $value,
        array $parameters = [],
    ): TestCall {
        $scenario = new ValidRuleScenario(
            description: $description,
            context: $context,
            value: $value,
            parameters: $parameters,
        );

        return $scenario->defineTest();
    }

    /**
     * @param  array<int, mixed>  $parameters
     */
    public function invalid(
        string $description,
        RuleContext $context,
        string $errorMessage,
        mixed $value,
        array $parameters = [],
    ): TestCall {
        $scenario = new InvalidRuleScenario(
            description: $description,
            context: $context,
            errorMessage: $errorMessage,
            value: $value,
            parameters: $parameters,
        );

        return $scenario->defineTest();
    }
}
