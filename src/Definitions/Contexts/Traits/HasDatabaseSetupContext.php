<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Closure;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\DatabaseSetupResolver;

trait HasDatabaseSetupContext
{
    // ------------------- With methods -------------------

    /**
     * @param  null|string|string[]|Closure(): void  $databaseSetup
     */
    public function withDatabaseSetup(null|string|array|Closure $databaseSetup): self
    {
        return $this->replicate(
            databaseSetup: DatabaseSetupResolver::resolveInitialContext($databaseSetup),
        );
    }

    // ------------------- Resolvers -------------------

    public function setupDatabase(): void
    {
        DatabaseSetupResolver::resolve($this->databaseSetup);
    }
}
