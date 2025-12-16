<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
use Jgss\LaravelPestScenarios\Exceptions\ResolutionFailedException;
use Throwable;

final readonly class PolicyResolver
{
    /**
     * @throws Throwable
     */
    public static function resolve(string $policyClass): object
    {
        if (! class_exists($policyClass)) {
            throw ResolutionFailedException::policyClassNotFound($policyClass);
        }

        return new $policyClass;
    }

    /**
     * @param  callable(): array<int, mixed>  $parameters
     *
     * @throws Throwable
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
