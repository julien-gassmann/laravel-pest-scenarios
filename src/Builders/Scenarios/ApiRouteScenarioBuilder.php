<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\JsonResponse;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ApiRouteContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\ApiRoutes\InvalidApiRouteScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\ApiRoutes\ValidApiRouteScenario;
use Jgss\LaravelPestScenarios\Support\PestTestCallFactory;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

use function Jgss\LaravelPestScenarios\getJsonStructure;

final readonly class ApiRouteScenarioBuilder
{
    public function __construct(
        private TestCallFactoryContract $factory = new PestTestCallFactory,
    ) {}

    /**
     * @param  null|string|Closure(): (array<array-key, mixed>|null)  $structure
     * @return Closure(): (array<array-key, mixed>|null)
     */
    private function resolveInitialStructure(null|string|Closure $structure): Closure
    {
        return match (true) {
            is_string($structure) => getJsonStructure($structure),
            is_callable($structure) => $structure,
            is_null($structure) => getJsonStructure('resource'),
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  null|string|Closure(): (array<array-key, mixed>|null)  $expectedStructure
     * @param  null|Closure(): JsonResponse  $expectedResponse
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function valid(
        string $description,
        ApiRouteContext $context,
        array $payload = [],
        int $expectedStatusCode = 200,
        null|string|Closure $expectedStructure = null,
        ?Closure $expectedResponse = null,
        array $databaseAssertions = [],
    ): FakeTestCall|TestCall {
        $scenario = new ValidApiRouteScenario(
            description: $description,
            context: $context,
            payload: $payload,
            expectedStatusCode: $expectedStatusCode,
            expectedStructure: $this->resolveInitialStructure($expectedStructure),
            expectedResponse: $expectedResponse,
            databaseAssertions: $databaseAssertions,
        );

        return $scenario->defineTest($this->factory);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<array-key, mixed>  $expectedErrorStructure
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function invalid(
        string $description,
        ApiRouteContext $context,
        array $payload = [],
        int $expectedStatusCode = 422,
        array $expectedErrorStructure = [],
        ?string $expectedErrorMessage = null,
        array $databaseAssertions = [],
    ): FakeTestCall|TestCall {
        $scenario = new InvalidApiRouteScenario(
            description: $description,
            context: $context,
            payload: $payload,
            expectedStatusCode: $expectedStatusCode,
            expectedErrorStructure: $expectedErrorStructure,
            expectedErrorMessage: $expectedErrorMessage,
            databaseAssertions: $databaseAssertions,
        );

        return $scenario->defineTest($this->factory);
    }
}
