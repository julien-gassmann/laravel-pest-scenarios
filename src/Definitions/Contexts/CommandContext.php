<?php

namespace Jgss\LaravelPestScenarios\Definitions\Contexts;

use Closure;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasAppLocaleContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasCommandContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasDatabaseSetupContext;
use Jgss\LaravelPestScenarios\Definitions\Contexts\Traits\HasMockingContext;
use Mockery\MockInterface;

/**
 * Immutable definition of a console command context used in scenario-based tests.
 * Encapsulates the artisan command name, optional application locale, and mocked dependencies used during execution.
 *
 * @property string $command Specifies the artisan command used for the test.
 * @property null|string $appLocale Specifies the app localisation used for the test.
 * @property Closure(): void $databaseSetup Returns the database insertions to perform before the test
 * @property array<class-string, Closure(): MockInterface> $mocks Provides classes mocked during the scenario (e.g. Filesystem::class => fn () => Mockery::mock(Filesystem::class))
 */
final readonly class CommandContext
{
    use HasAppLocaleContext;
    use HasCommandContext;
    use HasDatabaseSetupContext;
    use HasMockingContext;

    /**
     * @param  Closure(): void  $databaseSetup
     * @param  array<class-string, Closure(): MockInterface>  $mocks
     */
    public function __construct(
        protected string $command,
        protected ?string $appLocale,
        protected Closure $databaseSetup,
        protected array $mocks,
    ) {}

    /**
     * @param  null|Closure(): void  $databaseSetup
     * @param  null|array<class-string, Closure(): MockInterface>  $mocks
     */
    private function replicate(
        ?string $command = null,
        ?string $appLocale = null,
        ?Closure $databaseSetup = null,
        ?array $mocks = null,
    ): self {
        return new self(
            command: $command ?? $this->command,
            appLocale: $appLocale ?? $this->appLocale,
            databaseSetup: $databaseSetup ?? $this->databaseSetup,
            mocks: $mocks ?? $this->mocks,
        );
    }

    public function actAs(): void
    {
        // do nothing (for compatibility with PrepareContext trait)
    }
}
