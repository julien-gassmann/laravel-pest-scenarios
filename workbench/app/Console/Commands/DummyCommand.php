<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Workbench\App\Models\User;

class DummyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:command {--user_id=}';

    /**
     * Execute the console command.
     *
     * @throws Throwable // When Route or Class not found
     */
    public function handle(): void
    {
        $id = $this->option('user_id');
        $id ?: $this->info('Command complete.');

        $user = User::query()->find(intval($id));
        $user ?: $this->fail('User not found.');

        $user->update(['name' => 'Dummy Name']);
        $this->info('Command complete.');
    }
}
