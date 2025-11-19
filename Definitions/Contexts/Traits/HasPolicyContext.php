<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Auth\Access\Response;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\PolicyResolver;
use PHPUnit\Framework\SkippedTestSuiteError;

trait HasPolicyContext
{
    // ------------------- Getters -------------------

    public function getPolicyClass(): string
    {
        return $this->policyClass
            ?? throw new SkippedTestSuiteError('Policy class is missing in HTTP context definition');
    }

    // ------------------- Resolvers -------------------

    public function getPolicyInstance(): object
    {
        return PolicyResolver::resolve($this->getPolicyClass());
    }

    /**
     * @param  callable(): array<int, mixed>  $parameters
     */
    public function getPolicyResponse(string $method, callable $parameters): Response|bool|null
    {
        return PolicyResolver::resolveResponse(
            $this->getPolicyInstance(),
            $method,
            $parameters
        );
    }
}
