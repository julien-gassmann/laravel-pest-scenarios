<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Routing\Route;
use Jgss\LaravelPestScenarios\Exceptions\MissingDefinitionException;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\RouteResolver;
use Throwable;

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

    /**
     * @param  array<string, int|string|callable(): (int|string|null)>  $routeParameters
     */
    public function withRoute(string $routeName, array $routeParameters): self
    {
        return $this->replicate(routeName: $routeName, routeParameters: $routeParameters);
    }

    // ------------------- Getters -------------------

    /**
     * @throws Throwable
     */
    public function getRouteName(): string
    {
        return $this->routeName ?? throw MissingDefinitionException::routeName();
    }

    // ------------------- Resolvers -------------------

    /**
     * @throws Throwable
     */
    public function getRouteInstance(): Route
    {
        return RouteResolver::resolve($this->getRouteName());
    }

    /**
     * @return array<string, string>
     *
     * @throws Throwable
     */
    public function getRouteParameters(): array
    {
        return RouteResolver::resolveParameters($this->routeParameters);
    }
}
