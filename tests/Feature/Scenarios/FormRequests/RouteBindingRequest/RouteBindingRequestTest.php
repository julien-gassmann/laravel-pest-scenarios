<?php

use Jgss\LaravelPestScenarios\Context;
use Jgss\LaravelPestScenarios\Scenario;
use Workbench\App\Http\Requests\RouteBindingRequest;

use function Jgss\LaravelPestScenarios\getQueryId;

$context = Context::forFormRequest()->with(
    formRequestClass: RouteBindingRequest::class,
    databaseSetup: ['create_dummy', 'create_dummy_child'],
);

/**
 * ───────────────────────────────────────
 * Valid scenarios for RouteBindingRequest
 * ───────────────────────────────────────
 */
describe('FormRequests : success', function () use ($context): void {
    describe('RouteBindingRequest - multiple binding', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: 'can resolves multiple route parameter bindings',
            context: $context
                ->withRoute(
                    routeName: 'api.multiple.bindings',
                    routeParameters: [
                        'dummy' => getQueryId('dummy_first'),
                        'dummyChild' => getQueryId('dummy_child_first'),
                    ])
        );
    });

    describe('RouteBindingRequest - model column binding', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: 'can resolves route parameter with specific model column binding',
            context: $context
                ->withRoute(
                    routeName: 'api.model.column.binding',
                    routeParameters: [
                        'dummy' => fn () => queryDummy('dummy_first')->email,
                    ])
        );
    });

    describe('RouteBindingRequest - built in binding', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: 'can resolves route parameter with built in value binding',
            context: $context
                ->withRoute(
                    routeName: 'api.built.in.binding',
                    routeParameters: ['int' => 10]),
        );
    });

    describe('RouteBindingRequest - class binding', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: 'can resolves route parameter with class binding',
            context: $context
                ->withRoute(
                    routeName: 'api.class.binding',
                    routeParameters: ['class' => 'bind dummy service']),
        );
    });

    describe('RouteBindingRequest - enum binding', function () use ($context): void {
        Scenario::forFormRequest()->valid(
            description: 'can resolves route parameter with enum binding',
            context: $context
                ->withRoute(
                    routeName: 'api.enum.binding',
                    routeParameters: ['enum' => 'pending']),
        );
    });
});

/**
 * ───────────────────────────────────────
 * Invalid scenarios for RouteBindingRequest
 * ───────────────────────────────────────
 */
// describe('FormRequests : failure', function () use ($context) {
//    //
// });
