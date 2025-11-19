<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Rules;

use Jgss\LaravelPestScenarios\Definitions\Contexts\RuleContext;
use Pest\PendingCalls\TestCall;

/**
 * Each instance defines a failure ValidationRule scenario.
 *
 * @property string $description Describes the scenario
 * @property RuleContext $context Provides the contextual information for the rule (ruleClass, payload, actingAs, appLocale)
 * @property string $errorMessage Specifies either a raw error message or a translation key path for the expected validation message
 * @property mixed $value Provides the input value expected to pass validation
 * @property array<int, mixed> $parameters Specifies optional constructor parameters passed to the rule
 */
final readonly class InvalidRuleScenario extends RuleScenario
{
    /**
     * @param  array<int, mixed>  $parameters
     */
    public function __construct(
        string $description,
        RuleContext $context,
        public string $errorMessage,
        mixed $value,
        array $parameters,
    ) {
        parent::__construct(
            description: $description,
            context: $context,
            value: $value,
            parameters: $parameters,
        );
    }

    public function defineTest(): TestCall
    {
        $scenario = $this;

        return it($scenario->description, function () use ($scenario): void {
            // Arrange: prepare the test environment
            // - set up the database
            // - initialize mocks
            // - set app locale
            // - authenticate a user
            $scenario->prepareContext();

            // Arrange : Create the rule instance
            $rule = $scenario->context->getRuleInstance($scenario->parameters);
            $passes = true;
            $message = null;

            // Act : Validate the value using the rule instance
            $rule->validate(
                'dummy_field',
                $scenario->getResolvedValue(),
                $scenario->getFailClosure($passes, $message)
            );

            // Assert : Check if the validation failed and the error message is the one expected
            expect($passes)->toBeFalse()
                ->and($message)->toBe(__($scenario->errorMessage));
        });
    }
}
