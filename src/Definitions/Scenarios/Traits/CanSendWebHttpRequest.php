<?php

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits;

use Illuminate\Testing\TestResponse;
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
     */
    public function sendRequest(): TestResponse
    {
        $payload = self::resolvePayload($this->payload);

        $method = $this->context->getRouteHttpMethod();
        $uri = $this->context->getRouteUri($payload);

        $fromUri = $this->context->getFromRouteUri();
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
        };
    }
}
