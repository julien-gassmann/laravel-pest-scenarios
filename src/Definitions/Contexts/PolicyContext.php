<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasActingAsContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasAppLocaleContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasDatabaseSetupContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasMockingContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasPolicyContext;
use Mockery\MockInterface;

/**
 * Immutable representation of a Policy context used in scenario-based tests.
 * Encapsulates the policy class under test, the acting user, optional mocks, and the app locale.
 *
 * @property class-string $policyClass Specifies the fully qualified class name of the Policy under test
 * @property Closure(): ?User $actingAs Returns the user instance performing the action (e.g. fn() => User::first())
 * @property null|string $appLocale Specifies the app localisation used for the test.
 * @property Closure(): void $databaseSetup Returns the database insertions to perform before the test
 * @property array<class-string, Closure(): MockInterface> $mocks Provides classes mocked during the scenario (e.g. Filesystem::class => fn () => Mockery::mock(Filesystem::class))
 */
final readonly class PolicyContext
{
    use HasActingAsContext;
    use HasAppLocaleContext;
    use HasDatabaseSetupContext;
    use HasMockingContext;
    use HasPolicyContext;

    /**
     * @param  class-string  $policyClass
     * @param  Closure(): ?User  $actingAs
     * @param  Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function __construct(
        protected string $policyClass,
        protected Closure $actingAs,
        protected ?string $appLocale,
        protected Closure $databaseSetup,
        protected array $mocks,
    ) {}

    /**
     * @param  null|class-string  $policyClass
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  null|array<class-string, Closure(): MockInterface>  $mocks
     */
    private function replicate(
        ?string $policyClass = null,
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        ?array $mocks = null,
    ): self {
        return new self(
            policyClass: $policyClass ?? $this->policyClass,
            actingAs: $actingAs ?? $this->actingAs,
            appLocale: $appLocale ?? $this->appLocale,
            databaseSetup: $databaseSetup ?? $this->databaseSetup,
            mocks: $mocks ?? $this->mocks,
        );
    }
}
