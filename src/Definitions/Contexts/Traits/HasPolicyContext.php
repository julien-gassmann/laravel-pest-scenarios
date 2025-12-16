<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Auth\Access\Response;
use Jgss\LaravelPestScenarios\Exceptions\MissingDefinitionException;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\PolicyResolver;
use Throwable;

trait HasPolicyContext
{
    // ------------------- Getters -------------------

    /**
     * @throws Throwable
     */
    public function getPolicyClass(): string
    {
        return $this->policyClass ?? throw MissingDefinitionException::policyClass();
    }

    // ------------------- Resolvers -------------------

    /**
     * @throws Throwable
     */
    public function getPolicyInstance(): object
    {
        return PolicyResolver::resolve($this->getPolicyClass());
    }

    /**
     * @param  callable(): array<int, mixed>  $parameters
     *
     * @throws Throwable
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
