<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Illuminate\Contracts\Auth\Authenticatable as User;

use function Pest\Laravel\actingAs;

final readonly class ActingAsResolver
{
    /**
     * @param  callable(): ?User  $actingAs
     */
    public static function resolve(callable $actingAs): void
    {
        $actor = ($actingAs)();

        if ($actor) {
            actingAs($actor);
        }
    }
}
