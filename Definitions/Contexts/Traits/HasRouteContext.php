<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Routing\Route;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\RouteResolver;
use PHPUnit\Framework\SkippedTestSuiteError;

trait HasRouteContext
{
    // ------------------- With methods -------------------

    public function withRouteName(string $routeName): self
    {
        return $this->replicate(routeName: $routeName);
    }

    /**
     * @param  array<string, int|string|callable(): (int|string|null)>  $routeParameters
     */
    public function withRouteParameters(array $routeParameters): self
    {
        return $this->replicate(routeParameters: $routeParameters);
    }

    // ------------------- Getters -------------------

    public function getRouteName(): string
    {
        return $this->routeName
            ?? throw new SkippedTestSuiteError('Route name is missing in HTTP context definition');
    }

    // ------------------- Resolvers -------------------

    public function getRouteInstance(): Route
    {
        return RouteResolver::resolve($this->getRouteName());
    }

    /**
     * @return array<string, string>
     */
    public function getRouteParameters(): array
    {
        return RouteResolver::resolveParameters($this->routeParameters);
    }
}
