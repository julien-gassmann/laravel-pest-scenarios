<?php

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\SkippedTestSuiteError;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

trait CanSendHttpRequest
{
    /**
     * Sends an HTTP request to the route defined in the scenario using the appropriate method.
     * The method (GET, POST, PATCH, DELETE) is automatically determined from the route definition.
     * It also handles provided route parameters.
     *
     * @return TestResponse<Response>
     *
     * @throws SkippedTestSuiteError if unable to find route
     */
    public function sendRequest(): TestResponse
    {
        $payload = self::resolvePayload($this->payload);
        $route = $this->context->getRouteInstance();
        $method = $route->methods[0];
        $uri = $route->uri;
        $this->resolveUri($uri, $method, $payload);

        return match ($method) {
            'GET' => get($uri),
            'POST' => post($uri, $payload),
            'PUT' => patch($uri, $payload),
            'DELETE' => delete($uri, $payload),
            default => throw new SkippedTestSuiteError("Unable to resolve HTTP method for route: '{$this->context->getRouteName()}'."),
        };
    }

    /**
     * @param  array<array-key, mixed>  $payload
     *
     * @throws SkippedTestSuiteError if query string dynamic part is not stringable
     */
    private function resolveUri(string &$uri, mixed $method, array $payload): void
    {
        // If route contains parameters, replace '{param}' placeholder with tested parameter value
        foreach ($this->context->getRouteParameters() as $param => $value) {
            $uri = preg_replace('/\{'.$param.'(\??)}/', $value, $uri) ?? $uri;
        }

        // If route method is GET, convert payload to query string
        if ($method === 'GET' && ! empty($payload)) {
            $uri .= '?'.http_build_query($payload);
        }
    }
}
