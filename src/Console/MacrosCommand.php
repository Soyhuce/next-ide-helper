<?php

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Domain\Macros\Actions\FindMacroableClasses;
use Soyhuce\NextIdeHelper\Domain\Macros\Output\MacrosHelperFile;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

class MacrosCommand extends Command
{
    use BootstrapsApplication;

    /** @var string */
    protected $signature = 'next-ide-helper:macros';

    /** @var string */
    protected $description = 'Generate an IDE helper file for Laravel macros';

    public function handle(FindMacroableClasses $findMacroables): void
    {
        $this->bootstrapApplication();

        $macroables = new Collection();
        foreach (config('next-ide-helper.macros.directories') as $directory) {
            $macroables = $macroables->merge($findMacroables->execute($directory));
        }

        $ideHelperFile = new IdeHelperFile(config('next-ide-helper.macros.file_name'));

        foreach ($macroables as $macroable) {
            $amender = new MacrosHelperFile(new ReflectionClass($macroable));
            $amender->amend($ideHelperFile);
        }

        $ideHelperFile->render();
    }
}
