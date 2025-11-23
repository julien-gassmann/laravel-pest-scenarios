<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Rules;

use Jgss\LaravelPestScenarios\Definitions\Contexts\RuleContext;
use Jgss\LaravelPestScenarios\Support\TestCallFactoryContract;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Pest\PendingCalls\TestCall;

/**
 * Each instance defines a successful ValidationRule scenario.
 *
 * @property string $description Describes the scenario
 * @property RuleContext $context Provides the contextual information for the rule (ruleClass, payload, actingAs, appLocale)
 * @property mixed $value Provides the input value expected to pass validation
 * @property array<int, mixed> $parameters Specifies optional constructor parameters passed to the rule
 */
final readonly class ValidRuleScenario extends RuleScenario
{
    public function defineTest(TestCallFactoryContract $factory): FakeTestCall|TestCall
    {
        $scenario = $this;

        return $factory->make($scenario->description, function () use ($scenario): void {
            // Arrange: prepare the test environment
            // - set up the database
            // - initialize mocks
            // - set app locale
            // - authenticate a user
            $scenario->prepareContext();

            // Arrange : Create the rule instance
            $rule = $scenario->context->getRuleInstance($scenario->parameters);
            $passes = true;

            // Act : Validate the value using the rule instance
            $rule->validate(
                'dummy_field',
                $scenario->getResolvedValue(),
                $scenario->getFailClosure($passes)
            );

            // Assert : Check if the validation has passed successfully
            expect($passes)->toBeTrue();
        });
    }
}
