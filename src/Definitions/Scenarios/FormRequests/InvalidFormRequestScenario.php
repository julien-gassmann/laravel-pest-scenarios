<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\FormRequests;

use Jgss\LaravelPestScenarios\Definitions\Contexts\FormRequestContext;
use Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits\BuildsValidationMessages;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

/**
 * Each instance defines a failure FormRequest scenario.
 *
 * Each expected validation error is a list of translation keys or raw error messages:
 *   - Translation keys follow the format: 'rule.key|param1=value|param2=value' (e.g. 'between.numeric|min=18|max=25').
 *   - Alternatively, plain error messages can be used directly (e.g. 'This field is required.').
 * Example: ['age' => ['between.numeric|min=18|max=25', 'This field is required.']]
 *
 * @property string $description Describes the scenario
 * @property FormRequestContext $context Provides the contextual information for the request (routeName, routeParameters, formRequestClass, actingAs, appLocale)
 * @property array<string, mixed> $payload Provides the input data expected to fail validation
 * @property bool $shouldAuthorize Specifies the expected result of FormRequest's authorize() method
 * @property array<string, string[]> $expectedValidationErrors Specifies the validation rules expected to trigger errors, keyed by input field
 */
final readonly class InvalidFormRequestScenario extends FormRequestScenario
{
    use BuildsValidationMessages;

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string[]>  $expectedValidationErrors
     */
    public function __construct(
        string $description,
        FormRequestContext $context,
        array $payload,
        bool $shouldAuthorize,
        public array $expectedValidationErrors,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            payload: $payload,
            shouldAuthorize: $shouldAuthorize,
        );
    }

    public function defineTest(TestCallFactoryContract $factory): FakeTestCall|TestCall
    {
        $scenario = $this;

        return $factory->make($scenario->description, function () use ($scenario) {
            // Arrange: prepare the test environment
            // - set up the database
            // - initialize mocks
            // - set app locale
            // - authenticate a user
            $scenario->prepareContext();

            // Assert: The FormRequest correctly authorizes the current user
            $actualAuthorization = $scenario->doesFormRequestAuthorize();
            expect($scenario->shouldAuthorize)->toBe($actualAuthorization);

            if ($scenario->payload) {
                // Arrange : Create validator instance for the current scenario based on :
                // - The FormRequest class.
                // - The route-bound context (if needed).
                // - The scenario's payload.
                $validator = $scenario->makeValidator();
                $errors = $validator->errors()->getMessages();

                // Arrange : Generate expected error messages based on the current scenario's translation keys.
                $expectedMessages = $scenario->buildExpectedMessages();

                // Arrange : Sort messages in both arrays to allow comparison.
                array_walk($errors, fn (&$messages) => sort($messages));
                array_walk($expectedMessages, fn (&$messages) => sort($messages));

                // Assert : Check if :
                // - Validation does fail.
                // - Errors are triggered for the expected field.
                // - The validation error messages match the rules configured in the scenario.
                expect($validator->fails())->toBeTrue()
                    ->and($errors)->toHaveKeys(array_keys($expectedMessages))
                    ->and($errors)->toEqual($expectedMessages);
            }
        });
    }
}
