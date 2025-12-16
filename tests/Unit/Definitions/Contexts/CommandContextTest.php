<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Jgss\LaravelPestScenarios\Tests\Unit\Definitions\Contexts;

use Jgss\LaravelPestScenarios\Context;
use Mockery;
use Workbench\App\Http\Requests\DummyRequest;
use Workbench\App\Policies\DummyPolicy;

use function Jgss\LaravelPestScenarios\makeMock;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function PHPUnit\Framework\assertTrue;

/**
 * ───────────────────────────────────────
 * Valid scenarios for CommandContext class
 * ───────────────────────────────────────
 */
describe('Definitions - CommandContext : success', function (): void {

    // ------------------- With methods -------------------

    describe('With methods', function (): void {
        it('can replicate', function (array $dataset): void {
            // Arrange: Get dataset infos
            /** @var string $property */
            ['method' => $method, 'property' => $property, 'default' => $default, 'new' => $new] = $dataset;

            // Arrange: Create 2 CommandContext instances
            $context = Context::forCommand()->with(command: 'dummy:command');
            $newContext = (object) $context->$method($new);

            // Assert: Ensure context and new context are different with correct values
            expect($context)->not()->toBe($newContext);
            compareProperties(getProtectedProperty($context, $property), $default);
            compareProperties(getProtectedProperty($newContext, $property), $new);
        })->with([
            'withAppLocale' => [[
                'method' => 'withAppLocale',
                'property' => 'appLocale',
                'default' => null,
                'new' => 'fr',
            ]],
            'withMocks' => [[
                'method' => 'withMocks',
                'property' => 'mocks',
                'default' => [],
                'new' => makeMock(DummyPolicy::class, fn ($mock) => $mock),
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
    });

    // ------------------- Getters -------------------

    describe('Getters', function (): void {
        it("can get property 'command'", function (): void {
            // Arrange: Create CommandContext instance
            $context = Context::forCommand()->with(command: 'dummy:command');

            // Assert: Property is correctly returned
            expect($context->getCommand())->toBe('dummy:command');
        });
    });

    // ------------------- Resolvers -------------------

    $context = Context::forCommand()->with(
        command: 'dummy:command',
        appLocale: 'fr',
        databaseSetup: 'create_dummies',
    );

    describe('Resolvers', function () use ($context): void {
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
            // Arrange: Create CommandContext instance with mock
            $mock = Mockery::mock(DummyPolicy::class);
            $context = Context::forCommand()->with(
                command: 'dummy:command',
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
 * Invalid scenarios for CommandContext class
 * ───────────────────────────────────────
 */
// describe('Definitions - CommandContext : failure', function () {
//
// });
