<?php

namespace Jgss\LaravelPestScenarios\Resolvers\Config;

use Illuminate\Contracts\Auth\Authenticatable as User;

/**
 * Resolves pre-defined actors from configuration for test assertions.
 *
 * @extends BaseConfigResolver<User>
 */
final class ActorResolver extends BaseConfigResolver
{
    protected static string $key = 'actors';

    public static function getId(string $actorName): ?int
    {
        /** @var null|(User&object{id:int}) $actor */
        $actor = self::get($actorName);

        return $actor?->id;
    }
}
