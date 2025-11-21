<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\FormRequestResolver;
use PHPUnit\Framework\SkippedTestSuiteError;

/**
 * Requires the HasRouteContext trait to be used in the same class.
 */
trait HasFormRequestContext
{
    // ------------------- Getters -------------------

    /**
     * @return class-string<FormRequest>
     */
    public function getFormRequestClass(): string
    {
        return $this->formRequestClass
            ?? throw new SkippedTestSuiteError('FormRequest class is missing in HTTP context definition');
    }

    // ------------------- Resolvers -------------------

    public function getFormRequestInstance(): FormRequest
    {
        return FormRequestResolver::resolve($this->getFormRequestClass());
    }

    public function getFormRequestInstanceWithBindings(): FormRequest
    {
        return FormRequestResolver::resolveWithBindings(
            routeName: $this->getRouteName(),
            routeParameters: $this->getRouteParameters(),
            payload: $this->payload,
            actingAs: $this->actingAs,
            formRequestClass: $this->getFormRequestClass()
        );
    }
}
