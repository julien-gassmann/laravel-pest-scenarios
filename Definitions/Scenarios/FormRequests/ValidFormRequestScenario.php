<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\FormRequests;

use Jgss\LaravelPestScenarios\Definitions\Contexts\FormRequestContext;
use Pest\PendingCalls\TestCall;

/**
 * Each instance defines a successful FormRequest scenario.
 *
 * @property string $description Describes the scenario
 * @property FormRequestContext $context Provides the contextual information for the request (routeName, routeParameters, formRequestClass, actingAs, appLocale)
 * @property array<string, mixed> $payload Provides the input data expected to pass validation
 * @property bool $shouldAuthorize Specifies the expected result of FormRequest's authorize() method
 */
final readonly class ValidFormRequestScenario extends FormRequestScenario
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        string $description,
        FormRequestContext $context,
        array $payload,
        bool $shouldAuthorize,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            payload: $payload,
            shouldAuthorize: $shouldAuthorize,
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

            // Assert: the FormRequest correctly authorizes the current user
            $actualAuthorization = $scenario->doesFormRequestAuthorize();
            expect($scenario->shouldAuthorize)->toBe($actualAuthorization);

            if ($scenario->payload) {
                // Arrange : Create validator instance for the current scenario based on :
                // - The FormRequest class.
                // - The route-bound context (if needed).
                // - The scenario's payload.
                $validator = $scenario->makeValidator();
                $errors = $validator->errors()->messages();
                $payload = $scenario->getResolvedPayload();

                // Assert : Check if :
                // - Validation does pass.
                // - No errors are returned.
                // - The validated payload matches the input data.
                expect($validator->fails())->toBeFalse()
                    ->and($errors)->toBeEmpty()
                    ->and($validator->validated())->toEqual($payload);
            }
        });
    }
}
