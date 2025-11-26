<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts\Traits;

trait HasPayloadContext
{
    // ------------------- With methods -------------------

    /**
     * @param  array<string, mixed>  $payload
     */
    public function withPayload(array $payload): self
    {
        return $this->replicate(payload: $payload);
    }
}
