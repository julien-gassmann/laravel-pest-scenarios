<?php

declare(strict_types=1);

namespace Jgss\LaravelPestScenarios\Exceptions\Traits;

use PHPUnit\Framework\SkippedTestSuiteError;
use Throwable;

trait SkipOrFail
{
    private static function skipOrFail(string $message): Throwable
    {
        $isStrictModeEnabled = config('pest-scenarios.strict_mode.'.self::$key, true);

        if ($isStrictModeEnabled) {
            return new self($message);
        }

        return new SkippedTestSuiteError($message);
    }
}
