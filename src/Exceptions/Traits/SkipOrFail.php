<?php

declare(strict_types=1);

namespace Jgss\LaravelPestScenarios\Exceptions\Traits;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\SkippedTestSuiteError;

trait SkipOrFail
{
    private static function skipOrFail(string $message): AssertionFailedError
    {
        $isStrictModeEnabled = config('pest-scenarios.strict_mode.'.self::$key, true);

        if ($isStrictModeEnabled) {
            return new self($message);
        }

        return new SkippedTestSuiteError($message);
    }
}
