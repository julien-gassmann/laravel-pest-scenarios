<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Jgss\LaravelPestScenarios\Resolvers\Contexts\MockResolver;
use Mockery\MockInterface;

trait HasMockingContext
{
    // ------------------- With methods -------------------

    /**
     * @param  array<class-string, MockInterface>  $mocks
     */
    public function withMocks(array $mocks): self
    {
        return $this->replicate(mocks: $mocks);
    }

    // ------------------- Resolvers -------------------

    public function initMocks(): void
    {
        MockResolver::resolve($this->mocks);
    }
}
