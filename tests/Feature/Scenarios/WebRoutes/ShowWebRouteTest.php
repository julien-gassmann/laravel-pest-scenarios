<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\WebRoutes;

use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

use function Jgss\LaravelPestScenarios\getQueryId;

$context = Context::forWebRoute()->with(
    routeName: 'web.dummies.show',
    routeParameters: ['dummy' => getQueryId('dummy_first')],
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummy_with_children'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route web.dummies.show
 * ───────────────────────────────────────
 */
describe('WebRoute : success', function () use ($context) {
    describe('GET /web/dummies/{dummy}', function () use ($context) {
        Scenario::forWebRoute()->valid(
            description: 'returns 200 with corresponding model',
            context: $context,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummy data')
                    ->assertSee(queryDummy('dummy_first')->name)
                    ->assertSee(queryDummy('dummy_first')->email)
                    ->assertSee((string) queryDummy('dummy_first')->age)
                    ->assertSee((string) queryDummy('dummy_first')->children()->count()),
            ],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for route: web.dummies.show
 * ───────────────────────────────────────
 */
describe('WebRoute : failure', function () use ($context) {
    describe('GET /web/dummies/{dummy} - invalid values', function () use ($context) {
        Scenario::forWebRoute()->invalid(
            description: 'returns 404 with non-existent dummy',
            context: $context->withRouteParameters(['dummy' => 999999]),
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 404,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertSee('Not Found'),
            ],
        );
    });

    describe('GET /web/dummies/{dummy} - invalid authorizations', function () use ($context) {
        Scenario::forWebRoute()->invalid(
            description: 'returns 302 and redirect when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertRedirectToRoute('login'),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: 'returns 403 when not authorized',
            context: $context
                ->withActingAs('unauthorized')
                ->withDatabaseSetup(['create_unauthorized_user', 'create_dummy']),
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 403,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertSee('Not Allowed.'),
            ],
        );
    });
});
