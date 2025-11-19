<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Jgss\LaravelPestScenarios\Resolvers\Contexts\AppLocaleResolver;

trait HasAppLocaleContext
{
    // ------------------- With methods -------------------

    public function withAppLocale(?string $locale): self
    {
        return $this->replicate(appLocale: $locale);
    }

    // ------------------- Resolvers -------------------

    public function localiseApp(): void
    {
        AppLocaleResolver::resolve($this->appLocale);
    }
}
