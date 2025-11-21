<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

final readonly class AppLocaleResolver
{
    public static function resolve(?string $locale = null): void
    {
        $locale ??= app()->getLocale();

        app()->setLocale($locale);
    }
}
