<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\Policies;

use Exception;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Models\User;
use Workbench\App\Policies\DummyPolicy;

$context = Context::forPolicy()->with(
    policyClass: DummyPolicy::class,
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for DummyPolicy class
 * ───────────────────────────────────────
 */
describe('Policies : success', function () use ($context): void {
    describe('DummyPolicy - method without parameter', function () use ($context): void {
        Scenario::forPolicy()->valid(
            description: 'ensures method passes',
            context: $context->withActingAs(fn (): User => new User),
            method: 'methodWithoutParameter',
        );
    });

    describe('DummyPolicy - method with parameter', function () use ($context): void {
        Scenario::forPolicy()->valid(
            description: 'ensures method passes',
            context: $context->withActingAs(fn (): User => new User),
            method: 'methodWithParameter',
            parameters: fn (): array => [true]
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for DummyPolicy class
 * ───────────────────────────────────────
 */
describe('Policies : failure', function () use ($context): void {
    describe('DummyPolicy - method without parameter', function () use ($context): void {
        Scenario::forPolicy()->invalid(
            description: 'ensures method fails',
            context: $context,
            method: 'methodWithoutParameter',
        );
    });

    describe('DummyPolicy - method with parameter', function () use ($context): void {
        Scenario::forPolicy()->invalid(
            description: 'ensures method fails',
            context: $context->withActingAs(fn (): User => new User),
            method: 'methodWithParameter',
            parameters: fn (): array => [false]
        );
    });

    describe('DummyPolicy - method throwing exception', function () use ($context): void {
        Scenario::forPolicy()->invalid(
            description: 'ensures method fails',
            context: $context,
            method: 'methodThrowingException',
            expectedException: new Exception('dummy exception message'),
        );
    });
});
