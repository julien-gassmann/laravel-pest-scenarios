<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Models;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ModelContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Pest\PendingCalls\TestCall;

/**
 * Each instance defines a successful Model class scenario.
 *
 * @property string $description Describes the scenario
 * @property ModelContext $context Provides the contextual information for the model (actingAs, appLocale)
 * @property Closure(): mixed $input Provides the input to test (e.g. fn() => User::find(1)->isAdmin())
 * @property Closure(): mixed $expectedOutput Provides the expected output that should result from the given input (e.g. fn() => true)
 * @property array<int, Closure(): TestCase> $databaseAssertions Provides the database related assertions to perform
 */
final readonly class ValidModelScenario extends ModelScenario
{
    /**
     * @param  Closure(): mixed  $input
     * @param  Closure(): mixed  $expectedOutput
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function __construct(
        string $description,
        ModelContext $context,
        Closure $input,
        public Closure $expectedOutput,
        array $databaseAssertions,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            input: $input,
            databaseAssertions: $databaseAssertions,
        );
    }

    public function defineTest(TestCallFactoryContract $factory): TestCall
    {
        $scenario = $this;

        return $factory->make($scenario->description, function () use ($scenario): void {
            // Arrange: prepare the test environment
            // - set up the database
            // - initialize mocks
            // - set app locale
            // - authenticate a user
            $scenario->prepareContext();

            // Arrange: Get the provided input and expected output
            $input = ($scenario->input)();
            $expectedOutput = ($scenario->expectedOutput)();

            // Assert: Check if provided input and expected output are equals
            expect($input)->toEqual($expectedOutput);

            // Assert: Perform all database related assertions
            foreach ($scenario->databaseAssertions as $assertion) {
                $assertion();
            }
        });
    }
}
