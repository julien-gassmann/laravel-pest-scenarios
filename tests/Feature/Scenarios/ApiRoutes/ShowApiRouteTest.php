<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\ApiRoutes;

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Http\Resources\DummyResource;

use function Jgss\LaravelPestScenarios\getQueryId;
use function Jgss\LaravelPestScenarios\queryModel;

$context = Context::forApiRoute()->with(
    routeName: 'api.dummies.show',
    routeParameters: ['dummy' => getQueryId('dummy_first')],
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummy_with_children'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route api.dummies.show
 * ───────────────────────────────────────
 */
describe('ApiRoute : success', function () use ($context) {
    describe('GET /api/dummies/{dummy}', function () use ($context) {
        Scenario::forApiRoute()->valid(
            description: 'returns 200 with corresponding resource',
            context: $context,
            // --- Response content --------------------------------------------------
            expectedResponse: fn () => DummyResource::make(queryModel('dummy_first')->load('children'))->response(),
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for route: api.dummies.show
 * ───────────────────────────────────────
 */
describe('ApiRoute : failure', function () use ($context) {
    describe('GET /api/dummies/{dummy} - invalid authorizations', function () use ($context) {
        Scenario::forApiRoute()->invalid(
            description: 'returns 401 when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 401,
            // --- Error Message -----------------------------------------------------
            expectedErrorMessage: 'Unauthenticated.',
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
        );
    });
});
