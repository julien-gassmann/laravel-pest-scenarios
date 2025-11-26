<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Closure;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\DatabaseSetupResolver;

trait HasDatabaseSetupContext
{
    // ------------------- With methods -------------------

    /**
     * @param  Closure(): void  $databaseSetup
     */
    public function withDatabaseSetup(Closure $databaseSetup): self
    {
        return $this->replicate(databaseSetup: $databaseSetup);
    }

    // ------------------- Resolvers -------------------

    public function setupDatabase(): void
    {
        DatabaseSetupResolver::resolve($this->databaseSetup);
    }
}
