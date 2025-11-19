<?php

/**
 * @noinspection DuplicatedCode
 * @noinspection PhpInternalEntityUsedInspection Used for TestCall
 */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\WebRoutes;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Definitions\Contexts\WebRouteContext;
use Pest\PendingCalls\TestCall;
use Symfony\Component\HttpFoundation\Response;

/**
 * Each instance defines a successful web route scenario.
 *
 * @property string $description Describes the scenario
 * @property WebRouteContext $context Provides the contextual information for the request (routeName, routeParameters, actingAs, appLocale)
 * @property array<string, mixed> $payload Provides the valid input data (body or query string)
 * @property int $expectedStatusCode Specifies the expected HTTP status code for the response
 * @property array<int, Closure(): TestCase> $databaseAssertions Provides the database related assertions to perform
 * @property array<int, (Closure(): TestCase|Closure(TestResponse<Response>): TestResponse<Response>)> $responseAssertions Provides the view content related assertions to perform
 */
final readonly class ValidWebRouteScenario extends WebRouteScenario
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     * @param  array<int, (Closure(): TestCase|Closure(TestResponse<Response>): TestResponse<Response>)>  $responseAssertions
     */
    public function __construct(
        string $description,
        WebRouteContext $context,
        array $payload,
        int $expectedStatusCode,
        array $databaseAssertions,
        array $responseAssertions,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            payload: $payload,
            expectedStatusCode: $expectedStatusCode,
            databaseAssertions: $databaseAssertions,
            responseAssertions: $responseAssertions,
        );
    }

    public function defineTest(): TestCall
    {
        $scenario = $this;

        return it($scenario->description, function () use ($scenario) {
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

            // Assert: Perform all database related assertions
            foreach ($scenario->databaseAssertions as $assertion) {
                $assertion();
            }

            // Assert: Perform all view content related assertions
            foreach ($scenario->responseAssertions as $assertion) {
                $assertion($response);
            }
        });
    }
}
