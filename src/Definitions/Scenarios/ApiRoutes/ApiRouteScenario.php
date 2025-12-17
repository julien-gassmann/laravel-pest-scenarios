<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\ApiRoutes;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Jgss\LaravelPestScenarios\Definitions\Contexts\ApiRouteContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\CanSendApiHttpRequest;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\PrepareContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\ResolvePayload;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Pest\PendingCalls\TestCall;

abstract readonly class ApiRouteScenario
{
    use CanSendApiHttpRequest;
    use PrepareContext;
    use ResolvePayload;

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     */
    public function __construct(
        public string $description,
        public ApiRouteContext $context,
        public array $payload,
        public int $expectedStatusCode,
        public array $databaseAssertions,
    ) {}

    abstract public function defineTest(TestCallFactoryContract $factory): TestCall;
}
