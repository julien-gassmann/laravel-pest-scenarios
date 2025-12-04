<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Definitions\Contexts;

use Illuminate\Auth\Access\Response;
use Jgss\LaravelPestScenarios\Context;
use Mockery;
use PHPUnit\Framework\SkippedTestSuiteError;
use Workbench\App\Http\Requests\DummyRequest;
use Workbench\App\Models\User;
use Workbench\App\Policies\DummyPolicy;
use Workbench\App\Rules\DummyRule;

use function Jgss\LaravelPestScenarios\actor;
use function Jgss\LaravelPestScenarios\databaseSetup;
use function Jgss\LaravelPestScenarios\makeMock;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function PHPUnit\Framework\assertTrue;

/**
 * ───────────────────────────────────────
 * Valid scenarios for PolicyContext class
 * ───────────────────────────────────────
 */
describe('Definitions - PolicyContext : success', function (): void {

    // ------------------- With methods -------------------

    describe('With methods', function (): void {
        it('can replicate', function (array $dataset): void {
            // Arrange: Get dataset infos
            /** @var string $property */
            ['method' => $method, 'property' => $property, 'default' => $default, 'new' => $new] = $dataset;

            // Arrange: Create 2 PolicyContext instances
            $context = Context::forPolicy()->with(policyClass: DummyPolicy::class);
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
            'withMocks' => [[
                'method' => 'withMocks',
                'property' => 'mocks',
                'default' => [],
                'new' => makeMock(DummyRule::class, fn ($mock) => $mock),
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
        it("can get property 'policyClass'", function (): void {
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

        it('can resolves "getPolicyInstance"', function () use ($context): void {
            // Act: Call resolver
            $actualPolicy = $context->getPolicyInstance();

            // Assert: Ensure the policy instance is the expected one
            expect($actualPolicy)->toBeInstanceOf(DummyPolicy::class);
        });

        it('can resolves "getPolicyResponse"', function () use ($context): void {
            // Act: Call resolver
            $actualResponse = $context->getPolicyResponse(
                method: 'methodWithoutParameter',
                parameters: fn (): array => [],
            );

            // Assert: Ensure response is the expected one
            expect($actualResponse)->toBeFalse();
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
describe('Definitions - PolicyContext : failure', function (): void {

    // ------------------- HasPolicyContext -------------------

    describe('HasPolicyContext', function (): void {
        it('throws exception with non-existing Policy class', function (): void {
            // Arrange: Create PolicyContext
            /** @phpstan-ignore-next-line */
            $context = Context::forPolicy()->with('NonExistingPolicyClass');

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn (): object => $context->getPolicyInstance())
                ->toThrow(new SkippedTestSuiteError("Unable to find policy class : 'NonExistingPolicyClass'."));
        });

        it('throws exception with non-existing Policy method', function (): void {
            // Arrange: Create PolicyContext
            $context = Context::forPolicy()->with(DummyPolicy::class);

            // Assert: Ensure correct SkippedTestSuiteError is thrown
            expect(fn (): Response|bool|null => $context->getPolicyResponse('nonExistingMethod', fn (): array => []))
                ->toThrow(new SkippedTestSuiteError("Unable to find method 'nonExistingMethod' in 'DummyPolicy' class"));
        });
    });
});
