<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\ApiRoutes;

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

use function Jgss\LaravelPestScenarios\getQueryId;
use function Jgss\LaravelPestScenarios\queryModel;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;

$context = Context::forApiRoute()->with(
    routeName: 'api.dummies.delete',
    routeParameters: ['dummy' => getQueryId('dummy_first')],
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummy'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route api.dummies.delete
 * ───────────────────────────────────────
 */
describe('ApiRoute : success', function () use ($context): void {
    describe('DELETE /api/dummies/{dummy}', function () use ($context): void {
        Scenario::forApiRoute()->valid(
            description: 'returns 200 when deleted dummy',
            context: $context->withDatabaseSetup(['create_user', 'create_dummy_with_children']),
            // --- JSON Structure ----------------------------------------------------
            expectedStructure: 'message',
            // --- Response content --------------------------------------------------
            expectedResponse: fn () => response()->json(['message' => 'Dummy deleted']),
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
                fn () => assertDatabaseEmpty('dummy_children'),
            ],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for route: api.dummies.delete
 * ───────────────────────────────────────
 */
describe('ApiRoute : failure', function () use ($context): void {
    describe('DELETE /api/dummies/{dummy} - invalid values', function () use ($context): void {
        Scenario::forApiRoute()->invalid(
            description: 'returns 404 with non-existent dummy',
            context: $context->withRouteParameters(['dummy' => 999999]),
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 404,
            // --- Error Message -----------------------------------------------------
            expectedErrorMessage: 'No query results for model [Workbench\\App\\Models\\Dummy] 999999',
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseCount('dummies', 1),
            ],
        );
    });

    describe('DELETE /api/dummies/{dummy} - invalid authorizations', function () use ($context): void {
        Scenario::forApiRoute()->invalid(
            description: 'returns 401 when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 401,
            // --- Error Message -----------------------------------------------------
            expectedErrorMessage: 'Unauthenticated.',
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseCount('dummies', 1),
                fn () => assertDatabaseHas('dummies', queryModel('dummy_first')->toArray()),
            ],
        );

        Scenario::forApiRoute()->invalid(
            description: 'returns 403 when not authorized',
            context: $context
                ->withActingAs('unauthorized')
                ->withDatabaseSetup(['create_unauthorized_user', 'create_dummy']),
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 403,
            // --- Error Message -----------------------------------------------------
            expectedErrorMessage: 'Not Allowed.',
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseCount('dummies', 1),
                fn () => assertDatabaseHas('dummies', queryModel('dummy_first')->toArray()),
            ],
        );
    });
});
