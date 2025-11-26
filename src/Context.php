<?php

namespace Jgss\LaravelPestScenarios;

use Jgss\LaravelPestScenarios\Builders\Contexts\ApiRouteContextBuilder;
use Jgss\LaravelPestScenarios\Builders\Contexts\CommandContextBuilder;
use Jgss\LaravelPestScenarios\Builders\Contexts\FormRequestContextBuilder;
use Jgss\LaravelPestScenarios\Builders\Contexts\ModelContextBuilder;
use Jgss\LaravelPestScenarios\Builders\Contexts\PolicyContextBuilder;
use Jgss\LaravelPestScenarios\Builders\Contexts\RuleContextBuilder;
use Jgss\LaravelPestScenarios\Builders\Contexts\WebRouteContextBuilder;

final readonly class Context
{
    // -------------------------- Feature --------------------------
    public static function forApiRoute(): ApiRouteContextBuilder
    {
        return new ApiRouteContextBuilder;
    }

    public static function forWebRoute(): WebRouteContextBuilder
    {
        return new WebRouteContextBuilder;
    }

    // -------------------------- Unit --------------------------
    public static function forCommand(): CommandContextBuilder
    {
        return new CommandContextBuilder;
    }

    public static function forModel(): ModelContextBuilder
    {
        return new ModelContextBuilder;
    }

    public static function forFormRequest(): FormRequestContextBuilder
    {
        return new FormRequestContextBuilder;
    }

    public static function forRule(): RuleContextBuilder
    {
        return new RuleContextBuilder;
    }

    public static function forPolicy(): PolicyContextBuilder
    {
        return new PolicyContextBuilder;
    }
}
