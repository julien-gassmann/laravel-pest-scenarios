<?php

namespace Jgss\LaravelPestScenarios\Builders\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Definitions\Contexts\WebRouteContext;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\ActingAsResolver;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\DatabaseSetupResolver;
use Mockery\MockInterface;

final readonly class WebRouteContextBuilder
{
    /**
     * @param  array<string, int|string|Closure(): (int|string|null)>  $routeParameters
     * @param  array<string, int|string|Closure(): (int|string|null)>  $fromRouteParameters
     * @param  null|string|Closure(): ?User  $actingAs
     * @param  null|string|string[]|Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function with(
        string $routeName,
        array $routeParameters = [],
        ?string $fromRouteName = null,
        ?array $fromRouteParameters = null,
        null|string|Closure $actingAs = null,
        ?string $appLocale = null,
        null|string|array|Closure $databaseSetup = null,
        array $mocks = [],
    ): WebRouteContext {
        return new WebRouteContext(
            routeName: $routeName,
            routeParameters: $routeParameters,
            fromRouteName: $fromRouteName ?? $routeName,
            fromRouteParameters: $fromRouteParameters ?? $routeParameters,
            actingAs: ActingAsResolver::resolveInitialContext($actingAs),
            appLocale: $appLocale,
            databaseSetup: DatabaseSetupResolver::resolveInitialContext($databaseSetup),
            mocks: $mocks,
        );
    }
}
