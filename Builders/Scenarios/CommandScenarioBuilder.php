<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Definitions\Contexts\CommandContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Commands\InvalidCommandScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Commands\ValidCommandScenario;
use Jgss\LaravelPestScenarios\Support\PestTestCallFactory;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

final readonly class CommandScenarioBuilder
{
    public function __construct(
        private TestCallFactoryContract $factory = new PestTestCallFactory,
    ) {}

    /**
     * @param  null|Closure(): string|string  $arguments
     * @param  null|Closure(PendingCommand): PendingCommand  $commandAssertions
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function valid(
        string $description,
        CommandContext $context,
        null|Closure|string $arguments = null,
        ?Closure $commandAssertions = null,
        array $databaseAssertions = [],
    ): FakeTestCall|TestCall {
        $scenario = new ValidCommandScenario(
            description: $description,
            context: $context,
            arguments: $arguments,
            commandAssertions: $commandAssertions,
            databaseAssertions: $databaseAssertions,
        );

        return $scenario->defineTest($this->factory);
    }

    /**
     * @param  null|Closure(): string|string  $arguments
     * @param  null|Closure(PendingCommand): PendingCommand  $commandAssertions
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function invalid(
        string $description,
        CommandContext $context,
        null|Closure|string $arguments = null,
        ?Closure $commandAssertions = null,
        array $databaseAssertions = [],
    ): FakeTestCall|TestCall {
        $scenario = new InvalidCommandScenario(
            description: $description,
            context: $context,
            arguments: $arguments,
            commandAssertions: $commandAssertions,
            databaseAssertions: $databaseAssertions,
        );

        return $scenario->defineTest($this->factory);
    }
}
