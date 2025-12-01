<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Definitions\Contexts;

use Jgss\LaravelPestScenarios\Context;
use Mockery;
use PHPUnit\Framework\SkippedTestSuiteError;
use Workbench\App\Models\User;
use Workbench\App\Policies\DummyPolicy;
use Workbench\App\Rules\DummyRule;

use function Jgss\LaravelPestScenarios\actor;
use function Jgss\LaravelPestScenarios\databaseSetup;
use function Jgss\LaravelPestScenarios\getDatabaseSetup;
use function Jgss\LaravelPestScenarios\makeMock;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertTrue;

/**
 * ───────────────────────────────────────
 * Valid scenarios for RuleContext class
 * ───────────────────────────────────────
 */
describe('Definitions - RuleContext : success', function () {

    // ------------------- With methods -------------------

    describe('With methods', function () {
        it('can replicate', function (array $dataset) {
            // Arrange: Get dataset infos
            /** @var string $property */
            ['method' => $method, 'property' => $property, 'default' => $default, 'new' => $new] = $dataset;

            // Arrange: Create 2 RuleContext instances
            $context = Context::forRule()->with(ruleClass: DummyRule::class);
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
        ]);
    });

    // ------------------- Getters -------------------

    describe('Getters', function () {
        it("can get property 'ruleClass'", function () {
            // Arrange: Create RuleContext instance
            $context = Context::forRule()->with(ruleClass: DummyRule::class);

            // Assert: Property is correctly returned
            expect($context->getRuleClass())->toBe(DummyRule::class);
        });
    });

    // ------------------- Resolvers -------------------

    $context = Context::forRule()->with(
        ruleClass: DummyRule::class,
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

        it('can resolves "getRuleInstance"', function () use ($context) {
            // Act: Call resolver
            $actualPolicy = $context->getRuleInstance();

            // Assert: Ensure the rule instance is the expected one
            expect($actualPolicy)->toBeInstanceOf(DummyRule::class);
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
            // Arrange: Create RuleContext instance with mock
            $mock = Mockery::mock(DummyPolicy::class);
            $context = Context::forRule()->with(
                ruleClass: DummyRule::class,
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
 * Invalid scenarios for RuleContext class
 * ───────────────────────────────────────
 */
describe('Definitions - RuleContext : failure', function () {

    // ------------------- HasRuleContext -------------------

    describe('HasRuleContext', function () {
        it('throws exception with non-existing Rule class', function () {
            // Arrange: Create RuleContext
            /** @phpstan-ignore-next-line */
            $context = Context::forRule()->with('NonExistingRuleClass');

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getRuleInstance())
                ->toThrow(new SkippedTestSuiteError("Unable to find rule class : 'NonExistingRuleClass'."));
        });
    });
});
