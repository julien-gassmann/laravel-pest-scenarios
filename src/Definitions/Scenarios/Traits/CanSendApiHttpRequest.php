<?php

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits;

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

        $uri = $this->context->getRouteUri($payload);
        $method = $this->context->getRouteHttpMethod();

        return match ($method) {
            'GET' => getJson($uri),
            'POST' => postJson($uri, $payload),
            'PATCH' => patchJson($uri, $payload),
            'PUT' => putJson($uri, $payload),
            'DELETE' => deleteJson($uri, $payload),
        };
    }
}
