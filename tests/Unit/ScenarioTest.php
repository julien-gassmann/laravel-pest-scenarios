<?php

namespace Jgss\LaravelPestScenarios\Tests\Unit\Scenarios;

use Jgss\LaravelPestScenarios\Builders\Scenarios\ApiRouteScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\CommandScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\FormRequestScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\ModelScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\PolicyScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\RuleScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\WebRouteScenarioBuilder;
use Jgss\LaravelPestScenarios\Scenario;

/**
 * ───────────────────────────────────────
 * Valid scenarios for Scenario class
 * ───────────────────────────────────────
 */
describe('Scenario : success', function () {
    it('returns expected builder instance', function (array $dataset) {
        // Arrange: Get dataset infos
        /** @var class-string $builder */
        ['method' => $method, 'builder' => $builder] = $dataset;

        // Assert: Scenario method returns expected builder
        expect(Scenario::$method())->toBeInstanceOf($builder);
    })->with([
        'ApiRoute' => [[
            'method' => 'forApiRoute',
            'builder' => ApiRouteScenarioBuilder::class,
        ]],
        'WebRoute' => [[
            'method' => 'forWebRoute',
            'builder' => WebRouteScenarioBuilder::class,
        ]],
        'Command' => [[
            'method' => 'forCommand',
            'builder' => CommandScenarioBuilder::class,
        ]],
        'FormRequest' => [[
            'method' => 'forFormRequest',
            'builder' => FormRequestScenarioBuilder::class,
        ]],
        'Model' => [[
            'method' => 'forModel',
            'builder' => ModelScenarioBuilder::class,
        ]],
        'Rule' => [[
            'method' => 'forRule',
            'builder' => RuleScenarioBuilder::class,
        ]],
        'Policy' => [[
            'method' => 'forPolicy',
            'builder' => PolicyScenarioBuilder::class,
        ]],
    ]);
});
