<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Policies;

use Closure;
use Jgss\LaravelPestScenarios\Definitions\Contexts\PolicyContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;
use Throwable;

/**
 * Each instance defines a successful Policy class scenario.
 *
 * @property string $description Describes the scenario
 * @property PolicyContext $context Provides the contextual information for the policy (policyClass, actingAs, appLocale)
 * @property string $method Specifies the method name of the policy to call for the scenario
 * @property Closure(): array<int, mixed> $parameters Provides the parameters being passed to the method under test
 * @property Closure(): mixed $expectedOutput Provides the expected output that should result from the policy method (e.g. fn() => false)
 * @property null|Throwable $expectedException Specifies the exception that should be triggered when running the given method
 */
final readonly class InvalidPolicyScenario extends PolicyScenario
{
    /**
     * @param  Closure(): array<int, mixed>  $parameters
     * @param  Closure(): mixed  $expectedOutput
     */
    public function __construct(
        string $description,
        PolicyContext $context,
        string $method,
        Closure $parameters,
        Closure $expectedOutput,
        public ?Throwable $expectedException,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            method: $method,
            parameters: $parameters,
            expectedOutput: $expectedOutput,
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
            // - authenticate a user
            $scenario->prepareContext();

            if ($scenario->expectedException) {
                // Assert: Check if policy method throws the expected exception
                expect(fn () => $scenario->runPolicyMethod())
                    ->toThrow($scenario->expectedException);
            } else {
                // Act: Run the policy method
                $result = $scenario->runPolicyMethod();

                // Arrange: Get the expected output
                $expectedOutput = ($scenario->expectedOutput)();

                // Assert: Check if policy result and expected output are equals
                expect($result)->toEqual($expectedOutput);
            }
        });
    }
}
