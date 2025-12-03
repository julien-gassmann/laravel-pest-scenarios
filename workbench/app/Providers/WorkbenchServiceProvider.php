<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Jgss\LaravelPestScenarios\Console\Commands\MakeScenario;
use Workbench\App\Console\Commands\DummyCommand;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->commands([
            MakeScenario::class,
            DummyCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadTranslationsFrom(__DIR__.'/../../lang');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'workbench');
    }
}
