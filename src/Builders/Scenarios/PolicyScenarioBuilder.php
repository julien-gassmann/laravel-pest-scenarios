<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Closure;
use Jgss\LaravelPestScenarios\Definitions\Contexts\PolicyContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Policies\InvalidPolicyScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Policies\ValidPolicyScenario;
use Jgss\LaravelPestScenarios\Support\PestTestCallFactory;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Pest\PendingCalls\TestCall;
use Throwable;

final readonly class PolicyScenarioBuilder
{
    public function __construct(
        private TestCallFactoryContract $factory = new PestTestCallFactory,
    ) {}

    /**
     * @param  null|Closure(): array<int, mixed>  $parameters
     * @param  null|Closure(): mixed  $expectedOutput
     */
    public function valid(
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
            parameters: $parameters ?? fn (): array => [],
            expectedOutput: $expectedOutput ?? fn (): true => true,
        );

        return $scenario->defineTest($this->factory);
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
            parameters: $parameters ?? fn (): array => [],
            expectedOutput: $expectedOutput ?? fn (): false => false,
            expectedException: $expectedException,
        );

        return $scenario->defineTest($this->factory);
    }
}
