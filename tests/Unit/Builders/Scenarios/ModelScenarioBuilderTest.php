<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Builders\Scenarios;

use Exception;
use Jgss\LaravelPestScenarios\Builders\Scenarios\ModelScenarioBuilder;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCallFactory;

$context = Context::forModel()->with();

/**
 * ───────────────────────────────────────
 * Valid scenarios for ModelScenarioBuilder class
 * ───────────────────────────────────────
 */
describe('Builders - ModelScenarioBuilder : success', function () use ($context) {
    test('valid method returns a TestCall', function () use ($context) {
        // Arrange: Create builder and build valid test Scenario
        $builder = new ModelScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->valid('description valid', $context, fn () => null);

        // Arrange: Get description (protected property) from FakeTestCall
        $description = getProtectedProperty($scenario, 'description');

        // Assert: Ensure Scenario is instance of TestCall
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($description)->toBe('description valid');
    });

    test('invalid method returns a TestCall', function () use ($context) {
        // Arrange: Create builder and build invalid test Scenario
        $builder = new ModelScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->invalid('description invalid', $context, fn () => null, new Exception);

        // Arrange: Get description (protected property) from FakeTestCall
        $description = getProtectedProperty($scenario, 'description');

        // Assert: Ensure Scenario is instance of TestCall
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($description)->toBe('description invalid');
    });
});
