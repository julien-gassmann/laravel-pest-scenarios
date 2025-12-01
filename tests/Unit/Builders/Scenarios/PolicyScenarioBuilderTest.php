<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Builders\Scenarios;

use Jgss\LaravelPestScenarios\Builders\Scenarios\PolicyScenarioBuilder;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCallFactory;
use Workbench\App\Policies\DummyPolicy;

$context = Context::forPolicy()->with(DummyPolicy::class);

/**
 * ───────────────────────────────────────
 * Valid scenarios for PolicyScenarioBuilder class
 * ───────────────────────────────────────
 */
describe('Builders - PolicyScenarioBuilder : success', function () use ($context) {
    test('valid method returns a TestCall', function () use ($context) {
        // Arrange: Create builder and build valid test Scenario
        $builder = new PolicyScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->valid('description valid', $context, 'method');

        // Arrange: Get description (protected property) from FakeTestCall
        $description = getProtectedProperty($scenario, 'description');

        // Assert: Ensure Scenario is instance of TestCall
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($description)->toBe('description valid');
    });

    test('invalid method returns a TestCall', function () use ($context) {
        // Arrange: Create builder and build invalid test Scenario
        $builder = new PolicyScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->invalid('description invalid', $context, 'method');

        // Arrange: Get description (protected property) from FakeTestCall
        $description = getProtectedProperty($scenario, 'description');

        // Assert: Ensure Scenario is instance of TestCall
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($description)->toBe('description invalid');
    });
});
