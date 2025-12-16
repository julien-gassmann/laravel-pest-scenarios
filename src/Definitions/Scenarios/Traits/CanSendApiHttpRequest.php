<?php

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits;

use Illuminate\Routing\Route;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

trait CanSendApiHttpRequest
{
    /**
     * Sends an HTTP request to the route defined in the scenario using the appropriate method.
     * The method (GET, POST, PATCH, PUT, DELETE) is automatically determined from the route definition.
     * It also handles provided route parameters.
     *
     * @return TestResponse<Response>
     *
     * @throws Throwable
     */
    public function sendRequest(): TestResponse
    {
        $payload = self::resolvePayload($this->payload);

        $route = $this->context->getRouteInstance();
        $parameters = $this->context->getRouteParameters();
        $method = $route->methods[0];
        $uri = $this->resolveUri($route, $parameters, $payload);

        return match ($method) {
            'GET' => getJson($uri),
            'POST' => postJson($uri, $payload),
            'PATCH' => patchJson($uri, $payload),
            'PUT' => putJson($uri, $payload),
            'DELETE' => deleteJson($uri, $payload),
        };
    }

    /**
     * @param  array<string, string>  $parameters
     * @param  array<array-key, mixed>  $payload
     *
     * @throws SkippedTestSuiteError if query string dynamic part is not stringable
     */
    private function resolveUri(Route $route, array $parameters, array $payload): string
    {
        // Combine parameters + payload if GET (route() automatically adds query string)
        $params = $parameters;
        if ($route->methods[0] === 'GET') {
            $params = array_merge($parameters, $payload);
        }

        return route((string) $route->getName(), $params);
    }
}
