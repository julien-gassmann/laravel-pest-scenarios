<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\SkippedTestSuiteError;

abstract class PolicyResolver
{
    /**
     * @throws SkippedTestSuiteError if the policy cannot be found
     */
    public static function resolve(string $policyClass): object
    {
        if (! class_exists($policyClass)) {
            throw new SkippedTestSuiteError("Unable to find policy class : '$policyClass'.");
        }

        return new $policyClass;
    }

    /**
     * @param  callable(): array<int, mixed>  $parameters
     */
    public static function resolveResponse(object $policy, string $method, callable $parameters): Response|bool|null
    {
        if (! method_exists($policy, $method)) {
            $policyClass = class_basename($policy);
            throw new SkippedTestSuiteError("Unable to find method '$method' in '$policyClass' class");
        }

        /** @var Response|bool|null $result */
        $result = $policy->$method(Auth::user(), ...$parameters());

        return $result;
    }
}
