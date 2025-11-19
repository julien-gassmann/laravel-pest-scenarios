<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasActingAsContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasAppLocaleContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasDatabaseSetupContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasMockingContext;
use Mockery\MockInterface;

/**
 * Immutable definition of a Model testing context used in scenario-based tests.
 * Encapsulates the testing environment for Eloquent models, including mocking configuration,
 * authenticated user context, and localisation settings.
 *
 * @property Closure(): ?User $actingAs Returns the user instance performing the request (e.g. fn() => User::first())
 * @property string|null $appLocale Specifies the app localisation used for the test.
 * @property Closure(): void $databaseSetup Returns the database insertions to perform before the test
 * @property array<class-string, MockInterface> $mocks Provides classes mocked during the scenario (e.g. Filesystem::class => Mockery::mock(Filesystem::class))
 */
final readonly class ModelContext
{
    use HasActingAsContext;
    use HasAppLocaleContext;
    use HasDatabaseSetupContext;
    use HasMockingContext;

    /**
     * @param  Closure(): ?User  $actingAs
     * @param  Closure(): void  $databaseSetup
     * @param  array<class-string, MockInterface>  $mocks
     */
    public function __construct(
        protected Closure $actingAs,
        protected ?string $appLocale,
        protected Closure $databaseSetup,
        protected array $mocks,
    ) {}

    /**
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  null|array<class-string, MockInterface>  $mocks
     */
    protected function replicate(
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        ?array $mocks = null,
    ): self {
        return new self(
            actingAs: $actingAs ?? $this->actingAs,
            appLocale: $appLocale ?? $this->appLocale,
            databaseSetup: $databaseSetup ?? $this->databaseSetup,
            mocks: $mocks ?? $this->mocks,
        );
    }
}
