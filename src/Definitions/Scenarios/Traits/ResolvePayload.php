<?php

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits;

trait ResolvePayload
{
    /**
     * Resolves recursively the payload values passed has callable.
     *
     * @param  array<array-key, mixed>  $payload
     * @return array<string, mixed>
     */
    private static function resolvePayload(array $payload): array
    {
        /** @var array<string, mixed>  $resolvedPayload */
        $resolvedPayload = array_map(
            fn (mixed $value): mixed => match (true) {
                is_array($value) => self::resolvePayload($value),
                is_callable($value) && (is_scalar($value()) || is_array($value())) => $value(),
                default => $value,
            },
            $payload
        );

        return $resolvedPayload;
    }
}
