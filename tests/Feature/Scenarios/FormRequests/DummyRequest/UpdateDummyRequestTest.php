<?php

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Http\Requests\DummyRequest;

use function Jgss\LaravelPestScenarios\getQueryId;

$context = Context::forFormRequest()->with(
    formRequestClass: DummyRequest::class,
    routeName: 'api.dummies.update',
    routeParameters: ['dummy' => getQueryId('dummy_first')],
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummy'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for DummyRequest
 * when used on route: api.dummies.update
 * ───────────────────────────────────────
 */
describe('FormRequests : success', function () use ($context): void {
    describe('DummyRequest (update) - update one field', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: "passes with valid 'name'",
            context: $context,
            payload: ['name' => 'Updated Dummy'],
        );

        Scenario::forFormRequest()->valid(
            description: 'ignores correctly route parameter',
            context: $context,
            payload: [
                'name' => 'Updated Dummy',
                'email' => 'dummy@email.coml',
            ],
        );
    });

    describe('DummyRequest (update) - update relation', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: "passes with valid 'children_ids'",
            context: $context->withDatabaseSetup([
                'create_user',
                'create_dummy',
                'create_dummy_child',
            ]),
            payload: ['children_ids' => [getQueryId('dummy_child_first')]],
        );
    });

    describe('DummyRequest (update) - update multiple fields', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: "passes with valid 'name' and 'age'",
            context: $context,
            payload: [
                'name' => 'Updated Dummy',
                'age' => 20,
            ],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for DummyRequest
 * when used on route: api.dummies.update
 * ───────────────────────────────────────
 */
describe('FormRequests : failure', function () use ($context): void {
    describe('DummyRequest (update) - unauthorized', function () use ($context): void {
        Scenario::forFormRequest()->invalid(
            description: 'fails when authorized method return false',
            context: $context
                ->withActingAs('unauthorized')
                ->withDatabaseSetup(['create_unauthorized_user', 'create_dummies']),
            shouldAuthorize: false,
        );
    });

    describe('DummyRequest (update) - invalid values', function () use ($context): void {
        describe('name', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'name' is array (with raw messages)",
                context: $context,
                payload: ['name' => ['Updated Dummy']],
                expectedValidationErrors: ['name' => [
                    'The name field must be a string.',
                    'The name field must be between 3 and 50 characters.',
                ]],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'name' is array (with translation keys)",
                context: $context,
                payload: ['name' => ['Updated Dummy']],
                expectedValidationErrors: ['name' => ['string', 'between.string|min=3|max=50']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'name' is too short",
                context: $context,
                payload: ['name' => 'UD'],
                expectedValidationErrors: ['name' => ['between.string|min=3|max=50']],
            );
        });

        describe('email', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'email' is not valid",
                context: $context,
                payload: ['email' => 'invalid-email'],
                expectedValidationErrors: ['email' => ['email']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'email' already exists",
                context: $context
                    ->withActingAs('user')
                    ->withRouteParameters([])
                    ->withDatabaseSetup(['create_user', 'create_dummy']),
                payload: ['email' => 'dummy@email.com'],
                expectedValidationErrors: ['email' => ['unique']],
            );
        });

        describe('age', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'age' is string",
                context: $context,
                payload: ['age' => 'string'],
                expectedValidationErrors: ['age' => ['integer', 'min.numeric|min=18']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'age' is below 18",
                context: $context,
                payload: ['age' => 10],
                expectedValidationErrors: ['age' => ['min.numeric|min=18']],
            );
        });

        describe('is_active', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'is_active' is integer",
                context: $context,
                payload: ['is_active' => 42],
                expectedValidationErrors: ['is_active' => ['boolean']],
            );
        });

        describe('children', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'children_ids' is string",
                context: $context,
                payload: ['children_ids' => 'string'],
                expectedValidationErrors: ['children_ids' => ['array']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'children_ids' contains non-existing IDs",
                context: $context,
                payload: ['children_ids' => [88888, 99999]],
                expectedValidationErrors: [
                    'children_ids.0' => ['exists'],
                    'children_ids.1' => ['exists'],
                ],
            );
        });
    });

    describe('DummyRequest (update) - combined invalid values', function () use ($context): void {
        Scenario::forFormRequest()->invalid(
            description: 'fails when all values are invalid',
            context: $context
                ->withActingAs('user')
                ->withRouteParameters([])
                ->withDatabaseSetup(['create_user', 'create_dummy']),
            payload: [
                'email' => 'dummy@email.com',
                'age' => 10,
                'is_active' => 42,
            ],
            expectedValidationErrors: [
                'email' => ['unique'],
                'age' => ['min.numeric|min=18'],
                'is_active' => ['boolean'],
            ],
        );
    });
});
