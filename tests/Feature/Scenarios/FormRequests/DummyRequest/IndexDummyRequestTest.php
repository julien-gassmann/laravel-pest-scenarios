<?php

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Http\Requests\DummyQueryRequest;

$context = Context::forFormRequest()->with(
    formRequestClass: DummyQueryRequest::class,
    routeName: 'api.dummies.index',
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummies'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for DummyQueryRequest
 * when used on route: api.dummies.index
 * ───────────────────────────────────────
 */
describe('FormRequests : success', function () use ($context): void {
    describe('DummyQueryRequest - valid values', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: "passes with valid 'page'",
            context: $context,
            payload: ['page' => 1],
        );

        Scenario::forFormRequest()->valid(
            description: "passes with valid 'perPage'",
            context: $context,
            payload: ['perPage' => 10],
        );

        Scenario::forFormRequest()->valid(
            description: "passes with valid 'sort'",
            context: $context,
            payload: ['sort' => 'name'],
        );
    });

    describe('DummyQueryRequest - combined valid values', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: "passes with valid 'page' and 'perPage'",
            context: $context,
            payload: ['page' => 2, 'perPage' => 3],
        );

        Scenario::forFormRequest()->valid(
            description: "passes with valid 'page', 'perPage' and 'sort'",
            context: $context,
            payload: ['page' => 2, 'perPage' => 3, 'sort' => 'email'],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for DummyQueryRequest
 * when used on route: api.dummies.index
 * ───────────────────────────────────────
 */
describe('FormRequests : failure', function () use ($context): void {
    describe('DummyQueryRequest - unauthorized', function () use ($context): void {
        Scenario::forFormRequest()->invalid(
            description: 'fails when authorized method return false',
            context: $context
                ->withActingAs('unauthorized')
                ->withDatabaseSetup(['create_unauthorized_user', 'create_dummies']),
            shouldAuthorize: false,
        );
    });

    describe('DummyQueryRequest - invalid values', function () use ($context): void {
        describe('page', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'page' is string (with raw messages)",
                context: $context,
                payload: ['page' => 'invalid'],
                expectedValidationErrors: ['page' => [
                    'The page field must be an integer.',
                    'The page field must be greater than or equal to 1.',
                ]],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'page' is string (with translation keys)",
                context: $context,
                payload: ['page' => 'invalid'],
                expectedValidationErrors: ['page' => ['integer', 'gte.numeric|value=1']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'page' is negative",
                context: $context,
                payload: ['page' => -2],
                expectedValidationErrors: ['page' => ['gte.numeric|value=1']],
            );
        });

        describe('per page', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'perPage' is string",
                context: $context,
                payload: ['perPage' => 'invalid'],
                expectedValidationErrors: ['perPage' => ['integer']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'perPage' is over 10",
                context: $context,
                payload: ['perPage' => 15],
                expectedValidationErrors: ['perPage' => ['between.numeric|min=1|max=10']],
            );
        });

        describe('sort', function () use ($context): void {
            Scenario::forFormRequest()->invalid(
                description: "fails when 'sort' is array",
                context: $context,
                payload: ['sort' => ['invalid']],
                expectedValidationErrors: ['sort' => ['string', 'in']],
            );

            Scenario::forFormRequest()->invalid(
                description: "fails when 'sort' is not 'name' or 'email'",
                context: $context,
                payload: ['sort' => 'invalid'],
                expectedValidationErrors: ['sort' => ['in']],
            );
        });
    });

    describe('DummyQueryRequest - combined invalid values', function () use ($context): void {
        Scenario::forFormRequest()->invalid(
            description: 'fails when all values are invalid',
            context: $context,
            payload: ['page' => 'invalid', 'perPage' => 'invalid', 'sort' => 'invalid'],
            expectedValidationErrors: [
                'page' => ['integer', 'gte.numeric|value=1'],
                'perPage' => ['integer'],
                'sort' => ['in'],
            ],
        );
    });
});
