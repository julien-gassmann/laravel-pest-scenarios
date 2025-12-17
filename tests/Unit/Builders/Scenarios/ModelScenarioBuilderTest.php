<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Tests\Unit\Builders\Scenarios;

use Exception;
use Jgss\LaravelPestScenarios\Builders\Scenarios\ModelScenarioBuilder;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCallFactory;
use Pest\PendingCalls\TestCall;

$context = Context::forModel()->with();

/**
 * ───────────────────────────────────────
 * Valid scenarios for ModelScenarioBuilder class
 * ───────────────────────────────────────
 */
describe('Builders - ModelScenarioBuilder : success', function () use ($context): void {
    test('valid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build valid test Scenario
        $builder = new ModelScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->valid('description valid', $context, fn (): null => null);
        $description = getProtectedProperty($scenario, 'description');

        // Assert: Ensure Scenario is instance of TestCall
        expect($scenario)->toBeInstanceOf(TestCall::class)
            ->and($description)->toBe('description valid');
    });

    test('invalid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build invalid test Scenario
        $builder = new ModelScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->invalid('description invalid', $context, fn (): null => null, new Exception);
        $description = getProtectedProperty($scenario, 'description');

        // Assert: Ensure Scenario is instance of TestCall
        expect($scenario)->toBeInstanceOf(TestCall::class)
            ->and($description)->toBe('description invalid');
    });
});
