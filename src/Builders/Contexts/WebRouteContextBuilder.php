<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\WebRouteContext;
use Mockery\MockInterface;

final readonly class WebRouteContextBuilder
{
    /**
     * @param  array<string, int|string|Closure(): (int|string|null)>  $routeParameters
     * @param  array<string, int|string|Closure(): (int|string|null)>  $fromRouteParameters
     * @param  null|Closure(): ?User  $actingAs
     * @param  null|Closure(): void  $databaseSetup
     * @param  array<class-string, MockInterface>  $mocks
     */
    public function with(
        string $routeName,
        array $routeParameters = [],
        ?string $fromRouteName = null,
        ?array $fromRouteParameters = null,
        ?Closure $actingAs = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        array $mocks = [],
    ): WebRouteContext {
        return new WebRouteContext(
            routeName: $routeName,
            routeParameters: $routeParameters,
            fromRouteName: $fromRouteName ?? $routeName,
            fromRouteParameters: $fromRouteParameters ?? $routeParameters,
            actingAs: $actingAs ?? fn () => null,
            appLocale: $appLocale,
            databaseSetup: $databaseSetup ?? fn () => null,
            mocks: $mocks,
        );
    }
}
