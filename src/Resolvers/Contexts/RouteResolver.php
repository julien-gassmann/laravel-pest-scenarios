<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Jgss\LaravelPestScenarios\Exceptions\ResolutionFailedException;
use Throwable;

final readonly class RouteResolver
{
    /**
     * Resolves the route instance using its name.
     *
     * @throws Throwable
     */
    public static function resolve(string $routeName): Route
    {
        return RouteFacade::getRoutes()->getByName($routeName)
            ?? throw ResolutionFailedException::routeNameNotFound($routeName);
    }

    /**
     * Resolves the route parameters values passed has callable.
     *
     * @param  array<string, int|string|callable(): (int|string|null)>  $parameters
     * @return array<string, string>
     *
     * @throws Throwable
     */
    public static function resolveParameters(array $parameters): array
    {
        return array_map(
            fn (callable|int|string $value): string => match (true) {
                is_callable($value) && is_scalar($value()) => (string) $value(),
                is_scalar($value) => (string) $value,
                default => throw ResolutionFailedException::routeParametersCasting()
            },
            $parameters
        );
    }
}
