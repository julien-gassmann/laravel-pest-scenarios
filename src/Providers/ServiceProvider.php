<?php

namespace Jgss\LaravelPestScenarios\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Jgss\LaravelPestScenarios\Console\Commands\MakeScenario;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->commands([
            MakeScenario::class,
        ]);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/pest-scenarios.php' => config_path('pest-scenarios.php'),
        ], 'pest-scenarios');
    }
}
