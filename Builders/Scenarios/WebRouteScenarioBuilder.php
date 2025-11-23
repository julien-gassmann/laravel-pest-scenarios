<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Definitions\Contexts\WebRouteContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\WebRoutes\InvalidWebRouteScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\WebRoutes\ValidWebRouteScenario;
use Jgss\LaravelPestScenarios\Support\PestTestCallFactory;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;
use Symfony\Component\HttpFoundation\Response;

final readonly class WebRouteScenarioBuilder
{
    public function __construct(
        private TestCallFactoryContract $factory = new PestTestCallFactory,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     * @param  array<int, (Closure(): TestCase|Closure(TestResponse<Response>): TestResponse<Response>)>  $responseAssertions
     */
    public function valid(
        string $description,
        WebRouteContext $context,
        array $payload = [],
        int $expectedStatusCode = 200,
        array $databaseAssertions = [],
        array $responseAssertions = [],
    ): FakeTestCall|TestCall {
        $scenario = new ValidWebRouteScenario(
            description: $description,
            context: $context,
            payload: $payload,
            expectedStatusCode: $expectedStatusCode,
            databaseAssertions: $databaseAssertions,
            responseAssertions: $responseAssertions,
        );

        return $scenario->defineTest($this->factory);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     * @param  array<int, (Closure(): TestCase|Closure(TestResponse<Response>): TestResponse<Response>)>  $responseAssertions
     */
    public function invalid(
        string $description,
        WebRouteContext $context,
        array $payload = [],
        int $expectedStatusCode = 422,
        array $databaseAssertions = [],
        array $responseAssertions = [],
    ): FakeTestCall|TestCall {
        $scenario = new InvalidWebRouteScenario(
            description: $description,
            context: $context,
            payload: $payload,
            expectedStatusCode: $expectedStatusCode,
            databaseAssertions: $databaseAssertions,
            responseAssertions: $responseAssertions,
        );

        return $scenario->defineTest($this->factory);
    }
}
