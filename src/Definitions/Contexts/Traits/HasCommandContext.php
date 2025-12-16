<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Jgss\LaravelPestScenarios\Exceptions\MissingDefinitionException;
use Throwable;

trait HasCommandContext
{
    // ------------------- Getters -------------------

    /**
     * @throws Throwable
     */
    public function getCommand(): string
    {
        return $this->command ?? throw MissingDefinitionException::commandSignature();
    }
}
