<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ApiRouteContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\ApiRoutes\InvalidApiRouteScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\ApiRoutes\ValidApiRouteScenario;
use Pest\PendingCalls\TestCall;

use function Jgss\LaravelPestScenarios\getJsonStructure;

class ApiRouteScenarioBuilder
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  null|Closure(): (array<array-key, mixed>|null)  $expectedStructure
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function valid(
        string $description,
        ApiRouteContext $context,
        Closure $expectedResponse,
        array $payload = [],
        int $expectedStatusCode = 200,
        ?Closure $expectedStructure = null,
        array $databaseAssertions = [],
    ): TestCall {
        $scenario = new ValidApiRouteScenario(
            description: $description,
            context: $context,
            payload: $payload,
            expectedStatusCode: $expectedStatusCode,
            expectedStructure: $expectedStructure ?? getJsonStructure('resource'),
            expectedResponse: $expectedResponse,
            databaseAssertions: $databaseAssertions,
        );

        return $scenario->defineTest();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, string>  $expectedErrorKeys
     */
    public function invalid(
        string $description,
        ApiRouteContext $context,
        array $payload = [],
        int $expectedStatusCode = 422,
        array $expectedErrorKeys = [],
        ?string $expectedErrorMessage = null,
    ): TestCall {
        $scenario = new InvalidApiRouteScenario(
            description: $description,
            context: $context,
            payload: $payload,
            expectedStatusCode: $expectedStatusCode,
            expectedErrorKeys: $expectedErrorKeys,
            expectedErrorMessage: $expectedErrorMessage,
        );

        return $scenario->defineTest();
    }
}
