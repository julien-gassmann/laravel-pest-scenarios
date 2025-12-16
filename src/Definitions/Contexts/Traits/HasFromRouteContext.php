<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Routing\Route;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\RouteResolver;
use Throwable;

trait HasFromRouteContext
{
    // ------------------- With methods -------------------

    public function withFromRouteName(string $fromRouteName): self
    {
        return $this->replicate(fromRouteName: $fromRouteName);
    }

    /**
     * @param  array<string, int|string|callable(): (int|string|null)>  $fromRouteParameters
     */
    public function withFromRouteParameters(array $fromRouteParameters): self
    {
        return $this->replicate(fromRouteParameters: $fromRouteParameters);
    }

    /**
     * @param  array<string, int|string|callable(): (int|string|null)>  $fromRouteParameters
     */
    public function withFromRoute(string $fromRouteName, array $fromRouteParameters): self
    {
        return $this->replicate(fromRouteName: $fromRouteName, fromRouteParameters: $fromRouteParameters);
    }

    // ------------------- Getters -------------------

    public function getFromRouteName(): string
    {
        return $this->fromRouteName ?? $this->routeName;
    }

    // ------------------- Resolvers -------------------

    /**
     * @throws Throwable
     */
    public function getFromRouteInstance(): Route
    {
        return RouteResolver::resolve($this->fromRouteName);
    }

    /**
     * @return array<string, string>
     *
     * @throws Throwable
     */
    public function getFromRouteParameters(): array
    {
        return RouteResolver::resolveParameters($this->fromRouteParameters);
    }
}
