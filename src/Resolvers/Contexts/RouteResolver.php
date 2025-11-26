<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use PHPUnit\Framework\SkippedTestSuiteError;

final readonly class RouteResolver
{
    /**
     * Resolves the route instance using its name.
     *
     * @throws SkippedTestSuiteError if the route cannot be resolved
     */
    public static function resolve(string $routeName): Route
    {
        return RouteFacade::getRoutes()->getByName($routeName)
            ?? throw new SkippedTestSuiteError("Unable to find route: '$routeName'.");
    }

    /**
     * Resolves the route parameters values passed has callable.
     *
     * @param  array<string, int|string|callable(): (int|string|null)>  $parameters
     * @return array<string, string>
     */
    public static function resolveParameters(array $parameters): array
    {
        return array_map(
            fn ($value) => match (true) {
                is_callable($value) && is_scalar($value()) => (string) $value(),
                is_scalar($value) => (string) $value,
                default => throw new SkippedTestSuiteError('Unable to cast route parameters as string.'),
            },
            $parameters
        );
    }
}
