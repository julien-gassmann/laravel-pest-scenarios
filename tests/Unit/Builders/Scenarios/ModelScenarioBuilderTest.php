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
describe('Builders - ModelScenarioBuilder : success', function () use ($context): void {
    test('valid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build valid test Scenario
        $builder = new ModelScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->valid('description valid', $context, fn (): null => null);

        // Assert: Ensure Scenario is instance of TestCall
        /** @var FakeTestCall $scenario */
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($scenario->description)->toBe('description valid');
    });

    test('invalid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build invalid test Scenario
        $builder = new ModelScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->invalid('description invalid', $context, fn (): null => null, new Exception);

        // Assert: Ensure Scenario is instance of TestCall
        /** @var FakeTestCall $scenario */
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($scenario->description)->toBe('description invalid');
    });
});
