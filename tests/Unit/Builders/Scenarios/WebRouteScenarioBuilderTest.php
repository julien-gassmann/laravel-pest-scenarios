<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Builders\Scenarios;

use Jgss\LaravelPestScenarios\Builders\Scenarios\WebRouteScenarioBuilder;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCallFactory;

$context = Context::forWebRoute()->with('dummy.route');

/**
 * ───────────────────────────────────────
 * Valid scenarios for WebRouteScenarioBuilder class
 * ───────────────────────────────────────
 */
describe('Builders - WebRouteScenarioBuilder : success', function () use ($context): void {
    test('valid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build valid test Scenario
        $builder = new WebRouteScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->valid('description valid', $context);

        // Assert: Ensure Scenario is instance of TestCall
        /** @var FakeTestCall $scenario */
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($scenario->description)->toBe('description valid');
    });

    test('invalid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build invalid test Scenario
        $builder = new WebRouteScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->invalid('description invalid', $context);

        // Assert: Ensure Scenario is instance of TestCall
        /** @var FakeTestCall $scenario */
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($scenario->description)->toBe('description invalid');
    });
});
