<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Jgss\LaravelPestScenarios\Exceptions\MissingDefinitionException;
use Jgss\LaravelPestScenarios\Resolvers\Contexts\FormRequestResolver;
use Throwable;

/**
 * Requires the HasRouteContext trait to be used in the same class.
 */
trait HasFormRequestContext
{
    // ------------------- Getters -------------------

    /**
     * @return class-string<FormRequest>
     *
     * @throws Throwable
     */
    public function getFormRequestClass(): string
    {
        return $this->formRequestClass ?? throw MissingDefinitionException::formRequestClass();
    }

    // ------------------- Resolvers -------------------

    /**
     * @throws Throwable
     */
    public function getFormRequestInstance(): FormRequest
    {
        return FormRequestResolver::resolve($this->getFormRequestClass());
    }

    /**
     * @throws Throwable
     */
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
