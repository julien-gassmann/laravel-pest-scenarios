<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\ApiRoutes;

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Http\Resources\DummyResource;
use Workbench\App\Models\Dummy;

$context = Context::forApiRoute()->with(
    routeName: 'api.dummies.index',
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummies'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route api.dummies.index
 * ───────────────────────────────────────
 */
describe('ApiRoute : success', function () use ($context) {
    describe('GET /api/dummies - without payload', function () use ($context) {
        Scenario::forApiRoute()->valid(
            description: 'returns 200 with corresponding resources',
            context: $context,
            // --- JSON Structure ----------------------------------------------------
            expectedStructure: 'pagination',
            // --- Response content --------------------------------------------------
            expectedResponse: fn () => DummyResource::collection(Dummy::query()->paginate(5))->response(),
        );
    });

    describe('GET /api/dummies - with payload', function () use ($context) {
        Scenario::forApiRoute()->valid(
            description: "returns 200 with corresponding resources when 'perPage' = 10 and 'sort' = 'name'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['perPage' => 10, 'sort' => 'name'],
            // --- JSON Structure ----------------------------------------------------
            expectedStructure: 'pagination',
            // --- Response content --------------------------------------------------
            expectedResponse: fn () => DummyResource::collection(
                Dummy::query()
                    ->orderBy('name')
                    ->paginate(10)
            )->response(),
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for route: api.dummies.index
 * ───────────────────────────────────────
 */
describe('ApiRoute : failure', function () use ($context) {
    describe('GET /api/dummies - with invalid payload', function () use ($context) {
        Scenario::forApiRoute()->invalid(
            description: 'returns 422 with corresponding error message',
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'perPage' => 'invalid_items_per_page',
                'sort' => 'invalid_sorting',
            ],
            // --- JSON Structure ----------------------------------------------------
            expectedErrorStructure: ['errors' => ['perPage', 'sort']],
        );
    });

    describe('GET /api/dummies - invalid authorizations', function () use ($context) {
        Scenario::forApiRoute()->invalid(
            description: 'returns 401 when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 401,
            // --- Error Message -----------------------------------------------------
            expectedErrorMessage: 'Unauthenticated.',
        );
    });
});
