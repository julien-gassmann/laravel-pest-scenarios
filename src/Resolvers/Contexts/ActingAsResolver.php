<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Contexts;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as User;

use function Jgss\LaravelPestScenarios\getActor;
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

    /**
     * @param  null|string|Closure(): ?User  $actingAs
     * @return Closure(): ?User
     */
    public static function resolveInitialContext(null|string|Closure $actingAs): Closure
    {
        return match (true) {
            is_null($actingAs) => fn (): null => null,
            is_string($actingAs) => getActor($actingAs),
            is_callable($actingAs) => $actingAs,
        };
    }
}
