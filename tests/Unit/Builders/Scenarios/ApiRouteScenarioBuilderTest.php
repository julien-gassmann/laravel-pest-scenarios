<?php

/** @noinspection PhpInternalEntityUsedInspection Used for TestCall */

namespace Jgss\LaravelPestScenarios\Tests\Unit\Builders\Scenarios;

use Jgss\LaravelPestScenarios\Builders\Scenarios\ApiRouteScenarioBuilder;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCallFactory;
use Pest\PendingCalls\TestCall;

$context = Context::forApiRoute()->with('dummy.route');

/**
 * ───────────────────────────────────────
 * Valid scenarios for ApiRouteScenarioBuilder class
 * ───────────────────────────────────────
 */
describe('Builders - ApiRouteScenarioBuilder : success', function () use ($context): void {
    test('valid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build valid test Scenario
        $builder = new ApiRouteScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->valid('description valid', $context);
        $description = getProtectedProperty($scenario, 'description');

        // Assert: Ensure Scenario is instance of TestCall
        expect($scenario)->toBeInstanceOf(TestCall::class)
            ->and($description)->toBe('description valid');
    });

    test('invalid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build invalid test Scenario
        $builder = new ApiRouteScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->invalid('description invalid', $context);
        $description = getProtectedProperty($scenario, 'description');

        // Assert: Ensure Scenario is instance of TestCall
        expect($scenario)->toBeInstanceOf(TestCall::class)
            ->and($description)->toBe('description invalid');
    });
});
