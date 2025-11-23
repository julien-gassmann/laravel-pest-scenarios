<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Jgss\LaravelPestScenarios\Definitions\Contexts\FormRequestContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\FormRequests\InvalidFormRequestScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\FormRequests\ValidFormRequestScenario;
use Jgss\LaravelPestScenarios\Support\PestTestCallFactory;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

final readonly class FormRequestScenarioBuilder
{
    public function __construct(
        private TestCallFactoryContract $factory = new PestTestCallFactory,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function valid(
        string $description,
        FormRequestContext $context,
        array $payload = [],
        bool $shouldAuthorize = true,
    ): FakeTestCall|TestCall {
        $scenario = new ValidFormRequestScenario(
            description: $description,
            context: $context,
            payload: $payload,
            shouldAuthorize: $shouldAuthorize,
        );

        return $scenario->defineTest($this->factory);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string[]>  $expectedValidationErrors
     */
    public function invalid(
        string $description,
        FormRequestContext $context,
        array $payload = [],
        bool $shouldAuthorize = true,
        array $expectedValidationErrors = [],
    ): FakeTestCall|TestCall {
        $scenario = new InvalidFormRequestScenario(
            description: $description,
            context: $context,
            payload: $payload,
            shouldAuthorize: $shouldAuthorize,
            expectedValidationErrors: $expectedValidationErrors,
        );

        return $scenario->defineTest($this->factory);
    }
}
