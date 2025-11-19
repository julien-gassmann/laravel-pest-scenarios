<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\ApiRoutes;

use Jgss\LaravelPestScenarios\Definitions\Contexts\ApiRouteContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\CanSendHttpRequest;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\PrepareContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\ResolvePayload;
use Pest\PendingCalls\TestCall;

abstract readonly class ApiRouteScenario
{
    use CanSendHttpRequest;
    use PrepareContext;
    use ResolvePayload;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $description,
        public ApiRouteContext $context,
        public array $payload,
        public int $expectedStatusCode,
    ) {}

    abstract public function defineTest(): TestCall;
}
