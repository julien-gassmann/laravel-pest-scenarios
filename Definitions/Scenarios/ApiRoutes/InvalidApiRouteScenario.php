<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\ApiRoutes;

use Jgss\LaravelPestScenarios\Definitions\Contexts\ApiRouteContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

/**
 * Each instance defines a failure API route scenario.
 *
 * @property string $description Describes the scenario
 * @property ApiRouteContext $context Provides the contextual information for the request (routeName, routeParameters, actingAs, appLocale)
 * @property array<string, mixed> $payload Provides the invalid input data (body or query string)
 * @property int $expectedStatusCode Specifies the expected HTTP status code for the response
 * @property array<int, string> $expectedErrorKeys Provides input fields expected to trigger validation errors
 * @property string|null $expectedErrorMessage Specifies the expected error message returned for exception-based failures
 */
final readonly class InvalidApiRouteScenario extends ApiRouteScenario
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, string>  $expectedErrorKeys
     */
    public function __construct(
        string $description,
        ApiRouteContext $context,
        array $payload,
        int $expectedStatusCode,
        public array $expectedErrorKeys,
        public ?string $expectedErrorMessage,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            payload: $payload,
            expectedStatusCode: $expectedStatusCode,
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

            // Act: Send a request acting as the resolved user with the given payload
            $response = $scenario->sendRequest();

            // Assert: Check if the response status is the expected one
            $response->assertStatus($scenario->expectedStatusCode);

            // Assert: If scenario fails because of validation rules,
            // check if response includes the expected error keys
            if (! empty($scenario->expectedErrorKeys)) {
                $response->assertJsonStructure($scenario->expectedErrorKeys);
            }

            // Assert: If scenario fails because of an exception thrown,
            // check if response message is the one expected
            if ($scenario->expectedErrorMessage) {
                $response->assertJson(['message' => $scenario->expectedErrorMessage]);
            }
        });
    }
}
