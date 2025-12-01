<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Definitions\Contexts;

use Illuminate\Support\Facades\Route;
use Jgss\LaravelPestScenarios\Context;
use Mockery;
use PHPUnit\Framework\SkippedTestSuiteError;
use Workbench\App\Models\User;
use Workbench\App\Policies\DummyPolicy;
use Workbench\App\Rules\DummyRule;

use function Jgss\LaravelPestScenarios\actor;
use function Jgss\LaravelPestScenarios\databaseSetup;
use function Jgss\LaravelPestScenarios\getDatabaseSetup;
use function Jgss\LaravelPestScenarios\getQueryId;
use function Jgss\LaravelPestScenarios\makeMock;
use function Jgss\LaravelPestScenarios\queryId;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertTrue;

/**
 * ───────────────────────────────────────
 * Valid scenarios for ApiRouteContext class
 * ───────────────────────────────────────
 */
describe('Definitions - ApiRouteContext : success', function () {

    // ------------------- With methods -------------------

    describe('With methods', function () {
        it('can replicate', function (array $dataset) {
            // Arrange: Get dataset infos
            /** @var string $property */
            ['method' => $method, 'property' => $property, 'default' => $default, 'new' => $new] = $dataset;

            // Arrange: Create 2 ApiRouteContext instances
            $context = Context::forApiRoute()->with(routeName: 'dummy.route');
            $newContext = (object) $context->$method($new);

            // Assert: Ensure context and new context are different with correct values
            expect($context)->not()->toBe($newContext)
                ->and(getProtectedProperty($context, $property))->toEqual($default)
                ->and(getProtectedProperty($newContext, $property))->toEqual($new);
        })->with([
            'withActingAs' => [[
                'method' => 'withActingAs',
                'property' => 'actingAs',
                'default' => fn () => null,
                'new' => fn () => new User,
            ]],
            'withAppLocale' => [[
                'method' => 'withAppLocale',
                'property' => 'appLocale',
                'default' => null,
                'new' => 'fr',
            ]],
            'withDatabaseSetup' => [[
                'method' => 'withDatabaseSetup',
                'property' => 'databaseSetup',
                'default' => fn () => null,
                'new' => getDatabaseSetup('create_user'),
            ]],
            'withMocks' => [[
                'method' => 'withMocks',
                'property' => 'mocks',
                'default' => [],
                'new' => makeMock(DummyRule::class, fn ($mock) => $mock),
            ]],
            'withRouteName' => [[
                'method' => 'withRouteName',
                'property' => 'routeName',
                'default' => 'dummy.route',
                'new' => 'other.dummy.route',
            ]],
            'withRouteParameters' => [[
                'method' => 'withRouteParameters',
                'property' => 'routeParameters',
                'default' => [],
                'new' => ['dummy' => 'parameter'],
            ]],
        ]);

        it('can replicate with dataset "withRoute"', function () {
            // Arrange: Create 2 ApiRouteContext instances
            $context = Context::forApiRoute()->with(routeName: 'dummy.route');
            $newContext = $context->withRoute('other.dummy.route', ['dummy' => 'parameter']);

            // Assert: Ensure context and new context are different with correct values
            expect($context)->not()->toBe($newContext)
                ->and(getProtectedProperty($context, 'routeName'))->toEqual('dummy.route')
                ->and(getProtectedProperty($context, 'routeParameters'))->toEqual([])
                ->and(getProtectedProperty($newContext, 'routeName'))->toEqual('other.dummy.route')
                ->and(getProtectedProperty($newContext, 'routeParameters'))->toEqual(['dummy' => 'parameter']);
        });
    });

    // ------------------- Getters -------------------

    describe('Getters', function () {
        it("can get property 'routeName'", function () {
            // Arrange: Create ApiRouteContext instance
            $context = Context::forApiRoute()->with(routeName: 'dummy.route');

            // Assert: Property is correctly returned
            expect($context->getRouteName())->toBe('dummy.route');
        });
    });

    // ------------------- Resolvers -------------------

    $context = Context::forApiRoute()->with(
        routeName: 'api.dummies.update',
        routeParameters: ['dummy' => getQueryId('dummy_first')],
        actingAs: 'user',
        appLocale: 'fr',
        databaseSetup: 'create_dummies',
    );

    describe('Resolvers', function () use ($context) {
        it('can resolves "actAs"', function () use ($context) {
            // Arrange: Create user
            databaseSetup('create_user');
            $actor = actor('user');

            // Act: Call resolver
            $context->actAs();

            // Assert: Ensure user is authenticated
            /** @var User $actor */
            assertAuthenticatedAs($actor);
        });

        it('can resolves "getRouteInstance"', function () use ($context) {
            // Arrange: Get expected route
            $expectedRoute = Route::getRoutes()->getByName('api.dummies.update');

            // Act: Call resolver
            $actualRoute = $context->getRouteInstance();

            // Assert: Ensure route instance is the expected one
            expect($actualRoute)->toBe($expectedRoute);
        });

        it('can resolves "getRouteParameters"', function () use ($context) {
            // Arrange: Create dummy and get expected parameters
            databaseSetup('create_dummy');
            $expectedParameters = ['dummy' => strval(queryId('dummy_first'))];

            // Act: Call resolver
            $actualParameters = $context->getRouteParameters();

            // Assert: Ensure route parameters are the expected ones
            expect($actualParameters)->toBe($expectedParameters);
        });

        it('can resolves "localiseApp"', function () use ($context) {
            // Act: Call resolver
            $context->localiseApp();

            // Assert: Ensure app has expected localisation
            assertTrue(app()->getLocale() === 'fr');
        });

        it('can resolves "setupDatabase"', function () use ($context) {
            // Act: Call resolver
            $context->setupDatabase();

            // Assert: Ensure database is filled
            assertDatabaseCount('dummies', 10);
        });

        it('can resolves "initMocks"', function () use ($context) {
            // Arrange: Create ApiRouteContext instance with mock
            $mock = Mockery::mock(DummyPolicy::class);
            $context = Context::forApiRoute()->with(
                routeName: 'dummy.route',
                mocks: [DummyPolicy::class => fn () => $mock],
            );

            // Act: Call resolver
            $context->initMocks();

            // Assert: Ensure DummyPolicy is mocked
            expect(app(DummyPolicy::class))->toEqual($mock);
        });
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for ApiRouteContext class
 * ───────────────────────────────────────
 */
describe('Definitions - ApiRouteContext : failure', function () {

    // ------------------- HasRouteContext -------------------

    describe('HasRouteContext', function () {
        it('throws exception with non-existing route name', function () {
            // Arrange: Create ApiRouteContext
            $context = Context::forApiRoute()->with('non.existing.route');

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getRouteInstance())
                ->toThrow(new SkippedTestSuiteError("Unable to find route: 'non.existing.route'."));
        });

        it('throws exception with invalid route parameters', function () {
            // Arrange: Create ApiRouteContext
            $context = Context::forApiRoute()->with(
                routeName: 'api.dummies.show',
                /** @phpstan-ignore-next-line */
                routeParameters: ['dummy' => fn () => ['not', 'scalar']]
            );

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getRouteParameters())
                ->toThrow(new SkippedTestSuiteError('Unable to cast route parameters as string.'));
        });
    });
});
