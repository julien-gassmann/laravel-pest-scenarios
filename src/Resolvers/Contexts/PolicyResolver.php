<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
use Jgss\LaravelPestScenarios\Exceptions\ResolutionFailedException;

final readonly class PolicyResolver
{
    public static function resolve(string $policyClass): object
    {
        if (! class_exists($policyClass)) {
            throw ResolutionFailedException::policyClassNotFound($policyClass);
        }

        return new $policyClass;
    }

    /**
     * @param  callable(): array<int, mixed>  $parameters
     */
    public static function resolveResponse(object $policy, string $method, callable $parameters): Response|bool|null
    {
        if (! method_exists($policy, $method)) {
            throw ResolutionFailedException::policyMethodNotFound($policy::class, $method);
        }

        /** @var Response|bool|null $result */
        $result = $policy->$method(Auth::user(), ...$parameters());

        return $result;
    }
}
