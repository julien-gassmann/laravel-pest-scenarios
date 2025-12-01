<?php

namespace Jgss\LaravelPestScenarios\Tests;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Workbench\App\Exceptions\WorkbenchExceptionHandler;
use Workbench\App\Models\Dummy;
use Workbench\App\Models\DummyChild;
use Workbench\App\Models\User;
use Workbench\App\Providers\WorkbenchServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            WorkbenchServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup ExceptionHandler
        $app->singleton(ExceptionHandler::class, WorkbenchExceptionHandler::class);

        // Setup database config
        config()->set('database.default', 'testing');

        // Setup locale config
        config()->set('app.locale', 'en');

        // Setup pest-scenarios config
        config()->set('pest-scenarios', [
            'resolvers' => [
                'actors' => [
                    'user' => fn () => User::query()->firstOrFail(),
                    'unauthorized' => fn () => User::query()->where('name', 'Unauthorized')->firstOrFail(),
                    'guest' => fn () => null,
                ],
                'database_setups' => [
                    'create_user' => fn () => User::factory()->create(),
                    'create_unauthorized_user' => fn () => User::factory()->create(['name' => 'Unauthorized']),
                    'create_dummy' => fn () => Dummy::factory()->create(['email' => 'dummy@email.com']),
                    'create_dummies' => fn () => Dummy::factory(10)->create(),
                    'create_dummy_with_children' => fn () => Dummy::factory()->has(DummyChild::factory(rand(1, 5)), 'children')->create(),
                    'create_dummy_child' => fn () => DummyChild::factory()->create(),
                ],
                'json_structures' => [
                    'resource' => ['data'],
                    'pagination' => [
                        'data',
                        'links' => ['first', 'last', 'prev', 'next'],
                        'meta' => [
                            'current_page', 'from', 'last_page',
                            'links' => ['*' => ['url', 'label', 'active']],
                        ],
                    ],
                    'token' => ['token'],
                    'message' => ['message'],
                    'none' => null,
                ],
                'queries' => [
                    'dummy_first' => fn () => Dummy::query()->firstOrFail(),
                    'dummy_last' => fn () => Dummy::query()->orderByDesc('id')->firstOrFail(),
                    'dummy_child_first' => fn () => DummyChild::query()->firstOrFail(),
                    // Helpers unit test queries :
                    'int' => fn () => queryDummy('dummy_first')->id,
                    'string' => fn () => queryDummy('dummy_first')->name,
                    'bool' => fn () => queryDummy('dummy_first')->is_active,
                    'model' => fn () => queryDummy('dummy_first'),
                    'collection' => fn () => Dummy::all(),
                    'id' => fn () => queryDummy('dummy_first'),
                ],
            ],
        ]);
    }
}
