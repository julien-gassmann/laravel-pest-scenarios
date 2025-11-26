<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\WebRoutes;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Definitions\Contexts\WebRouteContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\CanSendWebHttpRequest;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\PrepareContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\ResolvePayload;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;
use Symfony\Component\HttpFoundation\Response;

abstract readonly class WebRouteScenario
{
    use CanSendWebHttpRequest;
    use PrepareContext;
    use ResolvePayload;

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, Closure(): TestCase>  $databaseAssertions
     * @param  array<int, (Closure(): TestCase|Closure(TestResponse<Response>): TestResponse<Response>)>  $responseAssertions
     */
    public function __construct(
        public string $description,
        public WebRouteContext $context,
        public array $payload,
        public bool $shouldFollowRedirect,
        public int $expectedStatusCode,
        public array $databaseAssertions,
        public array $responseAssertions,
    ) {}

    abstract public function defineTest(TestCallFactoryContract $factory): FakeTestCall|TestCall;
}
