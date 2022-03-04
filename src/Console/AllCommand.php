<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;

class AllCommand extends Command
{
    /** @var string */
    protected $signature = 'next-ide-helper:all';

    /** @var string */
    protected $description = 'Runs all next-ide-helper commands';

    public function handle(Kernel $console): void
    {
        $commands = $this->resolveCommands();

        foreach ($commands as $command) {
            $this->info('Running ' . app()->make($command)->getName());
            $console->call($command, [], $this->output);
        }
    }

    /**
     * @return array<class-string<\Illuminate\Console\Command>>
     */
    private function resolveCommands(): array
    {
        $commands = [
            AliasesCommand::class,
            MacrosCommand::class,
            MetaCommand::class,
            ModelsCommand::class,
        ];

        if (class_exists(\Illuminate\Database\Eloquent\Factories\Factory::class)) {
            $commands[] = FactoriesCommand::class;
        }

        return $commands;
    }
}
