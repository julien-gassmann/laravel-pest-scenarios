<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\ApiRoutes;

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Http\Resources\DummyResource;

use function Jgss\LaravelPestScenarios\queryId;
use function Jgss\LaravelPestScenarios\queryModel;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

$context = Context::forApiRoute()->with(
    routeName: 'api.dummies.store',
    actingAs: 'user',
    databaseSetup: 'create_user',
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route api.dummies.store
 * ───────────────────────────────────────
 */
describe('ApiRoute : success', function () use ($context) {
    describe('POST /api/dummies', function () use ($context) {
        Scenario::forApiRoute()->valid(
            description: 'returns 201 with newly created resource',
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'new@dummy.com',
                'age' => 20,
                'is_active' => true,
            ],
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 201,
            // --- Response content --------------------------------------------------
            expectedResponse: fn () => DummyResource::make(queryModel('dummy_last'))->response(),
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseCount('dummies', 1),
                fn () => assertDatabaseHas('dummies', [
                    'id' => queryId('dummy_last'),
                    'name' => 'New Dummy',
                    'email' => 'new@dummy.com',
                    'age' => 20,
                    'is_active' => true,
                ]),
            ],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for route: api.dummies.store
 * ───────────────────────────────────────
 */
describe('ApiRoute : failure', function () use ($context) {
    describe('POST /api/dummies - invalid values', function () use ($context) {
        Scenario::forApiRoute()->invalid(
            description: "returns 422 with missing 'name'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'email' => 'invalid-email',
                'age' => 20,
                'is_active' => true,
            ],
            // --- JSON Structure ----------------------------------------------------
            expectedErrorStructure: ['errors' => ['name']],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
                fn () => assertDatabaseMissing('dummies', [
                    'email' => 'invalid-email',
                    'age' => 20,
                    'is_active' => true,
                ]),
            ],
        );

        Scenario::forApiRoute()->invalid(
            description: "returns 422 with invalid 'email'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'invalid-email',
                'age' => 20,
            ],
            // --- JSON Structure ----------------------------------------------------
            expectedErrorStructure: ['errors' => ['email']],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
                fn () => assertDatabaseMissing('dummies', [
                    'name' => 'New Dummy',
                    'email' => 'invalid-email',
                    'age' => 20,
                ]),
            ],
        );

        Scenario::forApiRoute()->invalid(
            description: "returns 422 with invalid 'age'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'new@dummy.com',
                'age' => 10,
            ],
            // --- JSON Structure ----------------------------------------------------
            expectedErrorStructure: ['errors' => ['age']],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
                fn () => assertDatabaseMissing('dummies', [
                    'name' => 'New Dummy',
                    'email' => 'new@dummy.com',
                    'age' => 10,
                ]),
            ],
        );

        Scenario::forApiRoute()->invalid(
            description: "returns 422 with invalid 'email' and 'age'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'invalid-email',
                'age' => 10,
            ],
            // --- JSON Structure ----------------------------------------------------
            expectedErrorStructure: ['errors' => ['email', 'age']],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
                fn () => assertDatabaseMissing('dummies', [
                    'name' => 'New Dummy',
                    'email' => 'invalid-email',
                    'age' => 10,
                ]),
            ],
        );
    });

    describe('POST /api/dummies - invalid authorizations', function () use ($context) {
        Scenario::forApiRoute()->invalid(
            description: 'returns 401 when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'new@dummy.com',
                'age' => 20,
                'is_active' => true,
            ],
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 401,
            // --- Error Message -----------------------------------------------------
            expectedErrorMessage: 'Unauthenticated.',
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),

                fn () => assertDatabaseMissing('dummies', [
                    'name' => 'New Dummy',
                    'email' => 'new@dummy.com',
                    'age' => 20,
                    'is_active' => true,
                ]),
            ],
        );
    });
});
