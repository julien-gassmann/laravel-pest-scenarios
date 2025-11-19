<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasActingAsContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasAppLocaleContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasDatabaseSetupContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasMockingContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasRouteContext;
use Mockery\MockInterface;

/**
 * Immutable definition of an API route context used in scenario-based tests.
 * Represents the HTTP testing environment, including the route configuration,
 * request parameters, acting user, locale, and optional mocked dependencies.
 *
 * @property string|null $routeName Specifies the route name used in the scenario
 * @property array<string, int|string|callable(): (int|string|null)> $routeParameters Provides the route parameters, keyed by the parameter names (e.g. ['user' => 1])
 * @property Closure(): ?User $actingAs Returns the user instance performing the request (e.g. fn() => User::first())
 * @property null|string $appLocale Specifies the app localisation used for the test
 * @property Closure(): void $databaseSetup Returns the database insertions to perform before the test
 * @property array<class-string, MockInterface> $mocks Provides classes mocked during the scenario (e.g. Filesystem::class => Mockery::mock(Filesystem::class))
 */
final readonly class ApiRouteContext
{
    use HasActingAsContext;
    use HasAppLocaleContext;
    use HasDatabaseSetupContext;
    use HasMockingContext;
    use HasRouteContext;

    /**
     * @param  array<string, int|string|callable(): (int|string|null)>  $routeParameters
     * @param  Closure(): ?User  $actingAs
     * @param  Closure(): void  $databaseSetup
     * @param  array<class-string, MockInterface>  $mocks
     */
    public function __construct(
        protected string $routeName,
        protected array $routeParameters,
        protected Closure $actingAs,
        protected ?string $appLocale,
        protected Closure $databaseSetup,
        protected array $mocks,
    ) {}

    /**
     * @param  null|array<string, int|string|callable(): (int|string|null)>  $routeParameters
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  null|array<class-string, MockInterface>  $mocks
     */
    protected function replicate(
        ?string $routeName = null,
        ?array $routeParameters = null,
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        ?array $mocks = null,
    ): self {
        return new self(
            routeName: $routeName ?? $this->routeName,
            routeParameters: $routeParameters ?? $this->routeParameters,
            actingAs: $actingAs ?? $this->actingAs,
            appLocale: $appLocale ?? $this->appLocale,
            databaseSetup: $databaseSetup ?? $this->databaseSetup,
            mocks: $mocks ?? $this->mocks,
        );
    }
}
