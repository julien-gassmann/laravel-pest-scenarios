<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\ApiRoutes;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\JsonResponse;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ApiRouteContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

/**
 * Each instance defines a successful API route scenario.
 *
 * @property string $description Describes the scenario
 * @property ApiRouteContext $context Provides the contextual information for the request (routeName, routeParameters, actingAs, appLocale)
 * @property array<string, mixed> $payload Provides the valid input data (body or query string)
 * @property int $expectedStatusCode Specifies the expected HTTP status code for the response
 * @property Closure(): (array<array-key, mixed>|null) $expectedStructure Specifies the expected JSON structure type (e.g. RESOURCE, COLLECTION)
 * @property null|Closure(): JsonResponse $expectedResponse Returns the expected JSON response
 * @property array<int, Closure(): TestCase> $databaseAssertions Provides the database related assertions to perform
 */
final readonly class ValidApiRouteScenario extends ApiRouteScenario
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  Closure(): (array<array-key, mixed>|null)  $expectedStructure
     * @param  null|Closure(): JsonResponse  $expectedResponse
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function __construct(
        string $description,
        ApiRouteContext $context,
        array $payload,
        int $expectedStatusCode,
        public Closure $expectedStructure,
        public ?Closure $expectedResponse,
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

            // Act: Send a request acting as the resolved user with the given payload
            $response = $scenario->sendRequest();

            // Assert: Check if the response status is the expected one (200 or 201)
            $response->assertStatus($scenario->expectedStatusCode);

            // Assert: Check if the response format is correct
            $response->assertJsonStructure(($scenario->expectedStructure)());

            if ($scenario->expectedResponse) {
                // Arrange: Get the expected response content and format it as array
                $responseContent = ($scenario->expectedResponse)();
                $expectedResponse = (array) $responseContent->getData(true);

                // Assert: Check if response contains exactly the expected resource or collection
                expect($response->json())->toEqual($expectedResponse);
            }

            // Assert: Perform all database related assertions
            foreach ($scenario->databaseAssertions as $assertion) {
                $assertion();
            }
        });
    }
}
