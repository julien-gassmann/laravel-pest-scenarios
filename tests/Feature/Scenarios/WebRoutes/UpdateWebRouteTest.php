<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\WebRoutes;

use Illuminate\Support\ViewErrorBag;
use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

use function Jgss\LaravelPestScenarios\getQueryId;
use function Jgss\LaravelPestScenarios\queryId;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

$context = Context::forWebRoute()->with(
    routeName: 'web.dummies.update',
    routeParameters: ['dummy' => getQueryId('dummy_first')],
    fromRouteName: 'web.dummies.show',
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummy'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route web.dummies.update
 * ───────────────────────────────────────
 */
describe('WebRoute : success', function () use ($context) {
    describe('PUT /web/dummies/{dummy} - update one field', function () use ($context) {
        Scenario::forWebRoute()->valid(
            description: "returns 200 with updated dummy when updating 'name'",
            context: $context->withRouteName('web.put.dummies.update'),
            // --- Payload -----------------------------------------------------------
            payload: ['name' => 'Updated Dummy'],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummy data')
                    ->assertSee('Updated Dummy')
                    ->assertViewHas('dummy'),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseHas('dummies', [
                    'id' => queryId('dummy_first'),
                    'name' => 'Updated Dummy',
                ]),
            ],
        );
    });

    describe('PATCH /web/dummies/{dummy} - update relation', function () use ($context) {
        Scenario::forWebRoute()->valid(
            description: "returns 200 with updated dummy when updating 'children'",
            context: $context->withDatabaseSetup([
                'create_user',
                'create_dummy',
                'create_dummy_child',
            ]),
            // --- Payload -----------------------------------------------------------
            payload: ['children_ids' => [getQueryId('dummy_child_first')]],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummy data')
                    ->assertSee('1')
                    ->assertViewHas('dummy'),
            ],
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

    describe('PATCH /web/dummies/{dummy} - update multiple fields', function () use ($context) {
        Scenario::forWebRoute()->valid(
            description: "returns 200 with updated dummy when updating 'name' and 'email'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['name' => 'Updated Dummy', 'email' => 'updated@dummy.com'],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummy data')
                    ->assertSee('Updated Dummy')
                    ->assertSee('updated@dummy.com')
                    ->assertViewHas('dummy'),
            ],
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
 * Invalid scenarios for route: web.dummies.update
 * ───────────────────────────────────────
 */
describe('WebRoute : failure', function () use ($context) {
    describe('PATCH /web/dummies/{dummy} - invalid values', function () use ($context) {
        Scenario::forWebRoute()->invalid(
            description: "returns 302 and redirect with invalid 'email'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['email' => 'invalid-email'],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertRedirectToRoute(
                    'web.dummies.show',
                    ['dummy' => queryId('dummy_first')]
                ),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseMissing('dummies', [
                    'id' => queryId('dummy_first'),
                    'email' => 'invalid-email',
                ]),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: "redirects with errors containing 'email' when invalid",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['email' => 'invalid-email'],
            // --- Follow redirect ---------------------------------------------------
            shouldFollowRedirect: true,
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 200,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummy data')
                    ->assertViewHas(
                        'errors',
                        fn (ViewErrorBag $errors) => $errors->any()
                            && $errors->has('email')
                    ),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseMissing('dummies', [
                    'id' => queryId('dummy_first'),
                    'email' => 'invalid-email',
                ]),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: "redirects with errors containing 'children' when invalid",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['children_ids' => 10],
            // --- Follow redirect ---------------------------------------------------
            shouldFollowRedirect: true,
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 200,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummy data')
                    ->assertViewHas(
                        'errors',
                        fn (ViewErrorBag $errors) => $errors->any()
                            && $errors->has('children_ids')
                    ),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseMissing('dummies', [
                    'id' => queryId('dummy_first'),
                    'age' => 10,
                ]),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: "redirects with errors containing 'email' and 'age' when invalid",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['email' => 'invalid-email', 'age' => 10],
            // --- Follow redirect ---------------------------------------------------
            shouldFollowRedirect: true,
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 200,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummy data')
                    ->assertViewHas(
                        'errors',
                        fn (ViewErrorBag $errors) => $errors->any()
                            && $errors->has('email')
                            && $errors->has('age')
                    ),
            ],
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

    describe('PATCH /web/dummies/{dummy} - invalid authorizations', function () use ($context) {
        Scenario::forWebRoute()->invalid(
            description: 'returns 302 and redirect when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Payload -----------------------------------------------------------
            payload: ['name' => 'Updated Dummy'],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertRedirectToRoute('login'),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseMissing('dummies', [
                    'id' => queryId('dummy_first'),
                    'name' => 'Updated Dummy',
                ]),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: 'returns 403 when not authorized',
            context: $context
                ->withActingAs('unauthorized')
                ->withDatabaseSetup(['create_unauthorized_user', 'create_dummy_with_children']),
            // --- Payload -----------------------------------------------------------
            payload: ['name' => 'Updated Dummy'],
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 403,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertSee('Not Allowed.'),
            ],
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
