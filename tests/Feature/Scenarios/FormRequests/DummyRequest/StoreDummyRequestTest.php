<?php

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Http\Requests\DummyRequest;

$context = Context::forFormRequest()->with(
    formRequestClass: DummyRequest::class,
    routeName: 'api.dummies.store',
    actingAs: 'user',
    databaseSetup: 'create_user',
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for DummyRequest
 * when used on route: api.dummies.store
 * ───────────────────────────────────────
 */
describe('FormRequests : success', function () use ($context): void {
    describe('DummyRequest (store) - valid values', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: "passes with valid 'name', 'email', 'age' and 'is_active'",
            context: $context,
            payload: [
                'name' => 'New Dummy',
                'email' => 'new@dummy.com',
                'age' => 20,
                'is_active' => true,
            ],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for DummyRequest
 * when used on route: api.dummies.store
 * ───────────────────────────────────────
 */
describe('FormRequests : failure', function () use ($context): void {
    describe('DummyRequest (store) - unauthorized', function () use ($context): void {
        Scenario::forFormRequest()->invalid(
            description: 'fails when authorized method return false',
            context: $context
                ->withActingAs('unauthorized')
                ->withDatabaseSetup(['create_unauthorized_user', 'create_dummies']),
            shouldAuthorize: false,
        );
    });

    describe('DummyRequest (store) - invalid values', function () use ($context): void {
        describe('name', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'name' is missing (with raw messages)",
                context: $context,
                payload: [
                    'email' => 'new@dummy.com',
                    'age' => 20,
                    'is_active' => true,
                ],
                expectedValidationErrors: ['name' => ['The name field is required.']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'name' is missing (with translation keys)",
                context: $context,
                payload: [
                    'email' => 'new@dummy.com',
                    'age' => 20,
                    'is_active' => true,
                ],
                expectedValidationErrors: ['name' => ['required']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'name' is array",
                context: $context,
                payload: [
                    'name' => ['New Dummy'],
                    'email' => 'new@dummy.com',
                    'age' => 20,
                    'is_active' => true,
                ],
                expectedValidationErrors: ['name' => ['string', 'between.string|min=3|max=50']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'name' is too short",
                context: $context,
                payload: [
                    'name' => 'ND',
                    'email' => 'new@dummy.com',
                    'age' => 20,
                    'is_active' => true,
                ],
                expectedValidationErrors: ['name' => ['between.string|min=3|max=50']],
            );
        });

        describe('email', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'email' is missing",
                context: $context,
                payload: [
                    'name' => 'New Dummy',
                    'age' => 20,
                    'is_active' => true,
                ],
                expectedValidationErrors: ['email' => ['required']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'email' is not valid",
                context: $context,
                payload: [
                    'name' => 'New Dummy',
                    'email' => 'invalid-email',
                    'age' => 20,
                    'is_active' => true,
                ],
                expectedValidationErrors: ['email' => ['email']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'email' already exists",
                context: $context
                    ->withActingAs('user')
                    ->withDatabaseSetup(['create_user', 'create_dummy']),
                payload: [
                    'name' => 'New Dummy',
                    'email' => 'dummy@email.com',
                    'age' => 20,
                    'is_active' => true,
                ],
                expectedValidationErrors: ['email' => ['unique']],
            );
        });

        describe('age', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'age' is string",
                context: $context,
                payload: [
                    'name' => 'New Dummy',
                    'email' => 'new@dummy.com',
                    'age' => 'string',
                    'is_active' => true,
                ],
                expectedValidationErrors: ['age' => ['integer', 'min.numeric|min=18']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'age' is below 18",
                context: $context,
                payload: [
                    'name' => 'New Dummy',
                    'email' => 'new@dummy.com',
                    'age' => 10,
                    'is_active' => true,
                ],
                expectedValidationErrors: ['age' => ['min.numeric|min=18']],
            );
        });

        describe('is_active', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'is_active' is integer",
                context: $context,
                payload: [
                    'name' => 'New Dummy',
                    'email' => 'new@dummy.com',
                    'age' => 20,
                    'is_active' => 42,
                ],
                expectedValidationErrors: ['is_active' => ['boolean']],
            );
        });
    });

    describe('DummyRequest - combined invalid values', function () use ($context): void {
        Scenario::forFormRequest()->invalid(
            description: 'fails when all values are invalid',
            context: $context
                ->withActingAs('user')
                ->withDatabaseSetup(['create_user', 'create_dummy']),
            payload: [
                'email' => 'dummy@email.com',
                'age' => 10,
                'is_active' => 42,
            ],
            expectedValidationErrors: [
                'name' => ['required'],
                'email' => ['unique'],
                'age' => ['min.numeric|min=18'],
                'is_active' => ['boolean'],
            ],
        );
    });
});
