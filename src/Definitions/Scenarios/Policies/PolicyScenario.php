<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Policies;

use Closure;
use Illuminate\Auth\Access\Response;
use Jgss\LaravelPestScenarios\Definitions\Contexts\PolicyContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\PrepareContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

abstract readonly class PolicyScenario
{
    use PrepareContext;

    /**
     * @param  Closure(): array<int, mixed>  $parameters
     * @param  Closure(): mixed  $expectedOutput
     */
    public function __construct(
        public string $description,
        public PolicyContext $context,
        public string $method,
        public Closure $parameters,
        public Closure $expectedOutput,
    ) {}

    abstract public function defineTest(TestCallFactoryContract $factory): FakeTestCall|TestCall;

    public function runPolicyMethod(): Response|bool|null
    {
        return $this->context->getPolicyResponse($this->method, $this->parameters);
    }
}
