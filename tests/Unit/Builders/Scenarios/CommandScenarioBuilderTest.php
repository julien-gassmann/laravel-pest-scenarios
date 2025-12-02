<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Builders\Scenarios;

use Jgss\LaravelPestScenarios\Builders\Scenarios\CommandScenarioBuilder;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCall;
use Jgss\LaravelPestScenarios\Tests\Fakes\FakeTestCallFactory;

$context = Context::forCommand()->with('dummy:command');

/**
 * ───────────────────────────────────────
 * Valid scenarios for CommandScenarioBuilder class
 * ───────────────────────────────────────
 */
describe('Builders - CommandScenarioBuilder : success', function () use ($context): void {
    test('valid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build valid test Scenario
        $builder = new CommandScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->valid('description valid', $context);

        // Assert: Ensure Scenario is instance of TestCall
        /** @var FakeTestCall $scenario */
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($scenario->description)->toBe('description valid');
    });

    test('invalid method returns a TestCall', function () use ($context): void {
        // Arrange: Create builder and build invalid test Scenario
        $builder = new CommandScenarioBuilder(new FakeTestCallFactory);
        $scenario = $builder->invalid('description invalid', $context);

        // Assert: Ensure Scenario is instance of TestCall
        /** @var FakeTestCall $scenario */
        expect($scenario)->toBeInstanceOf(FakeTestCall::class)
            ->and($scenario->description)->toBe('description invalid');
    });
});
