<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Jgss\LaravelPestScenarios\Tests\Unit\Definitions\Contexts;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Exceptions\ResolutionFailedException;
use Mockery;
use Workbench\App\Http\Requests\DummyRequest;
use Workbench\App\Models\User;
use Workbench\App\Rules\DummyRule;

use function Jgss\LaravelPestScenarios\actor;
use function Jgss\LaravelPestScenarios\databaseSetup;
use function Jgss\LaravelPestScenarios\getQueryId;
use function Jgss\LaravelPestScenarios\makeMock;
use function Jgss\LaravelPestScenarios\queryId;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function PHPUnit\Framework\assertTrue;

/**
 * ───────────────────────────────────────
 * Valid scenarios for WebRouteContext class
 * ───────────────────────────────────────
 */
describe('Definitions - WebRouteContext : success', function (): void {

    // ------------------- With methods -------------------

    describe('With methods', function (): void {
        it('can replicate', function (array $dataset): void {
            // Arrange: Get dataset infos
            /** @var string $property */
            ['method' => $method, 'property' => $property, 'default' => $default, 'new' => $new] = $dataset;

            // Arrange: Create 2 WebRouteContext instances
            $context = Context::forWebRoute()->with(routeName: 'dummy.route');
            $newContext = (object) $context->$method($new);

            // Assert: Ensure context and new context are different with correct values
            expect($context)->not()->toBe($newContext);
            compareProperties(getProtectedProperty($context, $property), $default);
            compareProperties(getProtectedProperty($newContext, $property), $new);
        })->with([
            'withActingAs' => [[
                'method' => 'withActingAs',
                'property' => 'actingAs',
                'default' => fn (): null => null,
                'new' => fn (): User => new User,
            ]],
            'withAppLocale' => [[
                'method' => 'withAppLocale',
                'property' => 'appLocale',
                'default' => null,
                'new' => 'fr',
            ]],
            'withFromRouteName' => [[
                'method' => 'withFromRouteName',
                'property' => 'fromRouteName',
                'default' => 'dummy.route',
                'new' => 'from.dummy.route',
            ]],
            'withFromRouteParameters' => [[
                'method' => 'withFromRouteParameters',
                'property' => 'fromRouteParameters',
                'default' => [],
                'new' => ['dummy' => 'parameter'],
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

        it('can replicate with dataset "withDatabaseSetup"', function (): void {
            // Arrange: Create 2 FormRequestContext instances
            $context = Context::forFormRequest()->with(formRequestClass: DummyRequest::class);
            $newContext = $context->withDatabaseSetup('create_dummy');

            // Assert: Ensure context and new context are different
            expect($context)->not()->toBe($newContext);

            // Assert: Default setup has no effect on database
            $context->setupDatabase();
            assertDatabaseEmpty('dummies');

            // Assert: New setup fills database
            $newContext->setupDatabase();
            assertDatabaseCount('dummies', 1);
        });

        it('can replicate with dataset "withFromRoute"', function (): void {
            // Arrange: Create 2 WebRouteContext instances
            $context = Context::forWebRoute()->with(routeName: 'dummy.route');
            $newContext = $context->withFromRoute(fromRouteName: 'from.dummy.route', fromRouteParameters: ['dummy' => 'parameter']);

            // Assert: Ensure context and new context are different with correct values
            expect($context)->not()->toBe($newContext)
                ->and(getProtectedProperty($context, 'fromRouteName'))->toEqual('dummy.route')
                ->and(getProtectedProperty($context, 'fromRouteParameters'))->toEqual([])
                ->and(getProtectedProperty($newContext, 'fromRouteName'))->toEqual('from.dummy.route')
                ->and(getProtectedProperty($newContext, 'fromRouteParameters'))->toEqual(['dummy' => 'parameter']);
        });

        it('can replicate with dataset "withRoute"', function (): void {
            // Arrange: Create 2 WebRouteContext instances
            $context = Context::forWebRoute()->with(routeName: 'dummy.route');
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

    describe('Getters', function (): void {
        it("can get property 'routeName'", function (): void {
            // Arrange: Create WebRouteContext instance
            $context = Context::forWebRoute()->with(routeName: 'dummy.route');

            // Assert: Property is correctly returned
            expect($context->getRouteName())->toBe('dummy.route');
        });

        it("can get property 'fromRouteName'", function (): void {
            // Arrange: Create WebRouteContext instance
            $context = Context::forWebRoute()->with(
                routeName: 'dummy.route',
                fromRouteName: 'other.dummy.route',
            );

            // Assert: Property is correctly returned
            expect($context->getFromRouteName())->toBe('other.dummy.route');
        });
    });

    // ------------------- Resolvers -------------------

    $context = Context::forWebRoute()->with(
        routeName: 'web.dummies.update',
        routeParameters: ['dummy' => getQueryId('dummy_first')],
        fromRouteName: 'web.dummies.show',
        fromRouteParameters: ['dummy' => getQueryId('dummy_first')],
        actingAs: 'user',
        appLocale: 'fr',
        databaseSetup: 'create_dummies',
    );

    describe('Resolvers', function () use ($context): void {
        it('can resolves "actAs"', function () use ($context): void {
            // Arrange: Create user
            databaseSetup('create_user');
            $actor = actor('user');

            // Act: Call resolver
            $context->actAs();

            // Assert: Ensure user is authenticated
            /** @var User $actor */
            assertAuthenticatedAs($actor);
        });

        it('can resolves "getRouteInstance"', function () use ($context): void {
            // Arrange: Get expected route
            $expectedRoute = RouteFacade::getRoutes()->getByName('web.dummies.update');

            // Act: Call resolver
            $actualRoute = $context->getRouteInstance();

            // Assert: Ensure route instance is the expected one
            expect($actualRoute)->toBe($expectedRoute);
        });

        it('can resolves "getRouteParameters"', function () use ($context): void {
            // Arrange: Create dummy and get expected parameters
            databaseSetup('create_dummy');
            $expectedParameters = ['dummy' => strval(queryId('dummy_first'))];

            // Act: Call resolver
            $actualParameters = $context->getRouteParameters();

            // Assert: Ensure route parameters are the expected ones
            expect($actualParameters)->toBe($expectedParameters);
        });

        it('can resolves "getFromRouteInstance"', function () use ($context): void {
            // Arrange: Get expected from route
            $expectedRoute = RouteFacade::getRoutes()->getByName('web.dummies.show');

            // Act: Call resolver
            $actualRoute = $context->getFromRouteInstance();

            // Assert: Ensure from route instance is the expected one
            expect($actualRoute)->toBe($expectedRoute);
        });

        it('can resolves "getFromRouteParameters"', function () use ($context): void {
            // Arrange: Create dummy and get expected from parameters
            databaseSetup('create_dummy');
            $expectedParameters = ['dummy' => strval(queryId('dummy_first'))];

            // Act: Call resolver
            $actualParameters = $context->getFromRouteParameters();

            // Assert: Ensure from route parameters are the expected ones
            expect($actualParameters)->toBe($expectedParameters);
        });

        it('can resolves "localiseApp"', function () use ($context): void {
            // Act: Call resolver
            $context->localiseApp();

            // Assert: Ensure app has expected localisation
            assertTrue(app()->getLocale() === 'fr');
        });

        it('can resolves "setupDatabase"', function () use ($context): void {
            // Act: Call resolver
            $context->setupDatabase();

            // Assert: Ensure database is filled
            assertDatabaseCount('dummies', 10);
        });

        it('can resolves "initMocks"', function (): void {
            // Arrange: Create WebRouteContext instance with mock
            $mock = Mockery::mock(DummyRule::class);
            $context = Context::forWebRoute()->with(
                routeName: 'dummy.route',
                mocks: [DummyRule::class => fn () => $mock],
            );

            // Act: Call resolver
            $context->initMocks();

            // Assert: Ensure DummyPolicy is mocked
            expect(app(DummyRule::class))->toBe($mock);
        });
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for WebRouteContext class
 * ───────────────────────────────────────
 */
describe('Definitions - WebRouteContext : failure', function (): void {

    // ------------------- HasRouteContext -------------------

    describe('HasRouteContext', function (): void {
        it('throws exception with non-existing route name', function (): void {
            // Arrange: Create WebRouteContext
            $context = Context::forWebRoute()->with('non.existing.route');

            // Assert: Ensure correct Exception is thrown
            expect(fn (): Route => $context->getRouteInstance())
                ->toThrow(ResolutionFailedException::routeNameNotFound('non.existing.route'));
        });

        it('throws exception with invalid route parameters', function (): void {
            // Arrange: Create WebRouteContext
            $context = Context::forWebRoute()->with(
                routeName: 'web.dummies.show',
                /** @phpstan-ignore-next-line */
                routeParameters: ['dummy' => fn (): array => ['not', 'scalar']]
            );

            // Assert: Ensure correct Exception is thrown
            expect(fn (): array => $context->getRouteParameters())
                ->toThrow(ResolutionFailedException::routeParametersCasting());
        });

        it('throws exception with invalid route HTTP method', function (): void {
            // Arrange: Create invalid route
            $route = new Route(['INVALID'], '/invalid', fn (): null => null);
            $route->name('invalid.http.method');

            // Arrange: Mock Route facade to return invalid route
            $routeCollection = new RouteCollection;
            $routeCollection->add($route);
            RouteFacade::expects('getRoutes')->andReturn($routeCollection);

            // Arrange: Create WebRouteContext
            $context = Context::forWebRoute()->with(routeName: 'invalid.http.method');

            // Assert: Ensure correct Exception is thrown
            expect(fn (): string => $context->getRouteHttpMethod())
                ->toThrow(ResolutionFailedException::routeMethodNotFound('invalid.http.method'));
        });
    });

    // ------------------- HasFromRouteContext -------------------

    describe('HasFromRouteContext', function (): void {
        it('throws exception with non-existing from route name', function (): void {
            // Arrange: Create WebRouteContext
            $context = Context::forWebRoute()->with(
                routeName: 'web.dummies.index',
                fromRouteName: 'non.existing.from.route',
            );

            // Assert: Ensure correct Exception is thrown
            expect(fn (): Route => $context->getFromRouteInstance())
                ->toThrow(ResolutionFailedException::routeNameNotFound('non.existing.from.route'));
        });

        it('throws exception with invalid from route parameters', function (): void {
            // Arrange: Create WebRouteContext
            $context = Context::forWebRoute()->with(
                routeName: 'web.dummies.index',
                fromRouteName: 'web.dummies.show',
                /** @phpstan-ignore-next-line */
                fromRouteParameters: ['dummy' => fn (): array => ['not', 'scalar']]
            );

            // Assert: Ensure correct Exception is thrown
            expect(fn (): array => $context->getFromRouteParameters())
                ->toThrow(ResolutionFailedException::routeParametersCasting());
        });
    });
});
