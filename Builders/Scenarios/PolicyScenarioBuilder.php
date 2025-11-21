<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Closure;
use Jgss\LaravelPestScenarios\Definitions\Contexts\PolicyContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Policies\InvalidPolicyScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Policies\ValidPolicyScenario;
use Pest\PendingCalls\TestCall;
use Throwable;

class PolicyScenarioBuilder
{
    /**
     * @param  null|Closure(): array<int, mixed>  $parameters
     * @param  null|Closure(): mixed  $expectedOutput
     */
    public static function valid(
        string $description,
        PolicyContext $context,
        string $method,
        ?Closure $parameters = null,
        ?Closure $expectedOutput = null,
    ): TestCall {
        $scenario = new ValidPolicyScenario(
            description: $description,
            context: $context,
            method: $method,
            parameters: $parameters ?? fn () => [],
            expectedOutput: $expectedOutput ?? fn () => true,
        );

        return $scenario->defineTest();
    }

    /**
     * @param  null|Closure(): array<int, mixed>  $parameters
     */
    public function invalid(
        string $description,
        PolicyContext $context,
        string $method,
        ?Closure $parameters = null,
        ?Closure $expectedOutput = null,
        ?Throwable $expectedException = null,
    ): TestCall {
        $scenario = new InvalidPolicyScenario(
            description: $description,
            context: $context,
            method: $method,
            parameters: $parameters ?? fn () => [],
            expectedOutput: $expectedOutput ?? fn () => false,
            expectedException: $expectedException,
        );

        return $scenario->defineTest();
    }
}
