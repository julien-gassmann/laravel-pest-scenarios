<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Definitions\Contexts\CommandContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Commands\InvalidCommandScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Commands\ValidCommandScenario;
use Pest\PendingCalls\TestCall;

final readonly class CommandScenarioBuilder
{
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
    ): TestCall {
        $scenario = new ValidCommandScenario(
            description: $description,
            context: $context,
            arguments: $arguments,
            commandAssertions: $commandAssertions,
            databaseAssertions: $databaseAssertions,
        );

        return $scenario->defineTest();
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
    ): TestCall {
        $scenario = new InvalidCommandScenario(
            description: $description,
            context: $context,
            arguments: $arguments,
            commandAssertions: $commandAssertions,
            databaseAssertions: $databaseAssertions,
        );

        return $scenario->defineTest();
    }
}
