<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasActingAsContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasAppLocaleContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasDatabaseSetupContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasFromRouteContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasMockingContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasRouteContext;
use Mockery\MockInterface;

/**
 * Immutable definition of a web route context used in scenario-based tests.
 * Represents the HTTP testing environment, including the route configuration,
 * request parameters, acting user, locale, and optional mocked dependencies.
 *
 * @property string $routeName Specifies the route name used in the scenario
 * @property array<string, int|string|callable(): (int|string|null)> $routeParameters Provides the route parameters, keyed by the parameter names (e.g. ['user' => 1])
 * @property string $fromRouteName Specifies the source route name used when simulating a redirect or request origin in the scenario
 * @property array<string, int|string|callable(): (int|string|null)> $fromRouteParameters Provides the parameters for the source route, keyed by the parameter names (e.g. ['user' => 1])
 * @property Closure(): ?User $actingAs Returns the user instance performing the request (e.g. fn() => User::first())
 * @property null|string $appLocale Specifies the app localisation used for the test.
 * @property Closure(): void $databaseSetup Returns the database insertions to perform before the test
 * @property array<class-string, Closure(): MockInterface> $mocks Provides classes mocked during the scenario (e.g. Filesystem::class => fn () => Mockery::mock(Filesystem::class))
 */
final readonly class WebRouteContext
{
    use HasActingAsContext;
    use HasAppLocaleContext;
    use HasDatabaseSetupContext;
    use HasFromRouteContext;
    use HasMockingContext;
    use HasRouteContext;

    /**
     * @param  array<string, int|string|callable(): (int|string|null)>  $routeParameters
     * @param  array<string, int|string|callable(): (int|string|null)>  $fromRouteParameters
     * @param  Closure(): ?User  $actingAs
     * @param  Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function __construct(
        protected string $routeName,
        protected array $routeParameters,
        protected string $fromRouteName,
        protected array $fromRouteParameters,
        protected Closure $actingAs,
        protected ?string $appLocale,
        protected Closure $databaseSetup,
        protected array $mocks,
    ) {}

    /**
     * @param  null|array<string, int|string|callable(): (int|string|null)>  $routeParameters
     * @param  null|array<string, int|string|callable(): (int|string|null)>  $fromRouteParameters
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  null|array<class-string, Closure(): MockInterface>  $mocks
     */
    private function replicate(
        ?string $routeName = null,
        ?array $routeParameters = null,
        ?string $fromRouteName = null,
        ?array $fromRouteParameters = null,
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        ?array $mocks = null,
    ): self {
        return new self(
            routeName: $routeName ?? $this->routeName,
            routeParameters: $routeParameters ?? $this->routeParameters,
            fromRouteName: $fromRouteName ?? $this->fromRouteName,
            fromRouteParameters: $fromRouteParameters ?? $this->fromRouteParameters,
            actingAs: $actingAs ?? $this->actingAs,
            appLocale: $appLocale ?? $this->appLocale,
            databaseSetup: $databaseSetup ?? $this->databaseSetup,
            mocks: $mocks ?? $this->mocks,
        );
    }
}
