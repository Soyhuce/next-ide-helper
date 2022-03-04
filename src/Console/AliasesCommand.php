<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Soyhuce\NextIdeHelper\Domain\Aliases\ResolveAliases;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

class AliasesCommand extends Command
{
    use BootstrapsApplication;

    /** @var string */
    protected $signature = 'next-ide-helper:aliases';

    /** @var string */
    protected $description = 'Generates a file to help your IDE understand Laravel Aliases';

    public function handle(ResolveAliases $resolveAliases): void
    {
        $this->bootstrapApplication();

        $aliases = $resolveAliases->execute();

        $ideHelperFile = new IdeHelperFile(config('next-ide-helper.aliases.file_name'));

        foreach ($aliases as $alias => $concrete) {
            $ideHelperFile->getOrAddClass($alias)->extends($concrete);
        }

        $ideHelperFile->render();
    }
}
