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
 * Valid scenarios for PolicyContext class
 * ───────────────────────────────────────
 */
describe('Definitions - PolicyContext : success', function () {

    // ------------------- With methods -------------------

    describe('With methods', function () {
        it('can replicate', function (array $dataset) {
            // Arrange: Get dataset infos
            /** @var string $property */
            ['method' => $method, 'property' => $property, 'default' => $default, 'new' => $new] = $dataset;

            // Arrange: Create 2 PolicyContext instances
            $context = Context::forPolicy()->with(policyClass: DummyPolicy::class);
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
        ]);
    });

    // ------------------- Getters -------------------

    describe('Getters', function () {
        it("can get property 'policyClass'", function () {
            // Arrange: Create PolicyContext instance
            $context = Context::forPolicy()->with(policyClass: DummyPolicy::class);

            // Assert: Property is correctly returned
            expect($context->getPolicyClass())->toBe(DummyPolicy::class);
        });
    });

    // ------------------- Resolvers -------------------

    $context = Context::forPolicy()->with(
        policyClass: DummyPolicy::class,
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

        it('can resolves "getPolicyInstance"', function () use ($context) {
            // Act: Call resolver
            $actualPolicy = $context->getPolicyInstance();

            // Assert: Ensure the policy instance is the expected one
            expect($actualPolicy)->toBeInstanceOf(DummyPolicy::class);
        });

        it('can resolves "getPolicyResponse"', function () use ($context) {
            // Act: Call resolver
            $actualResponse = $context->getPolicyResponse(
                method: 'methodWithoutParameter',
                parameters: fn () => [],
            );

            // Assert: Ensure response is the expected one
            expect($actualResponse)->toBeFalse();
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
            // Arrange: Create PolicyContext instance with mock
            $mock = Mockery::mock(DummyRule::class);
            $context = Context::forPolicy()->with(
                policyClass: DummyPolicy::class,
                mocks: [DummyRule::class => fn () => $mock],
            );

            // Act: Call resolver
            $context->initMocks();

            // Assert: Ensure DummyRule is mocked
            expect(app(DummyRule::class))->toBe($mock);
        });
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for PolicyContext class
 * ───────────────────────────────────────
 */
describe('Definitions - PolicyContext : failure', function () {

    // ------------------- HasPolicyContext -------------------

    describe('HasPolicyContext', function () {
        it('throws exception with non-existing Policy class', function () {
            // Arrange: Create PolicyContext
            /** @phpstan-ignore-next-line */
            $context = Context::forPolicy()->with('NonExistingPolicyClass');

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getPolicyInstance())
                ->toThrow(new SkippedTestSuiteError("Unable to find policy class : 'NonExistingPolicyClass'."));
        });

        it('throws exception with non-existing Policy method', function () {
            // Arrange: Create PolicyContext
            $context = Context::forPolicy()->with(DummyPolicy::class);

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn () => $context->getPolicyResponse('nonExistingMethod', fn () => []))
                ->toThrow(new SkippedTestSuiteError("Unable to find method 'nonExistingMethod' in 'DummyPolicy' class"));
        });
    });
});
