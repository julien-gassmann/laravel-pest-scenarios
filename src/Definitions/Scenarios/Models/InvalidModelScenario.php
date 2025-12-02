<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Models;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ModelContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;
use Throwable;

/**
 * Each instance defines a failure Model class scenario.
 *
 * @property string $description Describes the scenario
 * @property ModelContext $context Provides the contextual information for the model (actingAs, appLocale)
 * @property Closure(): mixed $input Provides the input to test (e.g. fn() => User::find(1)->isAdmin())
 * @property Throwable $expectedException Specifies the exception that should be triggered by the given input
 * @property array<int, Closure(): TestCase> $databaseAssertions Provides the database related assertions to perform
 */
final readonly class InvalidModelScenario extends ModelScenario
{
    /**
     * @param  Closure(): mixed  $input
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function __construct(
        string $description,
        ModelContext $context,
        Closure $input,
        public Throwable $expectedException,
        array $databaseAssertions,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            input: $input,
            databaseAssertions: $databaseAssertions,
        );
    }

    public function defineTest(TestCallFactoryContract $factory): FakeTestCall|TestCall
    {
        $scenario = $this;

        return $factory->make($scenario->description, function () use ($scenario): void {
            // Arrange: prepare the test environment
            // - set up the database
            // - initialize mocks
            // - set app locale
            // - authenticate a user
            $scenario->prepareContext();

            // Assert: Check if provided input and expected exception are equals
            expect($scenario->input)->toThrow($scenario->expectedException);

            // Assert: Perform all database related assertions
            foreach ($scenario->databaseAssertions as $assertion) {
                $assertion();
            }
        });
    }
}
