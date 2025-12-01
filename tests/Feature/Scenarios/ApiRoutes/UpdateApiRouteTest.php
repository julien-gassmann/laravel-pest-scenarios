<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\ApiRoutes;

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Http\Resources\DummyResource;

use function Jgss\LaravelPestScenarios\getQueryId;
use function Jgss\LaravelPestScenarios\queryId;
use function Jgss\LaravelPestScenarios\queryModel;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

$context = Context::forApiRoute()->with(
    routeName: 'api.dummies.update',
    routeParameters: ['dummy' => getQueryId('dummy_first')],
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummy'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route api.dummies.update
 * ───────────────────────────────────────
 */
describe('ApiRoute : success', function () use ($context) {
    describe('PUT /api/dummies/{dummy} - update one field', function () use ($context) {
        Scenario::forApiRoute()->valid(
            description: "returns 200 with updated resource when updating 'name'",
            context: $context->withRouteName('api.put.dummies.update'),
            // --- Payload -----------------------------------------------------------
            payload: ['name' => 'Updated Dummy'],
            // --- Response content --------------------------------------------------
            expectedResponse: fn () => DummyResource::make(queryModel('dummy_first')->load('children'))->response(),
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseHas('dummies', [
                    'id' => queryId('dummy_first'),
                    'name' => 'Updated Dummy',
                ]),
            ],
        );
    });

    describe('PATCH /api/dummies/{dummy} - update relation', function () use ($context) {
        Scenario::forApiRoute()->valid(
            description: "returns 200 with updated resource when updating 'children'",
            context: $context->withDatabaseSetup([
                'create_user',
                'create_dummy',
                'create_dummy_child',
            ]),
            // --- Payload -----------------------------------------------------------
            payload: ['children_ids' => [getQueryId('dummy_child_first')]],
            // --- Response content --------------------------------------------------
            expectedResponse: fn () => DummyResource::make(queryModel('dummy_first')->load('children'))->response(),
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseCount('dummy_children', 1),
                fn () => assertDatabaseHas('dummy_children', [
                    'id' => queryId('dummy_child_first'),
                    'dummy_id' => queryId('dummy_first'),
                ]),
            ],
        );
    });

    describe('PATCH /api/dummies/{dummy} - update multiple fields', function () use ($context) {
        Scenario::forApiRoute()->valid(
            description: "returns 200 with updated resource when updating 'name' and 'email'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['name' => 'Updated Dummy', 'email' => 'updated@dummy.com'],
            // --- Response content --------------------------------------------------
            expectedResponse: fn () => DummyResource::make(queryModel('dummy_first')->load('children'))->response(),
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseHas('dummies', [
                    'id' => queryId('dummy_first'),
                    'name' => 'Updated Dummy',
                    'email' => 'updated@dummy.com',
                ]),
            ],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for route: api.dummies.update
 * ───────────────────────────────────────
 */
describe('ApiRoute : failure', function () use ($context) {
    describe('PATCH /api/dummies/{dummy} - invalid values', function () use ($context) {
        Scenario::forApiRoute()->invalid(
            description: "returns 422 with invalid 'email'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['email' => 'invalid-email'],
            // --- JSON Structure ----------------------------------------------------
            expectedErrorStructure: ['errors' => ['email']],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseMissing('dummies', [
                    'id' => queryId('dummy_first'),
                    'email' => 'invalid-email',
                ]),
            ],
        );

        Scenario::forApiRoute()->invalid(
            description: "returns 422 with invalid 'children'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['children_ids' => [88888, 99999]],
            // --- JSON Structure ----------------------------------------------------
            expectedErrorStructure: ['errors' => ['children_ids.0', 'children_ids.1']],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummy_children'),
            ],
        );

        Scenario::forApiRoute()->invalid(
            description: "returns 422 with invalid 'email' and 'age'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['email' => 'invalid-email', 'age' => 10],
            // --- JSON Structure ----------------------------------------------------
            expectedErrorStructure: ['errors' => ['email', 'age']],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseMissing('dummies', [
                    'id' => queryId('dummy_first'),
                    'email' => 'invalid-email',
                    'age' => 10,
                ]),
            ],
        );
    });

    describe('PATCH /api/dummies/{dummy} - invalid authorizations', function () use ($context) {
        Scenario::forApiRoute()->invalid(
            description: 'returns 401 when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Payload -----------------------------------------------------------
            payload: ['name' => 'Updated Dummy'],
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 401,
            // --- Error Message -----------------------------------------------------
            expectedErrorMessage: 'Unauthenticated.',
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseMissing('dummies', [
                    'id' => queryId('dummy_first'),
                    'name' => 'Updated Dummy',
                ]),
            ],
        );

        Scenario::forApiRoute()->invalid(
            description: 'returns 403 when not authorized',
            context: $context
                ->withActingAs('unauthorized')
                ->withDatabaseSetup(['create_unauthorized_user', 'create_dummy_with_children']),
            // --- Payload -----------------------------------------------------------
            payload: ['name' => 'Updated Dummy'],
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 403,
            // --- Error Message -----------------------------------------------------
            expectedErrorMessage: 'Not Allowed.',
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseMissing('dummies', [
                    'id' => queryId('dummy_first'),
                    'name' => 'Updated Dummy',
                ]),
            ],
        );
    });
});
