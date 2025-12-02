<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\WebRoutes;

use Illuminate\Support\ViewErrorBag;
use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Models\Dummy;

$context = Context::forWebRoute()->with(
    routeName: 'web.dummies.index',
    actingAs: 'user',
    databaseSetup: ['create_user', 'create_dummies'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route web.dummies.index
 * ───────────────────────────────────────
 */
describe('WebRoute : success', function () use ($context): void {
    describe('GET /web/dummies - without payload', function () use ($context): void {
        Scenario::forWebRoute()->valid(
            description: 'returns dummies list with 5 elements',
            context: $context,
            // --- Response assertions -----------------------------------------------
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
        );
    });

    describe('GET /web/dummies - with payload', function () use ($context): void {
        Scenario::forWebRoute()->valid(
            description: "returns dummies list with 10 elements when 'perPage' = 10 and 'sort' = 'name'",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: ['perPage' => 10, 'sort' => 'name'],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummies list')
                    ->assertSeeInOrder(
                        Dummy::query()
                            ->orderBy('name')
                            ->limit(10)
                            ->pluck('name')
                            ->toArray()
                    )
                    ->assertViewHas(
                        'dummiesPaginated',
                        Dummy::query()
                            ->orderBy('name')
                            ->paginate(10)
                    ),
            ],
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for route: web.dummies.index
 * ───────────────────────────────────────
 */
describe('WebRoute : failure', function () use ($context): void {
    describe('GET /web/dummies - invalid payload', function () use ($context): void {
        Scenario::forWebRoute()->invalid(
            description: 'returns 302 and redirect',
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'perPage' => 'invalid_items_per_page',
                'sort' => 'invalid_sorting',
            ],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertRedirectToRoute('web.dummies.index'),
            ]
        );

        Scenario::forWebRoute()->invalid(
            description: 'redirects to dummies list with corresponding errors',
            context: $context->withFromRouteName('web.dummies.index'),
            // --- Payload -----------------------------------------------------------
            payload: [
                'perPage' => 'invalid_items_per_page',
                'sort' => 'invalid_sorting',
            ],
            // --- Follow redirect ---------------------------------------------------
            shouldFollowRedirect: true,
            // --- Status code -------------------------------------------------------
            expectedStatusCode: 200,
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummies list')
                    ->assertViewHas(
                        'errors',
                        fn (ViewErrorBag $errors): bool => $errors->any()
                            && $errors->has('perPage')
                            && $errors->has('sort')
                    ),
            ],
        );
    });

    describe('GET /web/dummies - invalid authorizations', function () use ($context): void {
        Scenario::forWebRoute()->invalid(
            description: 'returns 302 and redirect when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertRedirectToRoute('login'),
            ],
        );
    });
});
