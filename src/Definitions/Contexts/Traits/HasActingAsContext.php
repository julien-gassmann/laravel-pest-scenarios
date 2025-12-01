<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\ActingAsResolver;

trait HasActingAsContext
{
    // ------------------- With methods -------------------

    /**
     * @param  string|Closure(): ?User  $actingAs
     */
    public function withActingAs(string|Closure $actingAs): self
    {
        return $this->replicate(
            actingAs: ActingAsResolver::resolveInitialContext($actingAs)
        );
    }

    // ------------------- Resolvers -------------------

    public function actAs(): void
    {
        ActingAsResolver::resolve($this->actingAs);
    }
}
