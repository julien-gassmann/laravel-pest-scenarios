<?php

namespace Jgss\LaravelPestScenarios\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Throwable;

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
        $stubContent = $this->replaceOriginalStubContent($stubPath);
        $targetPath = $this->getTargetPath();

        $this->files->put($targetPath, $stubContent);
        $this->files->ensureDirectoryExists(dirname($targetPath));

        $this->info('Scenario test file created successfully.');
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, Closure(): (int|string)>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'type' => fn () => select(
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
            'name' => fn () => text(
                label: 'What is your test file name ?',
                required: true,
                hint: 'You can precise the relative path from /tests directory. You can omit .php extension.',
            ),
        ];
    }

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
            ?? $this->ask('Which artisan command do you want to test ?')
            ?? 'artisan.command';

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
            ?? $this->ask('Which FormRequest class do you want to test ?')
            ?: 'ClassName';

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
            ?? $this->ask("Which $type class do you want to test ?")
            ?: 'ClassName';

        return [
            'class' => $classOption,
            'route' => null,
            'command' => null,
        ];
    }

    /**
     * Ensures types received exists and return related stub file.
     *
     * @throws Throwable // When type doesn't exist
     */
    protected function getOriginalStubPath(): string
    {
        /** @var string $type */
        $type = $this->argument('type');
        $stubPath = base_path("stubs/Scenarios/$type.stub");

        if (! file_exists($stubPath)) {
            $this->fail("Scenario type $type does not exist.");
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
        $routeData = [];

        ['class' => $classOption, 'route' => $routeOption, 'command' => $commandOption] = $this->getOptions();

        if ($routeOption) {
            // Resolves the route instance using its name.
            $route = Route::getRoutes()->getByName($routeOption)
                ?? $this->fail("Unable to find route: '$routeOption'.");

            /** @var string[] $routeMethods */
            $routeMethods = $route->methods();

            $routeData = [
                'route.name' => (string) $route->getName(),
                'METHOD' => $routeMethods[0],
                '/route/uri' => $route->uri(),
            ];
        }

        return str_replace(
            [...array_keys($routeData), 'ClassName', 'artisan.command', ' /** @noinspection PhpUndefinedClassInspection */'],
            [...array_values($routeData), $classOption ?? '', $commandOption ?? '', ''],
            $stubContent
        );
    }

    /**
     * Build path to store file and ensure it has .php extension.
     */
    protected function getTargetPath(): string
    {
        /** @var string $fileName */
        $fileName = $this->argument('name');

        // Ensure the filename ends with .php
        if (! str_ends_with($fileName, '.php')) {
            $fileName .= '.php';
        }

        return base_path("tests/$fileName");
    }
}
