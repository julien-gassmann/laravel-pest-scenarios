<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\Rules;

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Rules\DummyRule;

$context = Context::forRule()->with(
    ruleClass: DummyRule::class,
    payload: ['dummy' => 'payload'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for DummyRule class
 * ───────────────────────────────────────
 */
describe('RuleScenario : success', function () use ($context) {
    describe('DummyRule - valid values', function () use ($context) {
        Scenario::forRule()->valid(
            description: 'ensures passes',
            context: $context,
            value: 'valid',
        );

        Scenario::forRule()->valid(
            description: 'ensures passes with parameter',
            context: $context,
            value: 'valid',
            parameters: ['valid'],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for DummyRule class
 * ───────────────────────────────────────
 */
describe('RuleScenario : failure', function () use ($context) {
    describe('DummyRule - invalid values', function () use ($context) {
        Scenario::forRule()->invalid(
            description: 'ensures fails',
            context: $context,
            errorMessage: 'Dummy validation error message',
            value: 'invalid',
        );

        Scenario::forRule()->invalid(
            description: 'ensures fails with parameter',
            context: $context,
            errorMessage: 'Dummy validation error message',
            value: 'valid',
            parameters: ['invalid'],
        );
    });
});
