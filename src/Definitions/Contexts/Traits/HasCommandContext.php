<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Jgss\LaravelPestScenarios\Exceptions\MissingDefinitionException;

trait HasCommandContext
{
    // ------------------- Getters -------------------

    public function getCommand(): string
    {
        return $this->command ?? throw MissingDefinitionException::commandSignature();
    }
}
