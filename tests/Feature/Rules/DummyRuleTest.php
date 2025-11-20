<?php

namespace Tests\Unit\Rules;

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Jgss\LaravelPestScenarios\workbench\app\Rules\DummyRule;

$context = Context::forRule()->with(
    ruleClass: DummyRule::class,
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for forRule DummyRule
 * ───────────────────────────────────────
 */
describe('Rule DummyRule : success', function () use ($context) {
    describe('Valid values', function () use ($context) {
        Scenario::forRule()->valid(
            description: 'ensures valid Rule scenario passes',
            context: $context,
            value: 'valid',
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for rule DummyRule
 * ───────────────────────────────────────
 */
describe('Rule DummyRule : failure', function () use ($context) {
    describe('Invalid values', function () use ($context) {
        Scenario::forRule()->invalid(
            description: 'ensures invalid Rule scenario passes',
            context: $context,
            errorMessage: 'dummy error message',
            value: 'invalid',
        );
    });
});
