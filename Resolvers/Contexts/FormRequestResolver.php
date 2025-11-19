<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use PHPUnit\Framework\SkippedTestSuiteError;
use ReflectionNamedType;
use ReflectionParameter;

abstract class FormRequestResolver
{
    /**
     * Resolves the form request instance using its name.
     * Inject route instance and payload if provided.
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws SkippedTestSuiteError if the form request cannot be resolved
     */
    public static function resolve(string $formRequestClass, ?Route $route = null, ?array $payload = null): FormRequest
    {
        if (! class_exists($formRequestClass)) {
            throw new SkippedTestSuiteError("Unable to find form request class : '$formRequestClass'.");
        }

        if (! is_subclass_of($formRequestClass, FormRequest::class)) {
            throw new SkippedTestSuiteError("Provided class '$formRequestClass' doesn't extend FormRequest.");
        }

        if ($route && $payload) {
            /** @var string $routeMethod */
            $routeMethod = $route->methods[0];

            return $formRequestClass::create($route->uri, $routeMethod, $payload);
        }

        return new $formRequestClass;
    }

    /**
     * Resolves a fully functional HTTP context (request + route) for FormRequests unit testing.
     * Binds route parameters, and simulates authentication through a user resolver.
     *
     * Designed to make unit tests more realistic and expressive by mimicking real route execution.
     *
     * @param  array<string, string>  $routeParameters
     * @param  array<string, mixed>  $payload
     * @param  Closure(): ?User  $actingAs
     * @param  class-string<FormRequest>  $formRequestClass
     */
    public static function resolveWithBindings(string $routeName, array $routeParameters, array $payload, Closure $actingAs, string $formRequestClass): FormRequest
    {
        // Get Route and FormRequest instances
        $route = RouteResolver::resolve($routeName);
        $request = self::resolve($formRequestClass, $route, $payload);

        // Bind request and parameters
        $route->bind($request);
        self::bindRouteParameters($route, $routeParameters);

        // Set resolvers on the request
        $request->setRouteResolver(fn () => $route);
        $request->setUserResolver($actingAs);

        return $request;
    }

    /**
     * Resolves and binds route parameters declared in the route signature.
     *
     * For each parameter listed in the method signature,
     * if its name matches a registered route parameter, we compute its value and bind it to the route.
     *
     * @param  array<string, string>  $parameters
     */
    private static function bindRouteParameters(Route $route, array $parameters): void
    {
        /** @var ReflectionParameter[] $signatureParameters */
        $signatureParameters = $route->signatureParameters();

        foreach ($signatureParameters as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $parameters)) {
                $resolvedValue = self::resolveBindRouteParameter($route, $parameter, $parameters[$name]);
                $route->setParameter($name, $resolvedValue);
            }
        }
    }

    /**
     * Resolves a bind route parameter into its runtime value.
     *
     * This simulates Laravel’s route-model binding logic for testing:
     * - Scalars (int, string) → cast to string.
     * - Eloquent models → resolved via `Model::where(...)`.
     * - Other objects → instantiated using the raw value.
     *
     * @throws SkippedTestSuiteError if a model cannot be resolved
     */
    private static function resolveBindRouteParameter(Route $route, ReflectionParameter $parameter, string $value): object|string
    {
        /** @var ReflectionNamedType $type */
        $type = $parameter->getType();
        $name = $parameter->getName();
        $className = $type->getName();

        if ($type->isBuiltIn()) {
            return $value;
        }

        if (is_subclass_of($className, Model::class)) {
            $field = $route->bindingFieldFor($name) ?? 'id';

            return (new $className)::query()->where($field, $value)->first()
                ?? throw new SkippedTestSuiteError("Unable to find model '$name.$field' with value '$value'.");
        }

        return new $className($value);
    }
}
