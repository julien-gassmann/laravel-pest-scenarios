<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Definitions\Contexts;

use Illuminate\Support\Facades\Route;
use Jgss\LaravelPestScenarios\Context;
use Mockery;
use PHPUnit\Framework\SkippedTestSuiteError;
use Workbench\App\Http\Requests\DummyRequest;
use Workbench\App\Models\Dummy;
use Workbench\App\Models\User;
use Workbench\App\Policies\DummyPolicy;

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
 * Valid scenarios for FormRequestContext class
 * ───────────────────────────────────────
 */
describe('Definitions - FormRequestContext : success', function () {

    // ------------------- With methods -------------------

    describe('With methods', function () {
        it('can replicate', function (array $dataset) {
            // Arrange: Get dataset infos
            /** @var string $property */
            ['method' => $method, 'property' => $property, 'default' => $default, 'new' => $new] = $dataset;

            // Arrange: Create 2 FormRequestContext instances
            $context = Context::forFormRequest()->with(formRequestClass: DummyRequest::class);
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
                'new' => makeMock(DummyPolicy::class, fn ($mock) => $mock),
            ]],
            'withPayload' => [[
                'method' => 'withPayload',
                'property' => 'payload',
                'default' => [],
                'new' => ['dummy', 'payload'],
            ]],
            'withRouteName' => [[
                'method' => 'withRouteName',
                'property' => 'routeName',
                'default' => null,
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
            // Arrange: Create 2 FormRequestContext instances
            $context = Context::forFormRequest()->with(formRequestClass: DummyRequest::class);
            $newContext = $context->withRoute('other.dummy.route', ['dummy' => 'parameter']);

            // Assert: Ensure context and new context are different with correct values
            expect($context)->not()->toBe($newContext)
                ->and(getProtectedProperty($context, 'routeName'))->toEqual(null)
                ->and(getProtectedProperty($context, 'routeParameters'))->toEqual([])
                ->and(getProtectedProperty($newContext, 'routeName'))->toEqual('other.dummy.route')
                ->and(getProtectedProperty($newContext, 'routeParameters'))->toEqual(['dummy' => 'parameter']);
        });
    });

    // ------------------- Getters -------------------

    describe('Getters', function () {
        it("can get property 'formRequestClass'", function () {
            // Arrange: Create FormRequestContext instance
            $context = Context::forFormRequest()->with(formRequestClass: DummyRequest::class);

            // Assert: Property is correctly returned
            expect($context->getFormRequestClass())->toBe(DummyRequest::class);
        });
    });

    // ------------------- Resolvers -------------------

    $context = Context::forFormRequest()->with(
        formRequestClass: DummyRequest::class,
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

        it('can resolves "getFormRequestInstance"', function () use ($context) {
            // Act: Call resolver
            $formRequest = $context->getFormRequestInstance();

            // Assert: Ensure form request instance is the expected one
            expect($formRequest)->toBeInstanceOf(DummyRequest::class);
        });

        it('can resolves "getFormRequestInstanceWithBindings"', function () use ($context) {
            // Arrange: Create dummy
            databaseSetup('create_dummy');

            // Act: Call resolver
            $formRequest = $context->getFormRequestInstanceWithBindings();

            // Assert: Ensure form request route and route binding are the expected ones
            expect($formRequest->route()->getName())->toBe('api.dummies.update')
                ->and($formRequest->route('dummy'))->toEqual(queryDummy('dummy_first'));
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

        it('can resolves "initMocks"', function () {
            // Arrange: Create FormRequestContext instance with mock
            $mock = Mockery::mock(DummyPolicy::class);
            $context = Context::forFormRequest()->with(
                formRequestClass: DummyRequest::class,
                mocks: [DummyPolicy::class => fn () => $mock],
            );

            // Act: Call resolver
            $context->initMocks();

            // Assert: Ensure DummyPolicy is mocked
            expect(app(DummyPolicy::class))->toBe($mock);
        });
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for FormRequestContext class
 * ───────────────────────────────────────
 */
describe('Definitions - FormRequestContext : failure', function () {

    // ------------------- HasRouteContext -------------------

    describe('HasRouteContext', function () {
        it('throws exception with non-existing route name', function () {
            // Arrange: Create FormRequestContext
            $context = Context::forFormRequest()->with(
                formRequestClass: DummyRequest::class,
                routeName: 'non.existing.route',
            );

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getRouteInstance())
                ->toThrow(new SkippedTestSuiteError("Unable to find route: 'non.existing.route'."));
        });

        it('throws exception with invalid route parameters', function () {
            // Arrange: Create FormRequestContext
            $context = Context::forFormRequest()->with(
                formRequestClass: DummyRequest::class,
                routeName: 'api.dummies.show',
                /** @phpstan-ignore-next-line */
                routeParameters: ['dummy' => fn () => ['not', 'scalar']]
            );

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getRouteParameters())
                ->toThrow(new SkippedTestSuiteError('Unable to cast route parameters as string.'));
        });

        it('throws exception when route parameter is resolved in non-existing model', function () {
            // Arrange: Create FormRequestContext
            databaseSetup('create_dummy');
            $context = Context::forFormRequest()->with(
                formRequestClass: DummyRequest::class,
                routeName: 'api.dummies.update',
                routeParameters: ['dummy' => 999999],
            );

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getFormRequestInstanceWithBindings())
                ->toThrow(new SkippedTestSuiteError("Unable to find model 'dummy.id' with value '999999'."));
        });
    });

    // ------------------- HasFormRequestContext -------------------

    describe('HasFormRequestContext', function () {
        it('throws exception with non-existing FormRequest class', function () {
            // Arrange: Create FormRequestContext
            /** @phpstan-ignore-next-line */
            $context = Context::forFormRequest()->with(formRequestClass: 'NonExistingFormRequestClass');

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getFormRequestInstance())
                ->toThrow(new SkippedTestSuiteError("Unable to find form request class : 'NonExistingFormRequestClass'."));
        });

        it('throws exception with class not extending FormRequest', function () {
            // Arrange: Create FormRequestContext
            $className = DummyPolicy::class;
            /** @phpstan-ignore-next-line */
            $context = Context::forFormRequest()->with(formRequestClass: $className);

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getFormRequestInstance())
                ->toThrow(new SkippedTestSuiteError("Provided class '$className' doesn't extend FormRequest."));
        });
    });
});
