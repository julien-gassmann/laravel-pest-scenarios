<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use PHPUnit\Framework\SkippedTestSuiteError;

trait HasCommandContext
{
    // ------------------- Getters -------------------

    public function getCommand(): string
    {
        return $this->command
            ?? throw new SkippedTestSuiteError('Artisan command class is missing in context definition');
    }
}
