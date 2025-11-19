<?php

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits;

trait PrepareContext
{
    /**
     * Perform all Context related setups.
     */
    public function prepareContext(): void
    {
        $this->context->setupDatabase();
        $this->context->initMocks();
        $this->context->localiseApp();
        $this->context->actAs();
    }
}
