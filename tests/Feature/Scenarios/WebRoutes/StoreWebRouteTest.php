<?php

namespace Jgss\LaravelPestScenarios\Tests\Feature\WebRoutes;

use Illuminate\Support\ViewErrorBag;
use Illuminate\Testing\TestResponse;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;

use function Jgss\LaravelPestScenarios\queryId;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;

$context = Context::forWebRoute()->with(
    routeName: 'web.dummies.store',
    fromRouteName: 'web.dummies.index',
    actingAs: 'user',
    databaseSetup: 'create_user',
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for route web.dummies.store
 * ───────────────────────────────────────
 */
describe('WebRoute : success', function () use ($context) {
    describe('POST /web/dummies', function () use ($context) {
        Scenario::forWebRoute()->valid(
            description: 'returns 200 with newly created model',
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'new@dummy.com',
                'age' => 20,
                'is_active' => true,
            ],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res
                    ->assertSee('Dummy data')
                    ->assertSee('New Dummy')
                    ->assertSee('new@dummy.com')
                    ->assertViewHas('dummy'),
            ],
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
 * Invalid scenarios for route: web.dummies.store
 * ───────────────────────────────────────
 */
describe('WebRoute : failure', function () use ($context) {
    describe('POST /web/dummies - invalid values', function () use ($context) {
        Scenario::forWebRoute()->invalid(
            description: 'returns 302 and redirect with invalid values',
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'email' => 'invalid-email',
                'age' => 20,
                'is_active' => true,
            ],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertRedirectToRoute('web.dummies.index'),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: "redirects with errors containing 'name' when missing",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'email' => 'invalid-email',
                'age' => 20,
                'is_active' => true,
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
                        fn (ViewErrorBag $errors) => $errors->any()
                            && $errors->has('name')
                    ),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: "redirects with errors containing 'email' when invalid",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'invalid-email',
                'age' => 20,
                'is_active' => true,
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
                        fn (ViewErrorBag $errors) => $errors->any()
                            && $errors->has('email')
                    ),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: "redirects with errors containing 'age' when invalid",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'new@dummy.com',
                'age' => 10,
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
                        fn (ViewErrorBag $errors) => $errors->any()
                            && $errors->has('age')
                    ),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
            ],
        );

        Scenario::forWebRoute()->invalid(
            description: "redirects with errors containing 'email' and 'age' when invalid",
            context: $context,
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'invalid-email',
                'age' => 10,
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
                        fn (ViewErrorBag $errors) => $errors->any()
                            && $errors->has('email')
                            && $errors->has('age')
                    ),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
            ],
        );
    });

    describe('POST /web/dummies - invalid authorizations', function () use ($context) {
        Scenario::forWebRoute()->invalid(
            description: 'returns 302 and redirect when not authenticated',
            context: $context->withActingAs('guest'),
            // --- Payload -----------------------------------------------------------
            payload: [
                'name' => 'New Dummy',
                'email' => 'new@dummy.com',
                'age' => 20,
                'is_active' => true,
            ],
            // --- Response assertions -----------------------------------------------
            responseAssertions: [
                fn (TestResponse $res) => $res->assertRedirectToRoute('login'),
            ],
            // --- Database assertions -----------------------------------------------
            databaseAssertions: [
                fn () => assertDatabaseEmpty('dummies'),
            ],
        );
    });
});
