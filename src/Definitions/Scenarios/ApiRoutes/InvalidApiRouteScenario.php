<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\ApiRoutes;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ApiRouteContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Pest\PendingCalls\TestCall;

/**
 * Each instance defines a failure API route scenario.
 *
 * @property string $description Describes the scenario
 * @property ApiRouteContext $context Provides the contextual information for the request (routeName, routeParameters, actingAs, appLocale)
 * @property array<string, mixed> $payload Provides the invalid input data (body or query string)
 * @property int $expectedStatusCode Specifies the expected HTTP status code for the response
 * @property array<array-key, mixed> $expectedErrorStructure Specifies the JSON structure of the expected validation error payload
 * @property string|null $expectedErrorMessage Specifies the expected error message returned for exception-based failures
 * @property array<int, Closure(): TestCase> $databaseAssertions Provides the database related assertions to perform
 */
final readonly class InvalidApiRouteScenario extends ApiRouteScenario
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<array-key, mixed>  $expectedErrorStructure
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function __construct(
        string $description,
        ApiRouteContext $context,
        array $payload,
        int $expectedStatusCode,
        public array $expectedErrorStructure,
        public ?string $expectedErrorMessage,
        array $databaseAssertions,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            payload: $payload,
            expectedStatusCode: $expectedStatusCode,
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

            // Act: Send a request acting as the resolved user with the given payload
            $response = $scenario->sendRequest();

            // Assert: Check if the response status is the expected one
            $response->assertStatus($scenario->expectedStatusCode);

            // Assert: If scenario fails because of validation rules,
            // check if response includes the expected error keys
            if ($scenario->expectedErrorStructure !== []) {
                $response->assertJsonStructure($scenario->expectedErrorStructure);
            }

            // Assert: If scenario fails because of an exception thrown,
            // check if response message is the one expected
            if ($scenario->expectedErrorMessage) {
                $response->assertJson(['message' => $scenario->expectedErrorMessage]);
            }

            // Assert: Perform all database related assertions
            foreach ($scenario->databaseAssertions as $assertion) {
                $assertion();
            }
        });
    }
}
