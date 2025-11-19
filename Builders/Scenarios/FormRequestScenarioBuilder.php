<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Builders\Scenarios;

use Jgss\LaravelPestScenarios\Definitions\Contexts\FormRequestContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\FormRequests\InvalidFormRequestScenario;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\FormRequests\ValidFormRequestScenario;
use Pest\PendingCalls\TestCall;

class FormRequestScenarioBuilder
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public static function valid(
        string $description,
        FormRequestContext $context,
        array $payload = [],
        bool $shouldAuthorize = true,
    ): TestCall {
        $scenario = new ValidFormRequestScenario(
            description: $description,
            context: $context,
            payload: $payload,
            shouldAuthorize: $shouldAuthorize,
        );

        return $scenario->defineTest();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string[]>  $expectedValidationErrors
     */
    public static function invalid(
        string $description,
        FormRequestContext $context,
        array $payload = [],
        bool $shouldAuthorize = true,
        array $expectedValidationErrors = [],
    ): TestCall {
        $scenario = new InvalidFormRequestScenario(
            description: $description,
            context: $context,
            payload: $payload,
            shouldAuthorize: $shouldAuthorize,
            expectedValidationErrors: $expectedValidationErrors,
        );

        return $scenario->defineTest();
    }
}
