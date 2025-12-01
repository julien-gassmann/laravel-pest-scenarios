<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\WebRoutes;

use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Models\Dummy;

use function Jgss\LaravelPestScenarios\getQueryId;
use function Jgss\LaravelPestScenarios\queryId;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

$context = Context::forWebRoute()->with(
    routeName: 'web.dummies.delete',
    routeParameters: ['dummy' => getQueryId('dummy_first')],
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummies'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route web.dummies.delete
 * ───────────────────────────────────────
 */
describe('WebRoute : success', function () use ($context) {
    describe('DELETE /web/dummies/{dummy}', function () use ($context) {
        Scenario::forWebRoute()->valid(
            description: 'returns dummies list with 9 elements',
            context: $context,
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummies list')
                    ->assertSeeInOrder(
                        Dummy::query()
                            ->limit(5)
                            ->pluck('name')
                            ->toArray()
                    )
                    ->assertViewHas(
                        'dummiesPaginated',
                        Dummy::query()->paginate(5)
                    ),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseCount('dummies', 9),
            ],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for route: web.dummies.delete
 * ───────────────────────────────────────
 */
describe('WebRoute : failure', function () use ($context) {
    describe('DELETE /web/dummies/{dummy} - invalid values', function () use ($context) {
        Scenario::forWebRoute()->invalid(
            description: 'returns 404 with non-existent dummy',
            context: $context->withRouteParameters(['dummy' => 999999]),
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 404,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertSee('Not Found'),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseCount('dummies', 10),
            ],
        );
    });

    describe('DELETE /web/dummies/{dummy} - invalid authorizations', function () use ($context) {
        Scenario::forWebRoute()->invalid(
            description: 'returns 302 and redirect when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertRedirectToRoute('login'),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseCount('dummies', 10),
                fn () => assertDatabaseHas('dummies', ['id' => queryId('dummy_first')]),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: 'returns 403 when not authorized',
            context: $context
                ->withActingAs('unauthorized')
                ->withDatabaseSetup(['create_unauthorized_user', 'create_dummies']),
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 403,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertSee('Not Allowed.'),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseCount('dummies', 10),
                fn () => assertDatabaseHas('dummies', ['id' => queryId('dummy_first')]),
            ],
        );
    });
});
