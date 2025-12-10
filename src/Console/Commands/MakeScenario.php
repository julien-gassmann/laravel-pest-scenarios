<?php

namespace Jgss\LaravelPestScenarios\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route as RouteFacade;
use Throwable;
use Workbench\App\Providers\WorkbenchServiceProvider;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeScenario extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scenario {type} {name} {--R|route=} {--C|class=} {--A|command=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish structured test file for laravel-pest-scenario.';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Throwable // When Route or Class not found
     */
    public function handle(): void
    {
        $stubPath = $this->getOriginalStubPath();

        if (is_string($stubPath)) {
            $stubContent = $this->replaceOriginalStubContent($stubPath);
            $targetPath = $this->getTargetPath();

            $this->files->put($targetPath, $stubContent);
            $this->files->ensureDirectoryExists(dirname($targetPath));

            $this->info('Scenario test file created successfully.');
        }
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, Closure(): (int|string)>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'type' => fn (): int|string => select(
                label: 'Which type of Scenario should the test file perform ?',
                options: [
                    'ApiRoute',
                    'WebRoute',
                    'Command',
                    'FormRequest',
                    'Model',
                    'Policy',
                    'Rule',
                ],
            ),
            'name' => fn (): string => text(
                label: 'What is your test file name ?',
                required: true,
                hint: 'You can precise the relative path from /tests directory. You can omit .php extension.',
            ),
        ];
    }

    /**
     * Ensures types received exists and return related stub file.
     *
     * @throws Throwable // When type doesn't exist
     */
    protected function getOriginalStubPath(): string|bool
    {
        /** @var string $type */
        $type = $this->argument('type');
        $stubPath = realpath(__DIR__."/../../../stubs/Scenarios/$type.stub");

        if (! is_string($stubPath)) {
            $this->fail("Scenario type '$type' does not exist.");
        }

        return $stubPath;
    }

    /**
     * Replace placeholders in original stub file with
     * arguments and options received.
     *
     * @throws Throwable // When Route not found
     */
    protected function replaceOriginalStubContent(string $stubPath): string
    {
        $stubContent = (string) file_get_contents($stubPath);

        ['class' => $classOption, 'route' => $routeOption, 'command' => $commandOption] = $this->getOptions();

        $routeData = $this->resolveRouteOption($routeOption);
        $commandOption = $this->resolveCommandOption($commandOption);
        $classOption = $this->resolveClassOption($classOption);

        return str_replace(
            [...array_keys($routeData), 'ClassName', 'artisan.command', ' /** @noinspection PhpUndefinedClassInspection */'],
            [...array_values($routeData), $classOption, $commandOption, ''],
            $stubContent
        );
    }

    /**
     * Build path to store file and ensure it has .php extension.
     *
     * @throws Throwable // When file name contains invalid characters
     */
    protected function getTargetPath(): string
    {
        /** @var string $fileName */
        $fileName = $this->argument('name');

        if (! preg_match('/^[A-Za-z0-9\/]+$/', $fileName)) {
            $this->fail("Invalid file name: only letters, numbers and '/' are allowed.");
        }

        // Ensure the filename ends with .php
        if (! str_ends_with($fileName, '.php')) {
            $fileName .= '.php';
        }

        return base_path("tests/$fileName");
    }

    // ------------------------------ Option getters ------------------------------

    /**
     * @return array{class: null|string, route: null|string, command: null|string}
     */
    protected function getOptions(): array
    {
        return match ($this->argument('type')) {
            'ApiRoute', 'WebRoute' => $this->getRouteOptions(),
            'Command' => $this->getCommandOptions(),
            'FormRequest' => $this->getFormRequestOptions(),
            'Model' => $this->getClassOnlyOptions('Model'),
            'Policy' => $this->getClassOnlyOptions('Policy'),
            'Rule' => $this->getClassOnlyOptions('Rule'),
            default => ['class' => null, 'route' => null, 'command' => null],
        };
    }

    /**
     * @return array{class: null, route: string, command: null}
     */
    protected function getRouteOptions(): array
    {
        /** @var string $routeOption */
        $routeOption = $this->option('route')
            ?? $this->ask('Which route do you want to test ?');

        return [
            'class' => null,
            'route' => $routeOption,
            'command' => null,
        ];
    }

    /**
     * @return array{class: null, route: null, command: string}
     */
    protected function getCommandOptions(): array
    {
        /** @var string $commandOptions */
        $commandOptions = $this->option('command')
            ?? $this->ask('Which artisan command do you want to test ?');

        return [
            'class' => null,
            'route' => null,
            'command' => $commandOptions,
        ];
    }

    /**
     * @return array{class: string, route: string, command: null}
     */
    protected function getFormRequestOptions(): array
    {
        /** @var string $classOption */
        $classOption = $this->option('class')
            ?? $this->ask('Which FormRequest class do you want to test ?');

        /** @var string $routeOption */
        $routeOption = $this->option('route')
            ?? $this->ask('For which route do you want to test it ?');

        return [
            'class' => $classOption,
            'route' => $routeOption,
            'command' => null,
        ];
    }

    /**
     * @return array{class: string, route: null, command: null}
     */
    protected function getClassOnlyOptions(string $type): array
    {
        /** @var string $classOption */
        $classOption = $this->option('class')
            ?? $this->ask("Which $type class do you want to test ?");

        return [
            'class' => $classOption,
            'route' => null,
            'command' => null,
        ];
    }

    // ------------------------------ Option resolvers ------------------------------

    /**
     * @return array<string, string>
     *
     * @throws Throwable // When Route not found
     */
    protected function resolveRouteOption(?string $routeOption): array
    {
        if (in_array($routeOption, [null, '', '0'], true)) {
            return [];
        }

        // Resolves the route instance using its name.
        $route = RouteFacade::getRoutes()->getByName($routeOption);
        $route ?: $this->fail("Unable to find route: '$routeOption'.");

        /** @var Route $route */
        /** @var string[] $routeMethods */
        $routeMethods = $route->methods();

        return [
            'route.name' => (string) $route->getName(),
            'METHOD' => $routeMethods[0],
            '/route/uri' => $route->uri(),
        ];
    }

    /**
     * @throws Throwable // When artisan command not found
     */
    protected function resolveCommandOption(?string $commandOption): string
    {
        if (in_array($commandOption, [null, '', '0'], true)) {
            return 'artisan:command';
        }

        $commandName = trim($commandOption);
        $commandExists = array_key_exists($commandOption, Artisan::all());

        if (! $commandExists) {
            $this->fail("Unable to find artisan command: '$commandName'.");
        }

        return $commandName;
    }

    /**
     * @throws Throwable // When class not found
     */
    protected function resolveClassOption(?string $classOption): string
    {
        if (in_array($classOption, [null, '', '0'], true)) {
            return 'ClassName';
        }

        /** @var string $type */
        $type = $this->argument('type');
        $className = trim($classOption);
        $rootNamespace = app()->getNamespace();

        $FQCN = match ($type) {
            'FormRequest' => "{$rootNamespace}Http\\Requests\\$className",
            'Model' => "{$rootNamespace}Models\\$className",
            'Policy' => "{$rootNamespace}Policies\\$className",
            'Rule' => "{$rootNamespace}Rules\\$className",
        };

        if (app()->providerIsLoaded(WorkbenchServiceProvider::class)) {
            $FQCN = "Workbench\\$FQCN";
        }

        if (! class_exists($FQCN)) {
            $this->fail("Unable to find $type class for: '$className'.");
        }

        return $className;
    }
}
