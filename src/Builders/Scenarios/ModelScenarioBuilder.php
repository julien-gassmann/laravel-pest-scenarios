<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ModelContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Models\InvalidModelScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Models\ValidModelScenario;
use Jgss\LaravelPestScenarios\Support\PestTestCallFactory;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;
use Throwable;

final readonly class ModelScenarioBuilder
{
    public function __construct(
        private TestCallFactoryContract $factory = new PestTestCallFactory,
    ) {}

    /**
     * @param  Closure(): mixed  $input
     * @param  null|Closure(): mixed  $expectedOutput
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function valid(
        string $description,
        Closure $input,
        ?ModelContext $context = null,
        ?Closure $expectedOutput = null,
        array $databaseAssertions = [],
    ): FakeTestCall|TestCall {
        $scenario = new ValidModelScenario(
            description: $description,
            context: $context ?? Context::forModel()->with(),
            input: $input,
            expectedOutput: $expectedOutput ?? fn () => null,
            databaseAssertions: $databaseAssertions,
        );

        return $scenario->defineTest($this->factory);
    }

    /**
     * @param  Closure(): mixed  $input
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function invalid(
        string $description,
        Closure $input,
        Throwable $expectedException,
        ?ModelContext $context = null,
        array $databaseAssertions = [],
    ): FakeTestCall|TestCall {
        $scenario = new InvalidModelScenario(
            description: $description,
            context: $context ?? Context::forModel()->with(),
            input: $input,
            expectedException: $expectedException,
            databaseAssertions: $databaseAssertions,
        );

        return $scenario->defineTest($this->factory);
    }
}
