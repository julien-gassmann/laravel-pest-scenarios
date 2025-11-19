<?php

namespace Jgss\LaravelPestScenarios;

use Jgss\LaravelPestScenarios\Builders\Scenarios\ApiRouteScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\CommandScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\FormRequestScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\ModelScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\PolicyScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\RuleScenarioBuilder;
use Jgss\LaravelPestScenarios\Builders\Scenarios\WebRouteScenarioBuilder;

final readonly class Scenario
{
    // -------------------------- Feature --------------------------
    public static function forApiRoute(): ApiRouteScenarioBuilder
    {
        return new ApiRouteScenarioBuilder;
    }

    public static function forWebRoute(): WebRouteScenarioBuilder
    {
        return new WebRouteScenarioBuilder;
    }

    // -------------------------- Unit --------------------------
    public static function forCommand(): CommandScenarioBuilder
    {
        return new CommandScenarioBuilder;
    }

    public static function forFormRequest(): FormRequestScenarioBuilder
    {
        return new FormRequestScenarioBuilder;
    }

    public static function forModel(): ModelScenarioBuilder
    {
        return new ModelScenarioBuilder;
    }

    public static function forRule(): RuleScenarioBuilder
    {
        return new RuleScenarioBuilder;
    }

    public static function forPolicy(): PolicyScenarioBuilder
    {
        return new PolicyScenarioBuilder;
    }
}
