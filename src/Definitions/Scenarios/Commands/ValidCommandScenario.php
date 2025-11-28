<?php

/**
 * @noinspection DuplicatedCode
 * @noinspection PhpInternalEntityUsedInspection Used for TestCall
 */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Commands;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\PendingCommand;
use Jgss\LaravelPestScenarios\Definitions\Contexts\CommandContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

use function Pest\Laravel\artisan;

/**
 * Each instance defines a successful Command scenario.
 *
 * @property string $description Describes the scenario
 * @property CommandContext $context Provides the contextual information for the command (command, mocks, spies, appLocale)
 * @property null|Closure(): string|string $arguments $arguments Provides the arguments and options for the command to run
 * @property null|Closure(PendingCommand): PendingCommand $commandAssertions Provides the command related assertions to perform
 * @property array<int, Closure(): TestCase> $databaseAssertions Provides the database related assertions to perform
 */
final readonly class ValidCommandScenario extends CommandScenario
{
    /**
     * @param  null|Closure(): string|string  $arguments
     * @param  null|Closure(PendingCommand): PendingCommand  $commandAssertions
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function __construct(
        string $description,
        CommandContext $context,
        null|Closure|string $arguments,
        ?Closure $commandAssertions,
        array $databaseAssertions,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            arguments: $arguments,
            commandAssertions: $commandAssertions,
            databaseAssertions: $databaseAssertions,
        );
    }

    public function defineTest(TestCallFactoryContract $factory): FakeTestCall|TestCall
    {
        $scenario = $this;

        return $factory->make($scenario->description, function () use ($scenario) {
            // Arrange: prepare the test environment
            // - set up the database
            // - initialize mocks
            // - set app locale
            $scenario->prepareContext();

            // Act: Build pending command (does not run it yet)
            /** @var PendingCommand $command */
            $command = artisan($scenario->context->getCommand().' '.$scenario->resolveArguments());

            // Assert: Perform all command related assertions
            $assertions = $scenario->commandAssertions;
            if (is_callable($assertions)) {
                $assertions($command);
            }

            // Act: Run command
            $command->run();

            // Assert: Perform all database related assertions
            foreach ($scenario->databaseAssertions as $assertion) {
                $assertion();
            }
        });
    }
}
