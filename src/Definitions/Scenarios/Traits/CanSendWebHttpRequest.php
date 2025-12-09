<?php

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits;

use Illuminate\Routing\Route;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\SkippedTestSuiteError;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\from;

trait CanSendWebHttpRequest
{
    /**
     * Sends an HTTP request to the scenario’s target route, automatically resolving the HTTP method and
     * applying route parameters. The request is executed from the configured "from" route (target route otherwise),
     * which allows full redirect‑aware workflows to be tested. If `shouldFollowRedirect` is enabled,
     * redirects are followed before returning the final response.
     *
     * @return TestResponse<Response>
     *
     * @throws SkippedTestSuiteError if unable to find route
     */
    public function sendRequest(): TestResponse
    {
        $payload = self::resolvePayload($this->payload);

        $route = $this->context->getRouteInstance();
        $parameters = $this->context->getRouteParameters();
        $method = $route->methods[0];
        $uri = $this->resolveUri($route, $parameters, $payload);

        $fromRoute = $this->context->getFromRouteInstance();
        $fromParameters = $this->context->getFromRouteParameters();
        $fromUri = $this->resolveUri($fromRoute, $fromParameters, []);
        $from = from($fromUri);

        if ($this->shouldFollowRedirect ?? false) {
            $from = $from->followingRedirects();
        }

        return match ($method) {
            'GET' => $from->get($uri),
            'POST' => $from->post($uri, $payload),
            'PATCH' => $from->patch($uri, $payload),
            'PUT' => $from->put($uri, $payload),
            'DELETE' => $from->delete($uri, $payload),
            default => throw new SkippedTestSuiteError("Unable to resolve HTTP method for route: '{$this->context->getRouteName()}'."),
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
