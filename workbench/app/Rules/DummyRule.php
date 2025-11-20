<?php

namespace Jgss\LaravelPestScenarios\workbench\app\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DummyRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== 'valid') {
            $fail('dummy error message');
        }
    }
}
