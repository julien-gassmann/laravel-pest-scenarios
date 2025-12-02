<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Jgss\LaravelPestScenarios\Tests\Feature\Models;

use Exception;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

use function Jgss\LaravelPestScenarios\queryId;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

$context = Context::forModel()->with(
    databaseSetup: 'create_dummy_with_children',
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for Dummy model
 * ───────────────────────────────────────
 */
describe('ModelScenario : success', function () use ($context): void {
    describe('DummyModel', function () use ($context): void {
        Scenario::forModel()->valid(
            description: "ensures 'updateRandomChild' method works",
            context: $context,
            input: fn (): bool => queryDummy('dummy_first')->updateRandomChild(),
            expectedOutput: fn (): true => true,
            databaseAssertions: [
                fn () => assertDatabaseHas('dummy_children', [
                    'dummy_id' => queryId('dummy_first'),
                    'label' => 'updated label',
                ]),
            ]
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for Dummy model
 * ───────────────────────────────────────
 */
describe('ModelScenario : failure', function () use ($context): void {
    describe('DummyModel - invalid values', function () use ($context): void {
        Scenario::forModel()->invalid(
            description: "ensures 'updateRandomChild' method fails",
            context: $context,
            input: fn (): bool => queryDummy('dummy_first')->updateRandomChild(true),
            expectedException: new Exception('Child updating failed.'),
            databaseAssertions: [
                fn () => assertDatabaseMissing('dummy_children', ['label' => 'updated label']),
            ]
        );
    });
});
